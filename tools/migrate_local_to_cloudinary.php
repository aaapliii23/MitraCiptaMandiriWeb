<?php
// Batch migrasi foto lokal (uploads/*) ke Cloudinary — simpan secure_url, file lokal tetap sebagai backup
// Jalankan via CLI: php tools/migrate_local_to_cloudinary.php [--dry]
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/cloudinary.php';

$dry = in_array('--dry', $argv ?? []);
$map = [
    ['table'=>'gallery', 'col'=>'image', 'id'=>'id', 'folder'=>'mcm/gallery'],
    ['table'=>'classes', 'col'=>'image', 'id'=>'id', 'folder'=>'mcm/classes'],
    ['table'=>'instructors', 'col'=>'image', 'id'=>'id', 'folder'=>'mcm/instructors'],
    ['table'=>'certifications', 'col'=>'image', 'id'=>'id', 'folder'=>'mcm/certs'],
    ['table'=>'certificate_templates', 'col'=>'bg_image', 'id'=>'id', 'folder'=>'mcm/certs'],
    ['table'=>'facility_locations', 'col'=>'image', 'id'=>'id', 'folder'=>'mcm/facilities'],
    ['table'=>'finance_transactions', 'col'=>'receipt_image', 'id'=>'id', 'folder'=>'mcm/finance'],
    ['table'=>'testimonials', 'col'=>'image', 'id'=>'id', 'folder'=>'mcm/testimonials'],
];
$total=0; $ok=0; $skip=0; $fail=0;
foreach ($map as $m) {
    $table=$m['table']; $col=$m['col']; $idcol=$m['id']; $folder=$m['folder'];
    try { $pdo->query("SELECT 1 FROM `$table` LIMIT 1"); } catch(PDOException $e){ echo "SKIP $table (tidak ada)\n"; continue; }
    $rows = $pdo->query("SELECT `$idcol`, `$col` FROM `$table` WHERE `$col` IS NOT NULL AND `$col`<>''")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        $total++; $id=$r[$idcol]; $path=$r[$col];
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) { $skip++; continue; }
        if (str_starts_with($path, 'assets/')) { $skip++; continue; }
        if (!str_starts_with($path, 'uploads/')) { $skip++; continue; }
        $full = __DIR__ . '/../' . $path;
        if (!is_file($full)) { echo "[$table:$id] SKIP file hilang: $path\n"; $skip++; continue; }
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp','heic'], true)) { $skip++; continue; }
        if ($dry) { echo "[DRY][$table:$id] $path -> $folder\n"; $ok++; continue; }
        $file = ['name'=>basename($path),'type'=>mime_content_type($full)?:'image/'.$ext,'tmp_name'=>$full,'error'=>0,'size'=>filesize($full)];
        $res = uploadImageToCloudinary($file, $folder);
        if (!$res['ok']) { echo "[$table:$id] FAIL $path : ".$res['error']."\n"; $fail++; continue; }
        $url = $res['url'];
        $stmt = $pdo->prepare("UPDATE `$table` SET `$col`=? WHERE `$idcol`=?");
        $stmt->execute([$url, $id]);
        echo "[$table:$id] OK $path -> $url\n";
        $ok++;
        // jangan unlink, backup lokal tetap
        usleep(200000); // hindari rate-limit
    }
}
echo "Selesai: total=$total ok=$ok skip=$skip fail=$fail".($dry?" (DRY)":"")."\n";
