<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MCM CMS - Superadmin</title>
    <meta name="csrf-token" content="<?php echo htmlspecialchars(mcm_csrf_token()); ?>">
    <script>window.MCM_CSRF_TOKEN = "<?php echo htmlspecialchars(mcm_csrf_token()); ?>";</script>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="shortcut icon" type="image/png" href="../assets/img/logo.png">
    <link rel="apple-touch-icon" href="../assets/img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php include __DIR__ . '/styles_head.php'; ?>
    <?php include __DIR__ . '/styles_head_components.php'; ?>
</head>
<body>

<?php include __DIR__ . '/nav_bottom.php'; ?>

<?php include __DIR__ . '/sidebar.php'; ?>

<!-- Main Content -->
<div class="main-content bg-light" style="min-height: 100vh;">
    <?php include __DIR__ . '/mobile_header.php'; ?>

    <?php include __DIR__ . '/offcanvas.php'; ?>