<?php
// --- 404 publik: tangkap URL/file tidak ada yang fallback ke index.php (php -S tanpa router) ---
// Catatan: pakai DOCUMENT_ROOT agar benar untuk 2 mode deploy:
// - docroot = repo root (/index.php) dan
// - docroot = parent htdocs (/MitraCiptaMandiriWeb/index.php via XAMPP/subfolder)
$__reqPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$__reqPath = urldecode($__reqPath);
// normalisasi: hilangkan trailing slash kecuali root
$__reqPathNorm = rtrim($__reqPath, '/');
if ($__reqPathNorm === '') $__reqPathNorm = '/';
if ($__reqPathNorm !== '/' && $__reqPathNorm !== '/index.php' && $__reqPathNorm !== '/404.php' && $__reqPathNorm !== '/router.php') {
    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? __DIR__;
    // untuk php -S tanpa DOCUMENT_ROOT yang akurat, fallback ke __DIR__ jika path tidak diawali subfolder
    $__real = rtrim($docRoot, '/') . $__reqPath;
    // jika docRoot tidak mengandung file (kasus subfolder), coba __DIR__ + basename
    if (!file_exists($__real) && !str_starts_with($__reqPath, '/MitraCiptaMandiriWeb')) {
        // coba cek relatif terhadap repo (untuk docroot = repo)
        $__alt = __DIR__ . $__reqPath;
        if (file_exists($__alt)) $__real = $__alt;
    }
    $isAsset = str_contains($__reqPath, '/assets/') || str_contains($__reqPath, '/uploads/');
    // hanya 404 jika file benar-benar tidak ada DAN bukan asset yang memang tidak ada (biarkan 404 untuk asset juga, tapi jangan false-positive untuk file valid)
    if (!$isAsset && !file_exists($__real) && !is_dir($__real)) {
        // pastikan bukan halaman valid tanpa file fisik (misal /index.php?page=xxx sudah ditangani di bawah)
        if (!isset($_GET['page'])) {
            http_response_code(404);
            if (file_exists(__DIR__ . '/404.php')) {
                include __DIR__ . '/404.php';
                exit;
            }
        }
    }
}
unset($__reqPath, $__reqPathNorm, $__real, $__alt, $docRoot, $isAsset);

session_start();
// Generate CSRF Token for the form
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

require_once 'config/database.php';

// Tangani ?page= yang tidak dikenal di publik (index hanya homepage, tidak ada router ?page)
if (isset($_GET['page'])) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    exit;
}

// Fetch Gallery
try {
    $stmt = $pdo->query("SELECT * FROM gallery ORDER BY id ASC");
    $galleryItems = $stmt->fetchAll();
} catch(PDOException $e) { $galleryItems = []; }

// Fetch Classes
try {
    $stmt = $pdo->query("SELECT * FROM classes ORDER BY id ASC");
    $classItems = $stmt->fetchAll();
} catch(PDOException $e) { $classItems = []; }

// Prepare Class Details for JS (termasuk mode Online/Offline)
$jsClassData = [];
foreach($classItems as $c) {
    $pLegacy = (int)($c['price'] ?? 0);
    $pOn = isset($c['price_online']) && (int)$c['price_online'] > 0 ? (int)$c['price_online'] : (int)round($pLegacy * 0.8);
    $pOff = isset($c['price_offline']) && (int)$c['price_offline'] > 0 ? (int)$c['price_offline'] : $pLegacy;
    $jsClassData[$c['id']] = [
        'id' => $c['id'],
        'name' => $c['name'],
        'category' => $c['category'],
        'description' => $c['description'],
        'description_online' => $c['description_online'] ?? $c['description'],
        'description_offline' => $c['description_offline'] ?? $c['description'],
        'price' => $pLegacy,
        'price_online' => $pOn,
        'price_offline' => $pOff,
        'mode_available' => $c['mode_available'] ?? 'both',
        'image' => $c['image'],
        'features' => json_decode($c['features'], true) ?: []
    ];
}
?>
<?php include 'includes/header.php'; ?>
<script>window.mcmClassDetails = <?php echo json_encode($jsClassData); ?>;</script>

    <?php include 'pages/partials/section_beranda.php'; ?>
    <?php include 'pages/partials/section_tentang.php'; ?>
    <?php include 'pages/partials/section_galeri.php'; ?>
    <?php include 'pages/partials/section_paket.php'; ?>
    <?php include 'pages/partials/section_testimoni.php'; ?>
    <?php include 'pages/partials/modal_detail.php'; ?>
    <?php include 'pages/partials/modal_checkout.php'; ?>

    <?php include 'includes/footer.php'; ?>

    <?php include 'pages/partials/index_scripts.php'; ?>
</body>
</html>
