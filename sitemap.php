<?php
// sitemap.php — generator sitemap.xml dinamis untuk lpkmcm.com
// Diakses via https://lpkmcm.com/sitemap.xml (rewrite di .htaccess) atau langsung /sitemap.php.
// Standalone: tanpa session/header/footer agar output selalu XML murni.
// Bagian statis = halaman publik. Bagian dinamis = 1 URL per kelas dari tabel `classes`.

// Jangan bocorkan error PHP ke output XML; error tetap masuk error_log.
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Base URL kanonis produksi; di luar domain produksi pakai host aktif (untuk cek lokal).
$host = strtolower(trim($_SERVER['HTTP_HOST'] ?? ''));
if ($host === 'lpkmcm.com' || $host === 'www.lpkmcm.com') {
    $base = 'https://lpkmcm.com';
} elseif ($host !== '') {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $base = $scheme . '://' . $host;
} else {
    $base = 'https://lpkmcm.com'; // fallback CLI/cron
}

$today = date('Y-m-d');

// [path, changefreq, priority, lastmod-timestamp]
// lastmod statis diambil dari filemtime agar selalu akurat tanpa perlu diedit manual.
$urls = [
    ['/', 'weekly', '1.0', filemtime(__DIR__ . '/index.php')],
    ['/pages/programs.php', 'weekly', '0.9', filemtime(__DIR__ . '/pages/programs.php')],
    ['/pages/testimoni.php', 'weekly', '0.7', filemtime(__DIR__ . '/pages/testimoni.php')],
    ['/pages/about.php', 'monthly', '0.7', filemtime(__DIR__ . '/pages/about.php')],
    ['/pages/examiners.php', 'monthly', '0.6', filemtime(__DIR__ . '/pages/examiners.php')],
    ['/pages/certification.php', 'monthly', '0.6', filemtime(__DIR__ . '/pages/certification.php')],
    ['/pages/kebijakan_privasi.php', 'yearly', '0.3', filemtime(__DIR__ . '/pages/kebijakan_privasi.php')],
    ['/pages/syarat_layanan.php', 'yearly', '0.3', filemtime(__DIR__ . '/pages/syarat_layanan.php')],
    // Sengaja TIDAK dimasukkan: /admin*, /admin_login.php, /lms/*, /payment/*,
    // /auth/*, /chat/*, /agent/*, /legacy/*, /tools/*, verify_certificate.php
    // & verify_cert.php (butuh token/nomor sertifikat per URL, tidak layak diindeks).
];

// Dinamis: satu URL per kelas. DB gagal/tidak ada → lewati diam-diam, sitemap statis tetap valid.
try {
    if (file_exists(__DIR__ . '/config/secrets.php')) require_once __DIR__ . '/config/secrets.php';
    $dbHost = defined('DB_HOST') ? DB_HOST : (getenv('DB_HOST') ?: 'localhost');
    $dbName = defined('DB_NAME') ? DB_NAME : (getenv('DB_NAME') ?: 'mcm_db');
    $dbUser = defined('DB_USER') ? DB_USER : (getenv('DB_USER') ?: 'root');
    $dbPass = defined('DB_PASS') ? DB_PASS : (getenv('DB_PASS') ?: 'password');
    $pdo = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
    $rows = $pdo->query("SELECT id, created_at FROM classes ORDER BY id ASC")->fetchAll();
    foreach ($rows as $r) {
        $ts = strtotime($r['created_at'] ?? '');
        $urls[] = ['/pages/class_detail.php?id=' . (int)$r['id'], 'weekly', '0.8', $ts === false ? time() : $ts];
    }
} catch (Throwable $e) {
    error_log('sitemap.php: DB tidak tersedia, pakai URL statis saja. ' . $e->getMessage());
}

header('Content-Type: application/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as [$path, $freq, $prio, $ts]) {
    $lastmod = (is_int($ts) && $ts > 0) ? date('Y-m-d', $ts) : $today;
    echo '  <url>'
        . '<loc>' . htmlspecialchars($base . $path, ENT_XML1, 'UTF-8') . '</loc>'
        . '<lastmod>' . $lastmod . '</lastmod>'
        . '<changefreq>' . $freq . '</changefreq>'
        . '<priority>' . $prio . '</priority>'
        . '</url>' . "\n";
}
echo '</urlset>' . "\n";
