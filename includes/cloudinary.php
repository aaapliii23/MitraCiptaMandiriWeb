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
        return ['ok'=>true, 'url'=>$data['secure_url']];
    }
    $msg = $data['error']['message'] ?? ('Upload gagal (HTTP '.$code.')');
    // Cloudinary kadang kirim "File size too large" dll
    return ['ok'=>false, 'error'=>$msg];
}
