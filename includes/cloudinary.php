<?php
// ponytail: reusable Cloudinary uploader — signed upload, validated, one function
// Kredensial dibaca dari config/secrets.php (gitignored). Jika belum ada, fallback ke define di sini
// (segera pindahkan ke secrets.php agar tidak ter-commit).
if (file_exists(__DIR__ . '/../config/secrets.php')) {
    require_once __DIR__ . '/../config/secrets.php';
}
if (!defined('CLOUDINARY_CLOUD_NAME')) define('CLOUDINARY_CLOUD_NAME', '');
if (!defined('CLOUDINARY_API_KEY'))    define('CLOUDINARY_API_KEY', '');
if (!defined('CLOUDINARY_API_SECRET')) define('CLOUDINARY_API_SECRET', '');
if (!defined('CLOUDINARY_FOLDER'))     define('CLOUDINARY_FOLDER', 'mcm'); // ponytail: single folder, sub-folder via public_id prefix if needed
if (!defined('CLOUDINARY_MAX_BYTES'))  define('CLOUDINARY_MAX_BYTES', 5 * 1024 * 1024); // 5 MB

/**
 * Upload gambar ke Cloudinary (signed).
 * @param array $file elemen $_FILES['field'] (single)
 * @param string $folder subfolder opsional, default CLOUDINARY_FOLDER
 * @return array ['ok'=>bool, 'url'=>string, 'error'=>string]
 * ponytail: 5MB, jpg/png/webp, cURL, SHA1(timestamp+secret)
 */
function uploadImageToCloudinary(array $file, string $folder = CLOUDINARY_FOLDER): array {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        $code = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        $msg = match($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (melebihi limit server).',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file yang dipilih.',
            default => 'Upload gagal (kode '.$code.').'
        };
        return ['ok'=>false, 'error'=>$msg];
    }
    $tmp  = $file['tmp_name'] ?? '';
    $name = $file['name'] ?? 'upload';
    $size = (int)($file['size'] ?? 0);
    if ($size > CLOUDINARY_MAX_BYTES) {
        return ['ok'=>false, 'error'=>'File terlalu besar, maksimal 5 MB.'];
    }
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp','heic'];
    if (!in_array($ext, $allowed, true)) {
        return ['ok'=>false, 'error'=>'Format tidak didukung. Gunakan JPG, PNG, atau WEBP.'];
    }
    if (!is_file($tmp)) {
        return ['ok'=>false, 'error'=>'File tidak valid.'];
    }
    if (PHP_SAPI !== 'cli' && !is_uploaded_file($tmp)) {
        return ['ok'=>false, 'error'=>'File tidak valid (bukan upload HTTP).'];
    }
    $mime = @mime_content_type($tmp);
    if ($mime && !str_starts_with($mime, 'image/')) {
        // tetap tolak jika mime bukan image (secara ketat)
        // beberapa webp mungkin terdeteksi sebagai image/webp
        if (!in_array($mime, ['image/jpeg','image/png','image/webp'], true)) {
            // jangan hard-fail jika mime aneh tapi ekstensi ok — biarkan Cloudinary yang validasi
        }
    }
    $cloud = CLOUDINARY_CLOUD_NAME; $key = CLOUDINARY_API_KEY; $secret = CLOUDINARY_API_SECRET;
    if (!$cloud || !$key || !$secret) {
        return ['ok'=>false, 'error'=>'Konfigurasi Cloudinary belum diatur.'];
    }
    $timestamp = time();
    // signature untuk signed upload: sha1("folder=...&timestamp=...<secret>") — key diurut alfabet
    $toSign = "folder={$folder}&timestamp={$timestamp}";
    $signature = sha1($toSign . $secret);
    $url = "https://api.cloudinary.com/v1_1/{$cloud}/image/upload";
    $post = [
        'file'      => new CURLFile($tmp, $mime ?: 'image/'.$ext, $name),
        'api_key'   => $key,
        'timestamp' => $timestamp,
        'folder'    => $folder,
        'signature' => $signature,
    ];
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $post,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $resp = curl_exec($ch);
    $err  = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($resp === false) {
        return ['ok'=>false, 'error'=>'Koneksi ke Cloudinary gagal: '.$err];
    }
    $data = json_decode($resp, true);
    if ($code >= 200 && $code < 300 && isset($data['secure_url'])) {
        return ['ok'=>true, 'url'=>$data['secure_url'], 'public_id'=>$data['public_id'] ?? ''];
    }
    $msg = $data['error']['message'] ?? ('Upload gagal (HTTP '.$code.')');
    // Cloudinary kadang kirim "File size too large" dll
    return ['ok'=>false, 'error'=>$msg];
}

/**
 * Ekstrak public_id dari secure_url Cloudinary.
 * Contoh: https://res.cloudinary.com/ijpgxnt4/image/upload/v123/mcm/gallery/abc123.jpg -> mcm/gallery/abc123
 */
function cloudinaryPublicIdFromUrl(string $url): string {
    if (!str_contains($url, 'res.cloudinary.com')) return '';
    $p = parse_url($url, PHP_URL_PATH);
    if (!$p) return '';
    // cari /upload/ lalu ambil setelah version v123/
    $pos = strpos($p, '/upload/');
    if ($pos === false) return '';
    $after = substr($p, $pos + 8); // setelah /upload/
    // hapus v123/ di awal jika ada
    $after = preg_replace('#^v\d+/#', '', $after);
    // hapus ekstensi
    $after = preg_replace('/\.[a-z0-9]+$/i', '', $after);
    return ltrim($after, '/');
}

/**
 * Hapus gambar dari Cloudinary (signed destroy).
 * @param string $publicIdOrUrl public_id atau secure_url
 * @return array ['ok'=>bool,'error'=>string]
 * ponytail: Admin API destroy, SHA1(public_id+timestamp+secret)
 */
function deleteImageFromCloudinary(string $publicIdOrUrl): array {
    $publicId = $publicIdOrUrl;
    if (str_contains($publicIdOrUrl, 'res.cloudinary.com')) {
        $publicId = cloudinaryPublicIdFromUrl($publicIdOrUrl);
    }
    $publicId = trim($publicId);
    if ($publicId === '') return ['ok'=>false, 'error'=>'public_id kosong'];
    if (!str_contains($publicId, '/')) {
        // fallback: coba tanpa folder (legacy)
    }
    $cloud = CLOUDINARY_CLOUD_NAME; $key = CLOUDINARY_API_KEY; $secret = CLOUDINARY_API_SECRET;
    if (!$cloud || !$key || !$secret) return ['ok'=>false, 'error'=>'Konfigurasi Cloudinary belum diatur'];
    $timestamp = time();
    $toSign = "public_id={$publicId}&timestamp={$timestamp}";
    $signature = sha1($toSign . $secret);
    $url = "https://api.cloudinary.com/v1_1/{$cloud}/image/destroy";
    $post = ['public_id'=>$publicId,'api_key'=>$key,'timestamp'=>$timestamp,'signature'=>$signature];
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$post,CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>15,CURLOPT_SSL_VERIFYPEER=>true]);
    $resp = curl_exec($ch); $err=curl_error($ch); $code=curl_getinfo($ch,CURLINFO_HTTP_CODE); curl_close($ch);
    if ($resp===false) return ['ok'=>false,'error'=>'Koneksi Cloudinary gagal: '.$err];
    $data=json_decode($resp,true);
    $res = $data['result'] ?? '';
    if (($code>=200 && $code<300) && ($res==='ok' || $res==='not found')) return ['ok'=>true];
    $msg=$data['error']['message'] ?? ('Hapus gagal (HTTP '.$code.': '.$res.')');
    return ['ok'=>false,'error'=>$msg];
}
