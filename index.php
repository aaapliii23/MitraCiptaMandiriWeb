<?php
session_start();
// Generate CSRF Token for the form
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

require_once 'includes/db_config.php';

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
    $jsClassData[$c['id']] = [
        'id' => $c['id'],
        'name' => $c['name'],
        'description' => $c['description'],
        'price' => $c['price'],
        'image' => $c['image'],
        'features' => json_decode($c['features'], true) ?: []
    ];
}
?>
<script>window.mcmClassDetails = <?php echo json_encode($jsClassData); ?>;</script>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MCM - Mitra Cipta Mandiri | Solusi Kemandirian Ekonomi</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <?php include 'partials/section_beranda.php'; ?>
    <?php include 'partials/section_tentang.php'; ?>
    <?php include 'partials/section_galeri.php'; ?>
    <?php include 'partials/section_paket.php'; ?>
    <?php include 'partials/section_testimoni.php'; ?>
    <?php include 'partials/modal_detail.php'; ?>
    <?php include 'partials/modal_booking.php'; ?>
    <?php include 'partials/modal_checkout.php'; ?>

    <?php include 'includes/footer.php'; ?>

    <?php include 'partials/index_scripts.php'; ?>
</body>
</html>
