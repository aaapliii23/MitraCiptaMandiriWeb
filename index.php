<?php
session_start();
// Generate CSRF Token for the form
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

require_once 'config/database.php';

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

// Prepare Class Details for JS
$jsClassData = [];
foreach($classItems as $c) {
    $offPrice = (!empty($c['price_offline']) && (int)$c['price_offline'] > 0) ? (int)$c['price_offline'] : (int)$c['price'];
    $onPrice = (!empty($c['price_online']) && (int)$c['price_online'] > 0) ? (int)$c['price_online'] : (int)round($offPrice * 0.75);
    $jsClassData[$c['id']] = [
        'id' => $c['id'],
        'name' => $c['name'],
        'category' => $c['category'],
        'description' => $c['description'],
        'price' => $offPrice,
        'price_offline' => $offPrice,
        'price_online' => $onPrice,
        'wa_group_link' => $c['wa_group_link'] ?? '',
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
