<?php
session_start();
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin_login.php");
    exit;
}

require_once '../includes/db_config.php';

function getImgSrc($path) {
    if (empty($path)) return '../assets/img/logo.png';
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0 || strpos($path, '../') === 0) {
        return $path;
    }
    return '../' . ltrim($path, '/');
}

$adminBase = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

$page = $_GET['page'] ?? 'dashboard';

// Greeting logic
date_default_timezone_set('Asia/Jakarta');
$hour = date('H');
if ($hour >= 5 && $hour < 11) { $greeting = "Selamat Pagi"; $greetIcon = "fa-sun"; $greetColor = "#f59e0b"; }
elseif ($hour >= 11 && $hour < 15) { $greeting = "Selamat Siang"; $greetIcon = "fa-cloud-sun"; $greetColor = "#2563eb"; }
elseif ($hour >= 15 && $hour < 18) { $greeting = "Selamat Sore"; $greetIcon = "fa-cloud-sun-rain"; $greetColor = "#7c3aed"; }
else { $greeting = "Selamat Malam"; $greetIcon = "fa-moon"; $greetColor = "#1e293b"; }

// Fetch Statistics for Dashboard and Reports
$stats = [];
if ($page === 'dashboard' || $page === 'reports') {
    try {
        $stats['orders'] = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
        $stats['classes'] = $pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn();
        $stats['gallery'] = $pdo->query("SELECT COUNT(*) FROM gallery")->fetchColumn();
        $stats['admins'] = $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
        $stats['instructors'] = $pdo->query("SELECT COUNT(*) FROM instructors")->fetchColumn();
        
        $stmt = $pdo->query("SELECT o.*, c.name as class_name FROM orders o JOIN classes c ON o.class_id = c.id ORDER BY o.created_at DESC LIMIT 5");
        $recent_orders = $stmt->fetchAll();

        // Data for Chart: Registrations by Month (Last 6 Months)
        $chart_data = $pdo->query("SELECT DATE_FORMAT(created_at, '%M') as month, COUNT(*) as count 
                                   FROM orders 
                                   WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                                   GROUP BY MONTH(created_at)
                                   ORDER BY created_at ASC")->fetchAll(PDO::FETCH_ASSOC);
        
        // Data for Pie Chart: Categories
        $category_data = $pdo->query("SELECT category, COUNT(*) as count FROM classes GROUP BY category")->fetchAll(PDO::FETCH_ASSOC);
        
        // Top 3 Most Popular Classes
        $popular_classes = $pdo->query("SELECT c.name, COUNT(o.id) as total 
                                        FROM classes c 
                                        LEFT JOIN orders o ON c.id = o.class_id 
                                        GROUP BY c.id 
                                        ORDER BY total DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {}
}

// Fetch Orders (Pesanan) with Search and Category
$orders = [];
if ($page === 'orders') {
    $search = $_GET['search'] ?? '';
    try {
        $sql = "SELECT o.*, c.name as class_name, c.category as class_category 
                FROM orders o 
                JOIN classes c ON o.class_id = c.id";
        
        if (!empty($search)) {
            $sql .= " WHERE o.customer_name LIKE :search 
                      OR o.order_number LIKE :search 
                      OR o.customer_phone LIKE :search";
            $stmt = $pdo->prepare($sql . " ORDER BY o.created_at DESC");
            $searchParam = "%$search%";
            $stmt->bindParam(':search', $searchParam);
            $stmt->execute();
        } else {
            $stmt = $pdo->query($sql . " ORDER BY o.created_at DESC");
        }
        $orders = $stmt->fetchAll();
    } catch (PDOException $e) {}
}

// Fetch Classes
$classes = [];
if ($page === 'classes') {
    try {
        $stmt = $pdo->query("SELECT * FROM classes ORDER BY created_at DESC");
        $classes = $stmt->fetchAll();
    } catch (PDOException $e) {}
}

// Fetch Gallery
$gallery = [];
if ($page === 'gallery') {
    try {
        $stmt = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC");
        $gallery = $stmt->fetchAll();
    } catch (PDOException $e) {}
}

// Fetch Admins
$admins = [];
if ($page === 'admins') {
    try {
        $stmt = $pdo->query("SELECT * FROM admins ORDER BY created_at DESC");
        $admins = $stmt->fetchAll();
    } catch (PDOException $e) {}
}

// Fetch Instructors
$instructors = [];
if ($page === 'instructors') {
    try {
        $stmt = $pdo->query("SELECT * FROM instructors ORDER BY created_at DESC");
        $instructors = $stmt->fetchAll();
    } catch (PDOException $e) {}
}

// Fetch Certifications
$certs = [];
if ($page === 'certs') {
    try {
        $stmt = $pdo->query("SELECT * FROM certifications ORDER BY created_at DESC");
        $certs = $stmt->fetchAll();
    } catch (PDOException $e) {}
}

// Fetch Categories
$categories = [];
try {
    $stmt = $pdo->query("SELECT * FROM class_categories ORDER BY name ASC");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {}


?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MCM CMS - Superadmin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        
        :root {
            --primary-color: #2563eb;
            --secondary-color: #3b82f6;
            --dark-bg: #0f172a;
            --sidebar-bg: #1e293b;
        }
        
        html, body {
            height: auto !important;
            min-height: 100% !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch;
        }

        body { 
            background-color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: #334155;
            -webkit-tap-highlight-color: transparent;
            overflow-x: hidden;
        }

        /* Premium Animations */
        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(5deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }

        .float-icon {
            animation: float 6s ease-in-out infinite;
        }

        .stat-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }

        .stat-card:hover .position-absolute {
            opacity: 0.2 !important;
            transform: scale(1.1) rotate(10deg);
        }
        
        
        /* Premium Text Styling */
        .gradient-text {
            background: linear-gradient(135deg, #2563eb, #7c3aed, #db2777);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        .greet-line {
            height: 4px;
            width: 40px;
            background: linear-gradient(90deg, #2563eb, #3b82f6);
            border-radius: 10px;
            margin-top: 8px;
            transition: width 0.5s ease;
        }

        .mb-5:hover .greet-line {
            width: 80px;
        }

        /* Bottom Nav Upgrades */
        .mcm-bottom-nav {
            padding: 0 15px !important;
        }

        .mcm-nav-item .icon-wrapper {
            position: relative;
            z-index: 2;
        }

        @keyframes pulse-icon {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .mcm-nav-item:active .icon-wrapper {
            animation: pulse-icon 0.3s ease-out;
        }
        .sidebar { 
            height: 100vh; 
            background-color: var(--dark-bg); 
            width: 280px; 
            position: fixed; 
            top: 0; left: 0; 
            z-index: 1000; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            box-shadow: 4px 0 24px rgba(0,0,0,0.05);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.1) transparent;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }
        .sidebar .nav-link { 
            color: #94a3b8; 
            padding: 16px 24px; 
            font-weight: 600; 
            border-radius: 16px; 
            margin: 0 16px 10px; 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            border: 1px solid transparent;
        }

        .sidebar .nav-link i { 
            width: 32px; 
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover { 
            background-color: rgba(255,255,255,0.08); 
            color: #fff; 
            transform: translateX(8px);
            border-color: rgba(255,255,255,0.1);
        }

        .sidebar .nav-link:hover i {
            transform: scale(1.2);
            color: var(--secondary-color);
        }

        .sidebar .nav-link.active { 
            background: linear-gradient(135deg, #2563eb, #4f46e5); 
            color: #fff !important; 
            box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
            border-color: rgba(255,255,255,0.2);
        }

        /* Shimmer Effect for Active Link */
        .sidebar .nav-link.active::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transform: skewX(-25deg);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { left: -100%; }
            50% { left: 150%; }
            100% { left: 150%; }
        }

        .sidebar .nav-link.active i {
            color: #fff !important;
            filter: drop-shadow(0 0 5px rgba(255,255,255,0.5));
        }

        /* Continuous Idle Animation for Sidebar Icons */
        @keyframes side-icon-pulse {
            0% { transform: scale(1); opacity: 0.7; }
            50% { transform: scale(1.15); opacity: 1; }
            100% { transform: scale(1); opacity: 0.7; }
        }

        .sidebar .nav-link:not(.active) i {
            animation: side-icon-pulse 4s ease-in-out infinite;
        }

        .sidebar .nav-link:not(.active):hover i {
            animation: none;
        }
        
        /* Main Content */
        .main-content { 
            margin-left: 280px; 
            padding: 40px; 
            transition: all 0.3s; 
        }
        
        /* Modern Design System Overhaul */
        .card { 
            border-radius: 1.25rem; 
            border: 1px solid rgba(226, 232, 240, 0.6); 
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04); 
            background: #ffffff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -10px rgba(37, 99, 235, 0.1);
            border-color: rgba(37, 99, 235, 0.1);
        }
        
        .stat-card {
            border: none;
            overflow: hidden;
            position: relative;
        }
        .stat-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 150px;
            height: 150px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        /* Modern Table */
        .table-responsive { 
            border-radius: 1.25rem; 
            border: 1px solid rgba(226, 232, 240, 0.8);
            background: #fff;
        }
        .table { margin-bottom: 0; }
        .table thead th { 
            background: #f8fafc;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.7rem;
            letter-spacing: 1px;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .table tbody td { 
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f8fafc;
            color: #334155;
            font-size: 0.9rem;
        }
        .table tbody tr:last-child td { border-bottom: none; }
        .table tbody tr:hover { background-color: #fcfdfe; }

        /* Modern Badges */
        .badge {
            padding: 0.5rem 1rem;
            font-weight: 700;
            border-radius: 50rem;
            font-size: 0.75rem;
        }
        .badge-soft-warning { background: #fffbeb; color: #d97706; }
        .badge-soft-success { background: #f0fdf4; color: #16a34a; }
        .badge-soft-primary { background: #eff6ff; color: #2563eb; }
        .badge-soft-danger { background: #fef2f2; color: #dc2626; }

        /* Action Buttons */
        .btn-action {
            width: 38px;
            height: 38px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            transition: all 0.2s;
        }
        .btn-action:hover { transform: scale(1.1); }

        /* Images */
        .image-preview {
            width: 56px;
            height: 56px;
            object-fit: cover;
            border-radius: 1rem;
            border: 2px solid #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        @media (max-width: 991px) {
            .sidebar { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 20px 20px 100px 20px !important; }
            .card { border-radius: 1rem; }
            .table thead { display: none; } /* Hide headers on very small mobile if card fallback is used, but for now we keep table-responsive */
        }
    </style>
</head>
<body>

<!-- Bottom Navigation for Mobile -->
<div class="mcm-bottom-nav">
    <a href="?page=dashboard" class="mcm-nav-item <?php echo $page == 'dashboard' ? 'active' : ''; ?>">
        <div class="icon-wrapper"><i class="fas fa-th-large"></i></div>
        <span>Dasbor</span>
    </a>
    <a href="?page=orders" class="mcm-nav-item <?php echo $page == 'orders' ? 'active' : ''; ?>">
        <div class="icon-wrapper"><i class="fas fa-shopping-cart"></i></div>
        <span>Pesanan</span>
    </a>
    <a href="?page=classes" class="mcm-nav-item <?php echo $page == 'classes' ? 'active' : ''; ?>">
        <div class="icon-wrapper"><i class="fas fa-book-open"></i></div>
        <span>Paket</span>
    </a>
    <a href="?page=gallery" class="mcm-nav-item <?php echo $page == 'gallery' ? 'active' : ''; ?>">
        <div class="icon-wrapper"><i class="fas fa-camera-retro"></i></div>
        <span>Galeri</span>
    </a>
    <a href="?page=instructors" class="mcm-nav-item <?php echo $page == 'instructors' ? 'active' : ''; ?>">
        <div class="icon-wrapper"><i class="fas fa-user-tie"></i></div>
        <span>Guru</span>
    </a>
    <a href="?page=certs" class="mcm-nav-item <?php echo $page == 'certs' ? 'active' : ''; ?>">
        <div class="icon-wrapper"><i class="fas fa-certificate"></i></div>
        <span>Legal</span>
    </a>
    <a href="?page=reports" class="mcm-nav-item <?php echo $page == 'reports' ? 'active' : ''; ?>">
        <div class="icon-wrapper"><i class="fas fa-chart-bar"></i></div>
        <span>Laporan</span>
    </a>
</div>

<!-- Sidebar -->
<div class="sidebar d-flex flex-column shadow-lg" id="sidebar">
    <div class="p-4 d-flex align-items-center justify-content-between border-bottom border-secondary border-opacity-25">
        <a class="text-decoration-none d-flex align-items-center" href="#">
            <img src="../assets/img/logo.png" alt="MCM Logo" height="40" class="me-2">
            <div class="brand-text">
                <div class="fw-bold fs-6 text-white" style="letter-spacing: 1px; line-height: 1.1;">MITRA CIPTA</div>
                <div class="text-white-50" style="letter-spacing: 1.5px; font-size: 0.75rem;">MANDIRI</div>
            </div>
        </a>
        <button class="btn btn-link text-white-50 d-md-none p-0" id="closeSidebar"><i class="fas fa-times fs-5"></i></button>
    </div>
    
    <div class="px-4 py-4 mb-2 text-center">
        <div class="bg-gradient bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));">
            <i class="fas fa-user-shield fs-2 text-white"></i>
        </div>
        <div class="text-white fw-bold fs-5"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></div>
        <div class="badge bg-warning text-dark mt-1 px-3 rounded-pill">Superadmin</div>
    </div>
    
    <ul class="nav flex-column mb-auto px-2">
        <li class="nav-item mb-1">
            <a href="?page=dashboard" class="nav-link <?php echo $page == 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=orders" class="nav-link <?php echo $page == 'orders' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i> Pesanan & Transaksi
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=classes" class="nav-link <?php echo $page == 'classes' ? 'active' : ''; ?>">
                <i class="fas fa-book-open"></i> Paket Pelatihan
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=gallery" class="nav-link <?php echo $page == 'gallery' ? 'active' : ''; ?>">
                <i class="fas fa-camera-retro"></i> Galeri Foto
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=instructors" class="nav-link <?php echo $page == 'instructors' ? 'active' : ''; ?>">
                <i class="fas fa-user-tie"></i> Instruktur & Penguji
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=certs" class="nav-link <?php echo $page == 'certs' ? 'active' : ''; ?>">
                <i class="fas fa-certificate"></i> Legalitas & Sertifikasi
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=reports" class="nav-link <?php echo $page == 'reports' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i> Laporan & Rekap
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=categories" class="nav-link <?php echo $page == 'categories' ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i> Kategori Pelatihan
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=admins" class="nav-link <?php echo $page == 'admins' ? 'active' : ''; ?>">
                <i class="fas fa-users-cog"></i> Kelola Admin
            </a>
        </li>
        <li class="nav-item mb-1">
            <a href="?page=settings" class="nav-link <?php echo $page == 'settings' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i> Pengaturan Web
            </a>
        </li>
    </ul>
    
    <div class="p-4 mt-auto border-top border-secondary border-opacity-25">
        <a href="logout.php" class="btn btn-danger w-100 fw-bold shadow-sm" style="background: rgba(220, 53, 69, 0.9);"><i class="fas fa-power-off me-2"></i>Keluar Sistem</a>
    </div>
</div>

<!-- Main Content -->
<div class="main-content bg-light" style="min-height: 100vh;">
    <!-- Mobile Header (Simplified) -->
    <div class="d-md-none sticky-top bg-white py-3 px-4 shadow-sm border-bottom mb-4 d-flex justify-content-between align-items-center" style="z-index: 999;">
        <div class="d-flex align-items-center">
            <img src="../assets/img/logo.png" alt="MCM Logo" height="35" class="me-2">
            <div class="brand-text">
                <div class="fw-bold text-dark" style="font-size: 0.85rem; line-height: 1.1;">MITRA CIPTA MANDIRI</div>
                <div class="text-primary fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">PANEL</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="vr mx-1 text-muted opacity-25"></div>
            <div class="rounded-circle border border-2 border-primary p-1" data-bs-toggle="offcanvas" data-bs-target="#adminOffcanvas" role="button">
                <div class="bg-primary rounded-circle" style="width: 35px; height: 35px; background: linear-gradient(135deg, #2563eb, #3b82f6);">
                    <i class="fas fa-user-shield text-white p-2"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Offcanvas Menu -->
    <div class="offcanvas offcanvas-end border-0 shadow" tabindex="-1" id="adminOffcanvas" style="border-radius: 2rem 0 0 2rem;">
        <div class="offcanvas-header bg-light py-4 px-4">
            <h5 class="offcanvas-title fw-bold text-dark"><i class="fas fa-user-shield me-2 text-primary"></i>Panel Admin</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-4">
            <div class="text-center mb-5 p-4 rounded-4 bg-light shadow-sm">
                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 80px; height: 80px; background: linear-gradient(135deg, #2563eb, #3b82f6);">
                    <i class="fas fa-user-shield fs-1 text-white"></i>
                </div>
                <h5 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></h5>
                <span class="badge bg-warning text-dark rounded-pill px-3 mt-2">Superadmin MCM</span>
            </div>

            <div class="list-group list-group-flush gap-2">
                <a href="?page=admins" class="list-group-item list-group-item-action border-0 rounded-3 py-3 px-4 d-flex align-items-center bg-light mb-2">
                    <i class="fas fa-users-cog me-3 text-primary fs-5"></i>
                    <div class="fw-bold text-dark">Kelola Admin</div>
                </a>
                <a href="?page=settings" class="list-group-item list-group-item-action border-0 rounded-3 py-3 px-4 d-flex align-items-center bg-light mb-2">
                    <i class="fas fa-cog me-3 text-primary fs-5"></i>
                    <div class="fw-bold text-dark">Pengaturan Web</div>
                </a>
                <div class="mt-4 pt-4 border-top">
                    <a href="logout.php" class="btn btn-danger w-100 py-3 rounded-pill fw-bold shadow">
                        <i class="fas fa-power-off me-2"></i> Keluar Sistem
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- DASHBOARD HOME -->
    <?php if ($page === 'dashboard'): ?>
        <div class="mb-5 d-flex justify-content-between align-items-end" data-aos="fade-down">
            <div>
                <div class="d-flex align-items-center mb-1">
                    <div class="p-2 rounded-3 me-3 bg-white shadow-sm border" style="color: <?php echo $greetColor; ?>;">
                        <i class="fas <?php echo $greetIcon; ?> fs-4"></i>
                    </div>
                    <h2 class="fw-bold mb-0 text-dark"><?php echo $greeting; ?>, <span class="gradient-text"><?php echo explode(' ', $_SESSION['admin_username'])[0]; ?>!</span></h2>
                </div>
                <p class="text-muted mb-0 ps-5 ms-2">Platform MCM berjalan optimal hari ini. Berikut ringkasan performa terbaru.</p>
                <div class="greet-line ms-5 ps-2"></div>
            </div>
            <div class="d-none d-md-block">
                <span class="badge bg-white text-dark shadow-sm py-2 px-3 rounded-pill border">
                    <i class="far fa-calendar-alt me-2 text-primary"></i><?php echo date('d F Y'); ?>
                </span>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                <div class="card stat-card border-0 h-100 shadow-sm position-relative overflow-hidden" style="background: linear-gradient(135deg, #1e40af, #3b82f6, #60a5fa); color: white;">
                    <div class="card-body p-4 position-relative z-1">
                        <div class="bg-white bg-opacity-25 rounded-3 p-2 d-inline-block mb-3">
                            <i class="fas fa-shopping-bag fs-4"></i>
                        </div>
                        <h2 class="fw-bold mb-1 counter-value" data-target="<?php echo $stats['orders']; ?>">0</h2>
                        <div class="small fw-bold text-white-50 text-uppercase">Total Pesanan</div>
                    </div>
                    <i class="fas fa-shopping-bag position-absolute opacity-10 float-icon" style="font-size: 8rem; right: -20px; bottom: -20px;"></i>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
                <div class="card stat-card border-0 h-100 shadow-sm position-relative overflow-hidden" style="background: linear-gradient(135deg, #065f46, #10b981, #34d399); color: white;">
                    <div class="card-body p-4 position-relative z-1">
                        <div class="bg-white bg-opacity-25 rounded-3 p-2 d-inline-block mb-3">
                            <i class="fas fa-book-open fs-4"></i>
                        </div>
                        <h2 class="fw-bold mb-1 counter-value" data-target="<?php echo $stats['classes']; ?>">0</h2>
                        <div class="small fw-bold text-white-50 text-uppercase">Paket Aktif</div>
                    </div>
                    <i class="fas fa-book-open position-absolute opacity-10 float-icon" style="font-size: 8rem; right: -20px; bottom: -20px; animation-delay: 1s;"></i>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                <div class="card stat-card border-0 h-100 shadow-sm position-relative overflow-hidden" style="background: linear-gradient(135deg, #5b21b6, #8b5cf6, #a78bfa); color: white;">
                    <div class="card-body p-4 position-relative z-1">
                        <div class="bg-white bg-opacity-25 rounded-3 p-2 d-inline-block mb-3">
                            <i class="fas fa-images fs-4"></i>
                        </div>
                        <h2 class="fw-bold mb-1 counter-value" data-target="<?php echo $stats['gallery']; ?>">0</h2>
                        <div class="small fw-bold text-white-50 text-uppercase">Foto Galeri</div>
                    </div>
                    <i class="fas fa-images position-absolute opacity-10 float-icon" style="font-size: 8rem; right: -20px; bottom: -20px; animation-delay: 2s;"></i>
                </div>
            </div>
            <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
                <div class="card stat-card border-0 h-100 shadow-sm position-relative overflow-hidden" style="background: linear-gradient(135deg, #334155, #64748b, #94a3b8); color: white;">
                    <div class="card-body p-4 position-relative z-1">
                        <div class="bg-white bg-opacity-25 rounded-3 p-2 d-inline-block mb-3">
                            <i class="fas fa-user-shield fs-4"></i>
                        </div>
                        <h2 class="fw-bold mb-1 counter-value" data-target="<?php echo $stats['admins']; ?>">0</h2>
                        <div class="small fw-bold text-white-50 text-uppercase">Tim Admin</div>
                    </div>
                    <i class="fas fa-user-shield position-absolute opacity-10 float-icon" style="font-size: 8rem; right: -20px; bottom: -20px; animation-delay: 3s;"></i>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-4 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">Tren Pendaftaran</h5>
                        <div class="badge bg-light text-primary rounded-pill px-3">6 Bulan Terakhir</div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div style="height: 300px;">
                            <canvas id="registrationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-4 px-4 border-bottom-0">
                        <h5 class="fw-bold mb-0 text-dark">Distribusi Paket</h5>
                    </div>
                    <div class="card-body px-4 pb-4 d-flex flex-column">
                        <div style="height: 200px;" class="mb-4">
                            <canvas id="categoryChart"></canvas>
                        </div>
                        <div class="mt-auto">
                            <h6 class="fw-bold text-dark mb-3 small text-uppercase">Program Terpopuler</h6>
                            <?php foreach ($popular_classes as $pc): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted fw-medium text-truncate me-2"><?php echo htmlspecialchars($pc['name']); ?></span>
                                    <span class="badge bg-light text-primary rounded-pill"><?php echo $pc['total']; ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-4 px-4 border-bottom-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">Pesanan Terbaru</h5>
                        <a href="?page=orders" class="btn btn-sm btn-soft-primary px-3 rounded-pill fw-bold">Semua Pesanan</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="bg-light text-secondary small text-uppercase">
                                    <tr>
                                        <th class="ps-4">No. Order</th>
                                        <th>Nama Peserta</th>
                                        <th>Program</th>
                                        <th class="text-end pe-4">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $ro): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold">#<?php echo $ro['order_number']; ?></td>
                                            <td><?php echo htmlspecialchars($ro['customer_name']); ?></td>
                                            <td><span class="small text-muted"><?php echo htmlspecialchars($ro['class_name']); ?></span></td>
                                            <td class="text-end pe-4">
                                                <span class="badge badge-soft-<?php echo ($ro['status'] == 'confirmed' ? 'success' : ($ro['status'] == 'pending' ? 'warning' : 'danger')); ?>">
                                                    <?php echo ucfirst($ro['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-4 px-4 border-bottom-0">
                        <h5 class="fw-bold mb-0 text-dark">Aksi Cepat</h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="d-grid gap-2">
                            <a href="?page=classes" class="btn btn-light text-start p-3 border-0 rounded-4 transition-all hover-translate">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3 me-3">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small">Tambah Paket</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Buat program pelatihan baru</div>
                                    </div>
                                </div>
                            </a>
                            <a href="?page=gallery" class="btn btn-light text-start p-3 border-0 rounded-4 transition-all hover-translate">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 text-success p-2 rounded-3 me-3">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small">Upload Foto</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Update galeri kegiatan</div>
                                    </div>
                                </div>
                            </a>
                            <a href="?page=settings" class="btn btn-light text-start p-3 border-0 rounded-4 transition-all hover-translate">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3 me-3">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small">Ubah Kontak</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">Update info WA/Email</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Chart Initialization for Dashboard
            (function() {
                if (typeof Chart === 'undefined') return;

                const months = <?php echo json_encode(array_column($chart_data, 'month') ?: ['Jan', 'Feb', 'Mar']); ?>;
                const counts = <?php echo json_encode(array_column($chart_data, 'count') ?: [0, 0, 0]); ?>;
                const catLabels = <?php echo json_encode(array_column($category_data, 'category') ?: ['Belum Ada Data']); ?>;
                const catCounts = <?php echo json_encode(array_column($category_data, 'count') ?: [1]); ?>;

                const regCtx = document.getElementById('registrationChart')?.getContext('2d');
                if (regCtx) {
                    new Chart(regCtx, {
                        type: 'line',
                        data: {
                            labels: months,
                            datasets: [{
                                label: 'Pendaftaran',
                                data: counts,
                                borderColor: '#2563eb',
                                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                                fill: true,
                                tension: 0.4,
                                pointRadius: 4,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#2563eb',
                                pointBorderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { beginAtZero: true, border: { display: false }, grid: { color: 'rgba(0,0,0,0.05)' } },
                                x: { border: { display: false }, grid: { display: false } }
                            }
                        }
                    });
                }

                const catCtx = document.getElementById('categoryChart')?.getContext('2d');
                if (catCtx) {
                    new Chart(catCtx, {
                        type: 'doughnut',
                        data: {
                            labels: catLabels,
                            datasets: [{
                                data: catCounts,
                                backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#7c3aed', '#ef4444', '#64748b'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom', labels: { boxWidth: 12, padding: 20, font: { family: 'Plus Jakarta Sans', weight: '600' } } }
                            },
                            cutout: '75%'
                        }
                    });
                }
            })();
        </script>
    <?php endif; ?>

    <!-- ORDERS PAGE -->
    <?php if ($page === 'orders'): ?>
        <div class="row align-items-center mb-5 g-3">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Data Transaksi</h2>
                <p class="text-muted mb-0">Kelola pesanan dan status pembayaran pelanggan.</p>
            </div>
            <div class="col-md-6">
                <form action="" method="GET" class="d-flex gap-2">
                    <input type="hidden" name="page" value="orders">
                    <div class="input-group shadow-sm rounded-3 overflow-hidden">
                        <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 py-2" placeholder="Cari Pelanggan / No. Order..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">Cari</button>
                </form>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Pelanggan</th>
                                <th>Kategori & Kelas</th>
                                <th>Total</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr><td colspan="6" class="text-center py-5 text-muted">Data tidak ditemukan.</td></tr>
                            <?php else: ?>
                                <?php foreach ($orders as $o): ?>
                                    <tr>
                                        <td class="text-secondary fw-bold small"><?php echo htmlspecialchars($o['order_number']); ?></td>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($o['customer_name']); ?></div>
                                            <div class="small text-muted d-flex align-items-center mt-1">
                                                <i class="fab fa-whatsapp me-1 text-success"></i><?php echo htmlspecialchars($o['customer_phone']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;"><?php echo htmlspecialchars($o['class_category']); ?></div>
                                            <div class="fw-bold text-primary"><?php echo htmlspecialchars($o['class_name']); ?></div>
                                        </td>
                                        <td class="fw-bold text-dark">Rp <?php echo number_format($o['amount'], 0, ',', '.'); ?></td>
                                        <td class="text-center">
                                            <?php if ($o['status'] === 'pending'): ?>
                                                <span class="badge badge-soft-warning"><i class="fas fa-clock me-1"></i>Pending</span>
                                            <?php elseif ($o['status'] === 'confirmed'): ?>
                                                <span class="badge badge-soft-success"><i class="fas fa-check-circle me-1"></i>Lunas</span>
                                            <?php else: ?>
                                                <span class="badge badge-soft-danger"><i class="fas fa-times-circle me-1"></i>Batal</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button class="btn btn-action btn-soft-primary" data-bs-toggle="modal" data-bs-target="#detailPesananModal" 
                                                    data-order="<?php echo htmlspecialchars($o['order_number']); ?>"
                                                    data-name="<?php echo htmlspecialchars($o['customer_name']); ?>"
                                                    data-phone="<?php echo htmlspecialchars($o['customer_phone']); ?>"
                                                    data-email="<?php echo htmlspecialchars(!empty($o['customer_email']) ? $o['customer_email'] : '-'); ?>"
                                                    data-instansi="<?php echo htmlspecialchars(!empty($o['customer_institution']) ? $o['customer_institution'] : '-'); ?>"
                                                    data-alamat="<?php echo htmlspecialchars(!empty($o['customer_address']) ? $o['customer_address'] : '-'); ?>"
                                                    data-kelas="<?php echo htmlspecialchars($o['class_name']); ?>"
                                                    data-harga="<?php echo number_format($o['amount'], 0, ',', '.'); ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-action btn-soft-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal" data-id="<?php echo $o['id']; ?>" data-name="<?php echo htmlspecialchars($o['customer_name']); ?>" data-status="<?php echo $o['status']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-action btn-soft-danger" onclick="deleteItem('orders', <?php echo $o['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- CLASSES PAGE -->
    <?php if ($page === 'classes'): ?>
        <div class="row align-items-center mb-5 g-3">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Paket Pelatihan</h2>
                <p class="text-muted mb-0">Kelola kurikulum dan paket kursus profesional MCM.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="d-flex justify-content-md-end gap-2">
                    <button class="btn btn-outline-primary px-4 shadow-sm rounded-pill" onclick="showModal('categoryModal')">
                        <i class="fas fa-tags me-2"></i>Kategori
                    </button>
                    <button class="btn btn-primary px-4 shadow-sm rounded-pill" onclick="resetClassForm(); showModal('classModal');">
                        <i class="fas fa-plus me-2"></i>Tambah Paket
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <?php if (empty($classes)): ?>
                <div class="col-12 text-center py-5">
                    <div class="text-muted">Belum ada paket pelatihan yang dibuat.</div>
                </div>
            <?php else: ?>
                <?php foreach ($classes as $c): ?>
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 border-0 shadow-sm overflow-hidden">
                            <div class="position-relative">
                                <?php if (!empty($c['image'])): ?>
                                    <img src="<?php echo htmlspecialchars(getImgSrc($c['image'])); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($c['name']); ?>" style="height: 180px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                        <i class="fas fa-book-open fa-3x text-muted opacity-25"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="position-absolute top-0 end-0 p-3">
                                    <span class="badge bg-white text-primary shadow-sm rounded-pill"><?php echo htmlspecialchars($c['category']); ?></span>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <h5 class="fw-bold text-dark mb-2"><?php echo htmlspecialchars($c['name']); ?></h5>
                                <div class="text-primary fw-bold fs-5 mb-3">Rp <?php echo number_format($c['price'], 0, ',', '.'); ?></div>
                                <p class="text-muted small mb-4" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo htmlspecialchars($c['description'] ?? 'Tidak ada deskripsi.'); ?></p>
                                
                                <div class="d-flex gap-2">
                                    <button class="btn btn-light flex-grow-1 fw-bold rounded-pill" onclick='editClass(<?php echo json_encode($c); ?>)'>
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </button>
                                    <button class="btn btn-soft-danger btn-action rounded-circle" onclick="deleteItem('classes', <?php echo $c['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- GALLERY PAGE -->
    <?php if ($page === 'gallery'): ?>
        <div class="row align-items-center mb-5 g-3">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Galeri Foto</h2>
                <p class="text-muted mb-0">Dokumentasi visual kegiatan dan fasilitas.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn btn-primary px-4 shadow-sm rounded-pill" onclick="resetGalleryForm(); showModal('galleryModal');">
                    <i class="fas fa-upload me-2"></i>Tambah Foto
                </button>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($gallery as $g): ?>
                <div class="col-md-4 col-sm-6">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden">
                        <div class="position-relative" style="height: 220px;">
                            <img src="<?php echo htmlspecialchars(getImgSrc($g['image'])); ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?php echo htmlspecialchars($g['title']); ?>">
                            <div class="position-absolute top-0 end-0 p-3">
                                <span class="badge bg-dark bg-opacity-50 text-white rounded-pill backdrop-blur"><?php echo htmlspecialchars($g['category']); ?></span>
                            </div>
                        </div>
                        <div class="card-body p-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($g['title']); ?></h6>
                            <div class="d-flex gap-2">
                                <button class="btn btn-action btn-soft-primary" onclick="editGallery(<?php echo htmlspecialchars(json_encode($g)); ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-action btn-soft-danger" onclick="deleteItem('gallery', <?php echo $g['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- INSTRUCTORS PAGE -->
    <?php if ($page === 'instructors'): ?>
        <div class="row align-items-center mb-5 g-3">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Tim Pengajar</h2>
                <p class="text-muted mb-0">Data instruktur dan penguji profesional MCM.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn btn-primary px-4 shadow-sm rounded-pill" onclick="showModal('instructorModal')">
                    <i class="fas fa-plus me-2"></i>Tambah Instruktur
                </button>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($instructors as $ins): ?>
                <div class="col-md-4 col-xl-3">
                    <div class="card border-0 shadow-sm text-center p-4">
                        <div class="position-relative mb-3 d-inline-block mx-auto">
                            <img src="<?php echo htmlspecialchars(getImgSrc($ins['image'])); ?>" class="rounded-circle shadow-sm" style="width: 100px; height: 100px; object-fit: cover; border: 4px solid #fff;">
                            <div class="position-absolute bottom-0 end-0">
                                <span class="badge badge-soft-success rounded-circle p-2 border border-2 border-white"><i class="fas fa-check"></i></span>
                            </div>
                        </div>
                        <h6 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($ins['name']); ?></h6>
                        <div class="text-primary small fw-bold mb-4"><?php echo htmlspecialchars($ins['specialization']); ?></div>
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-action btn-soft-primary" onclick="editInstructor(<?php echo htmlspecialchars(json_encode($ins)); ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-action btn-soft-danger" onclick="deleteItem('instructors', <?php echo $ins['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- CERTIFICATIONS PAGE -->
    <?php if ($page === 'certs'): ?>
        <div class="row align-items-center mb-5 g-3">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Legalitas & Sertifikasi</h2>
                <p class="text-muted mb-0">Arsip dokumen resmi dan sertifikat lembaga.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn btn-primary px-4 shadow-sm rounded-pill" onclick="showModal('certModal')">
                    <i class="fas fa-plus me-2"></i>Tambah Dokumen
                </button>
            </div>
        </div>

        <div class="row g-4">
            <?php foreach ($certs as $ct): ?>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm group">
                        <div class="p-4 bg-light text-center" style="border-radius: 1.25rem 1.25rem 0 0;">
                            <img src="<?php echo htmlspecialchars(getImgSrc($ct['image'])); ?>" class="shadow-lg rounded-3" style="height: 160px; width: auto; max-width: 100%; object-fit: contain;">
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($ct['title']); ?></h6>
                                    <span class="small text-muted"><i class="far fa-calendar-alt me-1"></i>Diupload <?php echo date('d M Y', strtotime($ct['created_at'])); ?></span>
                                </div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-action btn-soft-primary" onclick="editCert(<?php echo htmlspecialchars(json_encode($ct)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-action btn-soft-danger" onclick="deleteItem('certs', <?php echo $ct['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="d-grid">
                                <a href="<?php echo htmlspecialchars($ct['image']); ?>" target="_blank" class="btn btn-light rounded-pill fw-bold small">Lihat Dokumen Lengkap</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- REPORTS PAGE -->
    <?php if ($page === 'reports'): ?>
        <div class="row align-items-center mb-5 g-3 no-print" data-aos="fade-down">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Laporan & Analitik</h2>
                <p class="text-muted mb-0">Visualisasi data pendaftaran dan pertumbuhan MCM.</p>
            </div>
            <div class="col-md-6 text-md-end d-flex justify-content-md-end gap-2 align-items-center">
                <select id="reportYear" class="form-select rounded-pill border-primary text-primary fw-bold" style="width: auto; height: 42px;" onchange="updateReportYear(this.value)">
                    <?php 
                    $currentYear = date('Y');
                    for($i = $currentYear; $i >= $currentYear - 5; $i--) echo "<option value='$i'>Tahun $i</option>";
                    ?>
                </select>
                <button class="btn btn-soft-primary px-4 rounded-pill" onclick="window.print()" style="height: 42px;">
                    <i class="fas fa-print me-2"></i>Cetak
                </button>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="fas fa-wallet text-primary"></i>
                        </div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Total Estimasi Omzet</h6>
                    </div>
                    <?php 
                    $revenue = $pdo->query("SELECT SUM(amount) FROM orders WHERE status = 'confirmed'")->fetchColumn();
                    ?>
                    <h3 class="fw-bold text-dark mb-1">Rp <?php echo number_format($revenue ?: 0, 0, ',', '.'); ?></h3>
                    <p class="small text-success mb-0"><i class="fas fa-arrow-up me-1"></i>Dari pesanan lunas</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-warning bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Pesanan Pending</h6>
                    </div>
                    <?php 
                    $pendingCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
                    ?>
                    <h3 class="fw-bold text-dark mb-1"><?php echo $pendingCount; ?></h3>
                    <p class="small text-muted mb-0">Menunggu konfirmasi</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="fas fa-user-check text-success"></i>
                        </div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Konversi Peserta</h6>
                    </div>
                    <?php 
                    $totalOrd = ($stats['orders'] ?? 0) ?: 1;
                    $confirmedCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'confirmed'")->fetchColumn() ?: 0;
                    $rate = round(($confirmedCount / $totalOrd) * 100);
                    ?>
                    <h3 class="fw-bold text-dark mb-1"><?php echo $rate; ?>%</h3>
                    <div class="progress" style="height: 6px; border-radius: 10px;">
                        <div class="progress-bar bg-success" style="width: <?php echo $rate; ?>%"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-danger bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="fas fa-user-times text-danger"></i>
                        </div>
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Pembatalan</h6>
                    </div>
                    <?php 
                    $cancelledCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'cancelled'")->fetchColumn();
                    ?>
                    <h3 class="fw-bold text-dark mb-1"><?php echo $cancelledCount; ?></h3>
                    <p class="small text-danger mb-0">Pesanan dibatalkan</p>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">Grafik Pendaftaran</h5>
                            <div class="btn-group btn-group-sm rounded-pill overflow-hidden border">
                                <button class="btn btn-light px-3 active chart-toggle" onclick="updateChart('weekly', this)">Mingguan</button>
                                <button class="btn btn-light px-3 chart-toggle" onclick="updateChart('monthly', this)">Bulanan</button>
                                <button class="btn btn-light px-3 chart-toggle" onclick="updateChart('yearly', this)">Tahunan</button>
                            </div>
                        </div>
                        <i class="fas fa-chart-line text-primary opacity-25 fs-4"></i>
                    </div>
                    <div style="height: 350px;">
                        <canvas id="reportsChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-primary text-white overflow-hidden position-relative" style="background: linear-gradient(135deg, #2563eb, #3b82f6) !important;">
                    <div class="position-relative z-index-1">
                        <h6 class="text-white-50 small text-uppercase fw-bold mb-3">Total Peserta</h6>
                        <h2 class="fw-bold mb-2"><?php echo number_format($stats['orders'] ?? 0); ?></h2>
                        <p class="small mb-0 opacity-75">Peserta terdaftar di seluruh program.</p>
                    </div>
                    <i class="fas fa-users position-absolute bottom-0 end-0 fs-1 opacity-10 m-3"></i>
                </div>
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                    <h6 class="text-muted small text-uppercase fw-bold mb-4">Program Terpopuler</h6>
                    <div class="overflow-auto" style="max-height: 250px;">
                        <?php 
                        $popular = $pdo->query("SELECT c.name, COUNT(o.id) as total FROM classes c LEFT JOIN orders o ON c.id = o.class_id GROUP BY c.id ORDER BY total DESC LIMIT 5")->fetchAll();
                        if (empty($popular)): ?>
                            <div class="text-center py-4 text-muted small">Belum ada data pendaftaran.</div>
                        <?php else: ?>
                            <?php foreach ($popular as $idx => $p): ?>
                                <div class="d-flex align-items-center mb-4">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3 fw-bold text-primary" style="min-width: 35px; height: 35px; font-size: 0.8rem;"><?php echo $idx + 1; ?></div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold text-dark small text-truncate" style="max-width: 150px;"><?php echo htmlspecialchars($p['name']); ?></div>
                                        <div class="progress mt-2" style="height: 6px; border-radius: 10px;">
                                            <?php $orderTotal = $stats['orders'] ?? 0; ?>
                                            <div class="progress-bar bg-primary rounded-pill" style="width: <?php echo ($orderTotal > 0 ? ($p['total'] / $orderTotal) * 100 : 0); ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="ms-3 fw-bold text-dark small"><?php echo $p['total']; ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <style>
            @media print {
                .no-print, .sidebar, .mcm-bottom-nav, .d-md-none, .btn-group, #reportYear, .btn-action { display: none !important; }
                .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
                .card { border: 1px solid #eee !important; box-shadow: none !important; }
                body { background: white !important; }
            }
            .main-content { overflow-y: auto !important; height: 100vh; }
        </style>

        <script>
            let mainChart = null;
            let currentChartType = 'weekly';
            let currentReportYear = new Date().getFullYear();

            function initReportsChart(type = 'weekly', year = null) {
                currentChartType = type;
                if (year) currentReportYear = year;
                
                const ctx = document.getElementById('reportsChart').getContext('2d');
                fetch(`actions/reports_data.php?type=${type}&year=${currentReportYear}`)
                    .then(res => res.json())
                    .then(data => {
                        if (mainChart) mainChart.destroy();
                        
                        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                        gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
                        gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

                        mainChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: data.labels,
                                datasets: [{
                                    label: 'Jumlah Pendaftaran',
                                    data: data.counts,
                                    borderColor: '#2563eb',
                                    borderWidth: 3,
                                    backgroundColor: gradient,
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: '#fff',
                                    pointBorderColor: '#2563eb',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                                    x: { grid: { display: false } }
                                }
                            }
                        });
                    });
            }

            function updateChart(type, btn) {
                document.querySelectorAll('.chart-toggle').forEach(b => b.classList.remove('active', 'btn-primary'));
                document.querySelectorAll('.chart-toggle').forEach(b => b.classList.add('btn-light'));
                btn.classList.add('active', 'btn-primary');
                btn.classList.remove('btn-light');
                initReportsChart(type);
            }

            function updateReportYear(year) {
                initReportsChart('monthly', year);
                // Switch button active state to Monthly if changing year
                const monthlyBtn = document.querySelector('.chart-toggle:nth-child(2)');
                if (monthlyBtn) updateChart('monthly', monthlyBtn);
            }

            document.addEventListener('DOMContentLoaded', () => initReportsChart('weekly'));
        </script>
    <?php endif; ?>


    <!-- ADMINS PAGE -->
    <?php if ($page === 'admins'): ?>
        <div class="row align-items-center mb-5 g-3">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Manajemen Tim</h2>
                <p class="text-muted mb-0">Kelola akses dan otoritas admin platform MCM.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn btn-primary px-4 shadow-sm rounded-pill" onclick="resetAdminForm(); showModal('adminModal');">
                    <i class="fas fa-user-plus me-2"></i>Tambah Admin
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th class="ps-4">Admin ID</th>
                                <th>Username</th>
                                <th>Terdaftar Pada</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admins as $ad): ?>
                                <tr>
                                    <td class="ps-4 text-secondary fw-bold">#<?php echo $ad['id']; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 38px; height: 38px;">
                                                <i class="fas fa-user-shield"></i>
                                            </div>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($ad['username']); ?></div>
                                        </div>
                                    </td>
                                    <td class="text-muted small"><?php echo date('d M Y, H:i', strtotime($ad['created_at'])); ?></td>
                                    <td class="text-end pe-4">
                                        <?php if ($ad['username'] !== $_SESSION['admin_username']): ?>
                                            <div class="d-inline-flex gap-2">
                                                <button class="btn btn-action btn-soft-primary" onclick="editAdmin(<?php echo htmlspecialchars(json_encode($ad)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-action btn-soft-danger" onclick="deleteItem('admins', <?php echo $ad['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <span class="badge badge-soft-primary">Anda</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- SETTINGS PAGE -->
    <?php if ($page === 'settings'): ?>
        <div class="mb-5">
            <h2 class="fw-bold mb-1 text-dark">Konfigurasi Platform</h2>
            <p class="text-muted">Kelola identitas, kontak, dan informasi publik MCM.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm overflow-hidden">
                    <div class="card-header bg-white py-4 px-4 border-bottom-0">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <h5 class="fw-bold mb-0 text-dark">Informasi Publik & Kontak</h5>
                        </div>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <form action="<?php echo $adminBase; ?>/actions/save_settings.php" method="POST" class="ajax-form">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">WhatsApp Bisnis</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="fab fa-whatsapp text-success"></i></span>
                                        <input type="text" class="form-control bg-light border-0 py-2" name="admin_whatsapp" value="6285793935707">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Email Official</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-envelope text-primary"></i></span>
                                        <input type="email" class="form-control bg-light border-0 py-2" name="admin_email" value="info@mcm.com">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Alamat Operasional</label>
                                    <textarea class="form-control bg-light border-0 py-2" name="admin_address" rows="3">Kota Sukabumi, Jawa Barat</textarea>
                                </div>
                                <div class="col-12">
                                    <h6 class="fw-bold text-dark mt-3 mb-3">Tautan Media Sosial</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-muted">Instagram</label>
                                            <input type="text" class="form-control bg-light border-0" name="social_ig" value="#">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-muted">Facebook</label>
                                            <input type="text" class="form-control bg-light border-0" name="social_fb" value="#">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold text-muted">TikTok</label>
                                            <input type="text" class="form-control bg-light border-0" name="social_tt" value="#">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 pt-4 border-top text-end">
                                <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm">Simpan Konfigurasi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                    <div class="card-header bg-white py-4 px-4 border-bottom-0">
                        <h6 class="fw-bold mb-0 text-dark">Identitas Visual</h6>
                    </div>
                    <div class="card-body p-4 pt-0 text-center">
                        <div class="mb-4 bg-light p-4 rounded-4 border border-dashed">
                            <img src="../assets/img/logo.png" alt="MCM Logo" class="mb-3" style="max-height: 80px;">
                            <p class="small text-muted mb-0">Logo saat ini (.png)</p>
                        </div>
                        <form action="<?php echo $adminBase; ?>/actions/save_settings.php" method="POST" enctype="multipart/form-data" class="ajax-form">
                            <div class="mb-3">
                                <input type="file" class="form-control" name="logo" accept="image/png">
                            </div>
                            <button type="submit" class="btn btn-light w-100 rounded-pill fw-bold">Update Logo</button>
                        </form>
                    </div>
                </div>
                <div class="card border-0 shadow-sm bg-dark text-white">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-shield-alt text-warning me-2"></i>
                            <h6 class="fw-bold mb-0">Keamanan</h6>
                        </div>
                        <p class="small text-white-50 mb-4">Ganti kata sandi admin secara berkala untuk menjaga keamanan data.</p>
                        <button class="btn btn-outline-light w-100 rounded-pill" onclick="showModal('changePasswordModal')">Ganti Password</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- CATEGORIES PAGE -->
    <?php if ($page === 'categories'): ?>
        <div class="row align-items-center mb-5 g-3" data-aos="fade-down">
            <div class="col-md-6">
                <h2 class="fw-bold mb-1 text-dark">Kategori Pelatihan</h2>
                <p class="text-muted mb-0">Kelola jenis-jenis pelatihan yang tersedia di MCM.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn btn-primary px-4 shadow-sm rounded-pill" onclick="resetCategoryForm(); showModal('categoryModal');">
                    <i class="fas fa-plus me-2"></i>Tambah Kategori
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Nama Kategori</th>
                                <th>Slug</th>
                                <th>Tgl Dibuat</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($categories)): ?>
                                <tr><td colspan="4" class="text-center py-5 text-muted">Belum ada kategori.</td></tr>
                            <?php else: ?>
                                <?php foreach ($categories as $cat): ?>
                                    <tr>
                                        <td class="ps-4"><span class="fw-bold text-dark"><?php echo htmlspecialchars($cat['name']); ?></span></td>
                                        <td><code><?php echo htmlspecialchars($cat['slug']); ?></code></td>
                                        <td><?php echo date('d M Y', strtotime($cat['created_at'])); ?></td>
                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex gap-2">
                                                <button class="btn btn-action btn-soft-primary" onclick="editCategory(<?php echo htmlspecialchars(json_encode($cat)); ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-action btn-soft-danger" onclick="deleteItem('categories', <?php echo $cat['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>


<!-- Detail Pesanan Modal -->
<div class="modal fade" id="detailPesananModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1.5rem;">
            <div class="modal-header bg-light border-0 py-3 px-4">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-info-circle me-2 text-info"></i>Detail Data Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <img src="../assets/img/logo.png" alt="MCM Logo" style="height: 50px; width: auto;" class="mb-2">
                    <h6 class="fw-bold text-primary mb-0">Mitra Cipta Mandiri</h6>
                    <small class="text-muted" id="detailOrderNum"></small>
                </div>
                
                <div class="row g-3">
                    <div class="col-6">
                        <label class="small text-muted d-block">Nama Lengkap</label>
                        <span class="fw-bold text-dark" id="detailName"></span>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted d-block">No. WhatsApp</label>
                        <span class="fw-bold text-dark" id="detailPhone"></span>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted d-block">Alamat Email</label>
                        <span class="fw-bold text-dark" id="detailEmail"></span>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted d-block">Asal Instansi/Sekolah</label>
                        <span class="fw-bold text-dark" id="detailInstansi"></span>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted d-block">Alamat Lengkap</label>
                        <p class="fw-medium text-dark mb-0" id="detailAlamat"></p>
                    </div>
                    <div class="col-12 border-top pt-3">
                        <div class="bg-light p-3 rounded-3 d-flex justify-content-between align-items-center">
                            <div>
                                <label class="small text-muted d-block">Kelas Pilihan</label>
                                <span class="fw-bold text-primary" id="detailKelas"></span>
                            </div>
                            <div class="text-end">
                                <label class="small text-muted d-block">Total Bayar</label>
                                <span class="fw-bold text-dark">Rp <span id="detailHarga"></span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-secondary w-100 py-2 rounded-pill fw-bold" data-bs-dismiss="modal">Tutup Detail</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Update Status Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-4">Ubah status pesanan untuk <strong id="modalParticipantName" class="text-dark"></strong>.</p>
                <form id="updateStatusForm" action="<?php echo $adminBase; ?>/actions/update_order.php" method="POST" class="ajax-form">
                    <input type="hidden" name="order_id" id="modalOrderId">
                    <div class="mb-4">
                        <select class="form-select form-control-lg" name="status" id="modalOrderStatus" required>
                            <option value="pending">Pending (Menunggu Pembayaran)</option>
                            <option value="confirmed">Lunas (Confirmed)</option>
                            <option value="cancelled">Dibatalkan</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Class Modal -->
<div class="modal fade" id="classModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="classModalTitle">Tambah Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="classForm" action="<?php echo $adminBase; ?>/actions/manage_classes.php" method="POST" enctype="multipart/form-data" onsubmit="submitAjaxForm('classForm'); return false;">
                    <input type="hidden" name="action" id="classAction" value="create">
                    <input type="hidden" name="id" id="classId">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Nama Kelas</label>
                            <input type="text" class="form-control" name="name" id="className" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Tanggal Mulai Pelatihan</label>
                            <input type="date" class="form-control" name="start_date" id="classStartDate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Kategori</label>
                            <select class="form-select" name="category" id="classCategory" required>
                                <option value="">Pilih Kategori...</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat['name']); ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Harga (Rp)</label>
                            <input type="number" class="form-control" name="price" id="classPrice" placeholder="Misal: 500000" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium">Deskripsi</label>
                            <textarea class="form-control" name="description" id="classDescription" rows="3" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium">Fitur / Materi (Pisahkan dengan koma)</label>
                            <input type="text" class="form-control" name="features" id="classFeatures" placeholder="Materi 1, Materi 2, Sertifikat..." required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium">Gambar Cover</label>
                            <input type="file" class="form-control" name="image" id="classImage" accept="image/*">
                            <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengubah gambar (saat edit).</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 px-4 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" onclick="submitAjaxForm('classForm')">Simpan Kelas</button>
            </div>
        </div>
    </div>
</div>

<!-- Gallery Modal -->
<div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="galleryModalTitle">Tambah Foto Galeri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="galleryForm" action="<?php echo $adminBase; ?>/actions/manage_gallery.php" method="POST" enctype="multipart/form-data" onsubmit="submitAjaxForm('galleryForm'); return false;">
                    <input type="hidden" name="action" value="create" id="galleryAction">
                    <input type="hidden" name="id" id="galleryId">
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Judul Foto (Akan digunakan untuk semua foto)</label>
                        <input type="text" class="form-control" name="title" id="galleryTitle" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-medium">Filter / Kategori</label>
                         <select class="form-select" name="category" required id="galleryCategory">
                            <option value="all">Umum</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['slug']); ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-medium">File Gambar (Bisa pilih banyak sekaligus)</label>
                        <input type="file" class="form-control" name="images[]" id="galleryImageInput" accept="image/*" required multiple>
                        <small class="text-muted">Gunakan tombol Ctrl (Windows) atau Command (Mac) untuk memilih lebih dari 1 foto.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 px-4 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" id="gallerySubmitBtn" onclick="submitAjaxForm('galleryForm')">Upload Foto</button>
            </div>
        </div>
    </div>
</div>
<!-- Gallery Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="categoryModalTitle">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="categoryForm" action="<?php echo $adminBase; ?>/actions/manage_categories.php" method="POST" onsubmit="submitAjaxForm('categoryForm'); return false;">
                    <input type="hidden" name="action" value="create" id="categoryAction">
                    <input type="hidden" name="id" id="categoryId">
                    <div class="mb-2">
                        <label class="form-label small fw-medium">Nama Kategori</label>
                        <input type="text" class="form-control" name="name" id="categoryName" placeholder="Contoh: Digital Marketing" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 px-4 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" id="categorySubmitBtn" onclick="submitAjaxForm('categoryForm')">Simpan Kategori</button>
            </div>
        </div>
    </div>
</div>

<!-- Admin Modal -->
<div class="modal fade" id="adminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="adminModalTitle">Tambah Admin Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="adminForm" action="<?php echo $adminBase; ?>/actions/manage_admins.php" method="POST" onsubmit="submitAjaxForm('adminForm'); return false;">
                    <input type="hidden" name="action" value="create" id="adminAction">
                    <input type="hidden" name="id" id="adminId">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Username Admin</label>
                        <input type="text" class="form-control" name="username" id="adminUsername" required placeholder="Contoh: admin_mcm">
                    </div>
                    <div class="mb-1">
                        <label class="form-label small fw-bold" id="adminPasswordLabel">Password Baru</label>
                        <input type="password" class="form-control" name="password" id="adminPassword" required placeholder="Minimal 6 karakter">
                        <small class="text-muted" id="adminPasswordHint">Biarkan kosong jika tidak ingin mengubah password.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 px-4 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" id="adminSubmitBtn" onclick="submitAjaxForm('adminForm')">Buat Akun</button>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Ganti Password Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="changePasswordForm" action="<?php echo $adminBase; ?>/actions/manage_admins.php" method="POST" onsubmit="submitAjaxForm('changePasswordForm'); return false;">
                    <input type="hidden" name="action" value="change_password">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password Baru</label>
                        <input type="password" class="form-control" name="new_password" required placeholder="Minimal 6 karakter">
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 px-4 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" onclick="submitAjaxForm('changePasswordForm')">Simpan Password</button>
            </div>
        </div>
    </div>
</div>

<!-- Instructor Modal -->
<div class="modal fade" id="instructorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="instructorModalTitle">Tambah Instruktur / Penguji</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="instructorForm" action="<?php echo $adminBase; ?>/actions/manage_instructors.php" method="POST" enctype="multipart/form-data" onsubmit="submitAjaxForm('instructorForm'); return false;">
                    <input type="hidden" name="action" id="instructorAction" value="create">
                    <input type="hidden" name="id" id="instructorId">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" class="form-control" name="name" id="instructorName" required placeholder="Gunakan gelar jika ada">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Spesialisasi / Gelar</label>
                        <input type="text" class="form-control" name="specialization" id="instructorSpec" required placeholder="Contoh: Ahli Tata Rias">
                    </div>
                    <div class="mb-1">
                        <label class="form-label small fw-bold">Foto Profil</label>
                        <input type="file" class="form-control" name="image" id="instructorImage" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 px-4 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" onclick="submitAjaxForm('instructorForm')">Simpan Data</button>
            </div>
        </div>
    </div>
</div>

<!-- Cert Modal -->
<div class="modal fade" id="certModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold" id="certModalTitle">Tambah Dokumen Sertifikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="certForm" action="<?php echo $adminBase; ?>/actions/manage_certs.php" method="POST" enctype="multipart/form-data" onsubmit="submitAjaxForm('certForm'); return false;">
                    <input type="hidden" name="action" id="certAction" value="create">
                    <input type="hidden" name="id" id="certId">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Dokumen</label>
                        <input type="text" class="form-control" name="title" id="certTitle" required placeholder="Contoh: Izin LPK MCM">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Keterangan Singkat</label>
                        <textarea class="form-control" name="description" id="certDesc" rows="2" placeholder="Detail singkat mengenai dokumen ini"></textarea>
                    </div>
                    <div class="mb-1">
                        <label class="form-label small fw-bold">Scan Dokumen (Gambar)</label>
                        <input type="file" class="form-control" name="image" id="certImage" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah dokumen.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 px-4 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" onclick="submitAjaxForm('certForm')">Simpan Dokumen</button>
            </div>
        </div>
    </div>
</div>

</div> <!-- End of main-content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Mobile Sidebar Toggle
document.getElementById('openSidebar')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.add('show');
});
document.getElementById('closeSidebar')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.remove('show');
});

// Setup Modals Data
const updateStatusModal = document.getElementById('updateStatusModal');
if (updateStatusModal) {
    updateStatusModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('modalOrderId').value = button.getAttribute('data-id');
        document.getElementById('modalParticipantName').textContent = button.getAttribute('data-name');
        document.getElementById('modalOrderStatus').value = button.getAttribute('data-status');
    });
}

const detailPesananModal = document.getElementById('detailPesananModal');
if (detailPesananModal) {
    detailPesananModal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        if (!btn) return;
        
        const map = {
            'detailOrderNum': 'data-order',
            'detailName': 'data-name',
            'detailPhone': 'data-phone',
            'detailEmail': 'data-email',
            'detailInstansi': 'data-instansi',
            'detailAlamat': 'data-alamat',
            'detailKelas': 'data-kelas',
            'detailHarga': 'data-harga'
        };

        for (const [id, attr] of Object.entries(map)) {
            const el = document.getElementById(id);
            if (el) el.textContent = btn.getAttribute(attr) || '-';
        }
    });
}

function resetClassForm() {
    document.getElementById('classForm').reset();
    document.getElementById('classAction').value = 'create';
    document.getElementById('classId').value = '';
    document.getElementById('classModalTitle').textContent = 'Tambah Kelas';
    document.getElementById('classImage').required = true;
}

function editClass(data) {
    resetClassForm();
    document.getElementById('classAction').value = 'update';
    document.getElementById('classId').value = data.id;
    document.getElementById('className').value = data.name;
    document.getElementById('classStartDate').value = data.start_date || '';
    document.getElementById('classCategory').value = data.category;
    document.getElementById('classPrice').value = data.price || '';
    document.getElementById('classDescription').value = data.description;
    
    // Parse features from JSON array
    try {
        let features = data.features;
        if (typeof features === 'string' && (features.startsWith('[') || features.startsWith('{'))) {
            features = JSON.parse(features);
        }
        document.getElementById('classFeatures').value = Array.isArray(features) ? features.join(', ') : features;
    } catch(e) {
        document.getElementById('classFeatures').value = data.features;
    }
    
    document.getElementById('classImage').required = false;
    document.getElementById('classModalTitle').textContent = 'Edit Paket Pelatihan';
    
    new bootstrap.Modal(document.getElementById('classModal')).show();
}

function editInstructor(data) {
    document.getElementById('instructorForm').reset();
    document.getElementById('instructorAction').value = 'update';
    document.getElementById('instructorId').value = data.id;
    document.getElementById('instructorName').value = data.name;
    document.getElementById('instructorSpec').value = data.specialization;
    document.getElementById('instructorImage').required = false;
    document.getElementById('instructorModalTitle').textContent = 'Edit Data Instruktur';
    new bootstrap.Modal(document.getElementById('instructorModal')).show();
}

function editCert(data) {
    document.getElementById('certForm').reset();
    document.getElementById('certAction').value = 'update';
    document.getElementById('certId').value = data.id;
    document.getElementById('certTitle').value = data.title;
    document.getElementById('certDesc').value = data.description || '';
    document.getElementById('certImage').required = false;
    document.getElementById('certModalTitle').textContent = 'Edit Dokumen Legal';
    new bootstrap.Modal(document.getElementById('certModal')).show();
}

function submitAjaxForm(formId) {
    const form = document.getElementById(formId);
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    const submitBtn = document.querySelector(`button[onclick="submitAjaxForm('${formId}')"]`);
    const originalBtnText = submitBtn ? submitBtn.innerHTML : '';

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
    }

    fetch(form.getAttribute('action'), {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(text => {
        let data;
        try {
            data = JSON.parse(text);
        } catch(e) {
            throw new Error('Respons server tidak valid: ' + (text || '(kosong)').slice(0, 150));
        }
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message
            });
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: err.message || 'Gagal menghubungi server.'
        });
    })
    .finally(() => {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    });
}

function resetGalleryForm() {
    const form = document.getElementById('galleryForm');
    if (form) form.reset();
    document.getElementById('galleryAction').value = 'create';
    document.getElementById('galleryId').value = '';
    document.getElementById('galleryModalTitle').textContent = 'Tambah Foto Galeri';
    document.getElementById('galleryImageInput').required = true;
    document.getElementById('galleryImageInput').setAttribute('multiple', 'multiple');
    document.getElementById('gallerySubmitBtn').textContent = 'Upload Foto';
}

function editGallery(data) {
    resetGalleryForm();
    document.getElementById('galleryAction').value = 'update';
    document.getElementById('galleryId').value = data.id;
    document.getElementById('galleryTitle').value = data.title;
    document.getElementById('galleryCategory').value = data.category;
    document.getElementById('galleryImageInput').required = false;
    document.getElementById('galleryImageInput').removeAttribute('multiple');
    document.getElementById('galleryModalTitle').textContent = 'Edit Foto';
    document.getElementById('gallerySubmitBtn').textContent = 'Simpan Perubahan';
    new bootstrap.Modal(document.getElementById('galleryModal')).show();
}

function resetCategoryForm() {
    const form = document.getElementById('categoryForm');
    if (form) form.reset();
    document.getElementById('categoryAction').value = 'create';
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryModalTitle').textContent = 'Tambah Kategori Baru';
    document.getElementById('categorySubmitBtn').textContent = 'Simpan Kategori';
}

function editCategory(data) {
    resetCategoryForm();
    document.getElementById('categoryAction').value = 'update';
    document.getElementById('categoryId').value = data.id;
    document.getElementById('categoryName').value = data.name;
    document.getElementById('categoryModalTitle').textContent = 'Edit Kategori';
    document.getElementById('categorySubmitBtn').textContent = 'Simpan Perubahan';
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}

function resetAdminForm() {
    const form = document.getElementById('adminForm');
    if (form) form.reset();
    document.getElementById('adminAction').value = 'create';
    document.getElementById('adminId').value = '';
    document.getElementById('adminModalTitle').textContent = 'Tambah Admin Baru';
    document.getElementById('adminPasswordLabel').textContent = 'Password Baru';
    document.getElementById('adminPassword').required = true;
    document.getElementById('adminPasswordHint').style.display = 'none';
    document.getElementById('adminSubmitBtn').textContent = 'Buat Akun';
}

function editAdmin(data) {
    resetAdminForm();
    document.getElementById('adminAction').value = 'update';
    document.getElementById('adminId').value = data.id;
    document.getElementById('adminUsername').value = data.username;
    document.getElementById('adminPassword').required = false;
    document.getElementById('adminPasswordLabel').textContent = 'Password Baru (opsional)';
    document.getElementById('adminPasswordHint').style.display = '';
    document.getElementById('adminModalTitle').textContent = 'Edit Data Admin';
    document.getElementById('adminSubmitBtn').textContent = 'Simpan Perubahan';
    new bootstrap.Modal(document.getElementById('adminModal')).show();
}

function deleteItem(type, id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            let endpoint = '';
            const adminBase = '<?php echo $adminBase; ?>';
            switch(type) {
                case 'classes': endpoint = adminBase + '/actions/manage_classes.php'; break;
                case 'gallery': endpoint = adminBase + '/actions/manage_gallery.php'; break;
                case 'instructors': endpoint = adminBase + '/actions/manage_instructors.php'; break;
                case 'certs': endpoint = adminBase + '/actions/manage_certs.php'; break;
                case 'admins': endpoint = adminBase + '/actions/manage_admins.php'; break;
                case 'orders': endpoint = adminBase + '/actions/manage_orders.php'; break;
                case 'categories': endpoint = adminBase + '/actions/manage_categories.php'; break;
            }
            
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', id);
            
            fetch(endpoint, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    Swal.fire('Terhapus!', data.message, 'success').then(() => window.location.reload());
                } else {
                    Swal.fire('Gagal!', data.message, 'error');
                }
            });
        }
    });
}

// AJAX Form Submission
document.querySelectorAll('.ajax-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

        fetch(this.getAttribute('action'), {
            method: this.getAttribute('method') || 'POST',
            body: new FormData(this)
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire('Berhasil!', data.message, 'success').then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Gagal', data.message, 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        })
        .catch(err => {
            Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
});
</script>

<style>
    /* Premium Bottom Navbar Styling - Matching User Request */
    .mcm-bottom-nav {
        display: none; /* Default hidden for desktop */
    }

    @media (max-width: 991px) {
        .mcm-bottom-nav {
            display: flex !important;
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(20px) !important;
            height: 70px !important;
            justify-content: space-around !important;
            align-items: center !important;
            z-index: 999999 !important;
            box-shadow: 0 -10px 25px rgba(0, 0, 0, 0.05) !important;
            border-top: 1px solid rgba(0,0,0,0.05) !important;
            padding-bottom: env(safe-area-inset-bottom) !important;
        }

        .mcm-nav-item {
            position: relative;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            text-decoration: none !important;
            color: #64748b !important; 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
            flex: 1 !important;
            z-index: 2;
        }

        /* Active Indicator Glow */
        .mcm-nav-item.active {
            color: #2563eb !important;
            transform: translateY(-8px);
        }

        .mcm-nav-item.active .icon-wrapper {
            background: linear-gradient(135deg, #2563eb, #3b82f6) !important;
            color: #ffffff !important;
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3) !important;
            transform: scale(1.1);
        }

        /* Continuous Motion for Active Icon */
        @keyframes float-active {
            0% { transform: translateY(0) scale(1.1); }
            50% { transform: translateY(-5px) scale(1.15); }
            100% { transform: translateY(0) scale(1.1); }
        }

        .mcm-nav-item.active .icon-wrapper {
            animation: float-active 3s ease-in-out infinite !important;
        }

        .mcm-nav-item .icon-wrapper {
            width: 42px !important;
            height: 42px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 14px !important;
            font-size: 1.2rem !important;
            margin-bottom: 4px !important;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
        }

        /* Idle Animation */
        @keyframes breathe {
            0% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); opacity: 0.8; }
        }

        .mcm-nav-item:not(.active) .icon-wrapper {
            animation: breathe 3s ease-in-out infinite;
        }

        .mcm-nav-item span {
            font-size: 0.6rem !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            opacity: 0.5;
            transition: all 0.3s ease;
        }

        .mcm-nav-item.active span {
            opacity: 1 !important;
            color: #1e293b !important;
        }

        /* Custom Progress for AJAX */
        .ajax-progress {
            position: fixed;
            top: 0; left: 0;
            height: 4px;
            background: linear-gradient(90deg, #2563eb, #7c3aed, #db2777);
            z-index: 999999;
            width: 0;
            transition: width 0.3s ease;
            box-shadow: 0 0 15px rgba(37, 99, 235, 0.5);
        }
    }
</style>
<div class="ajax-progress" id="ajaxProgress"></div>

<script>
// INSTANT AJAX NAVIGATION (No Reload, No Flicker)
function initAjaxLinks() {
    document.querySelectorAll('a').forEach(link => {
        const href = link.getAttribute('href');
        if (href && href.startsWith('?page=') && !link.dataset.ajaxBound) {
            link.dataset.ajaxBound = "true";
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                loadContent(url, true);
            });
        }
    });
}

async function loadContent(url, pushState = true) {
    const progress = document.getElementById('ajaxProgress');
    try {
        if (progress) {
            progress.style.width = '0%';
            progress.style.display = 'block';
            setTimeout(() => progress.style.width = '30%', 10);
        }

        const pageName = new URLSearchParams(url).get('page');
        updateActiveStates(pageName);

        const response = await fetch(url);
        if (progress) progress.style.width = '70%';
        
        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        const newMain = doc.querySelector('.main-content');
        if (newMain) {
            const mainContainer = document.querySelector('.main-content');
            mainContainer.innerHTML = newMain.innerHTML;
            if (pushState) history.pushState({page: pageName}, '', url);
            
            if (progress) {
                progress.style.width = '100%';
                setTimeout(() => progress.style.display = 'none', 300);
            }

            // Execute scripts in the new content (must be from the live DOM)
            mainContainer.querySelectorAll('script').forEach(oldScript => {
                const newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });

            // Re-init AOS and Counters
            if (typeof AOS !== 'undefined') AOS.refreshHard();
            initCounters();
            initAjaxLinks();
            reinitBootstrapModals();
            
            // Re-init Reports if on reports page
            if (pageName === 'reports' && typeof initReportsChart === 'function') {
                initReportsChart('weekly');
            }
            
            window.scrollTo(0, 0);
        }
    } catch (err) {
        if (progress) progress.style.display = 'none';
        window.location.href = url;
    }
}

function updateActiveStates(page) {
    document.querySelectorAll('.nav-link, .mcm-nav-item').forEach(el => {
        const href = el.getAttribute('href');
        if (href && href.includes(`page=${page}`)) {
            el.classList.add('active');
        } else {
            el.classList.remove('active');
        }
    });
}

function initCounters() {
    document.querySelectorAll('.counter-value').forEach(counter => {
        const target = +counter.getAttribute('data-target');
        const duration = 1500;
        const increment = target / (duration / 16);
        
        let current = 0;
        const updateCount = () => {
            current += increment;
            if (current < target) {
                counter.innerText = Math.ceil(current);
                requestAnimationFrame(updateCount);
            } else {
                counter.innerText = target;
            }
        };
        updateCount();
    });
}

window.addEventListener('popstate', (e) => {
    if (e.state && e.state.page) {
        loadContent(`?page=${e.state.page}`, false);
    } else {
        window.location.reload();
    }
});

// Initial Load
document.addEventListener('DOMContentLoaded', () => {
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            once: true,
            offset: 50
        });
    }
    initCounters();
    initAjaxLinks();
    reinitBootstrapModals();
});
</script>

<script>
function reinitBootstrapModals() {
    // Re-attach data-bs-toggle modal triggers after AJAX content swap
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(btn => {
        if (btn.dataset.modalBound) return;
        btn.dataset.modalBound = 'true';
        btn.addEventListener('click', function() {
            const target = this.getAttribute('data-bs-target');
            if (target) showModal(target.replace('#', ''));
        });
    });

    // Re-attach updateStatus modal events
    const updateStatusModal = document.getElementById('updateStatusModal');
    if (updateStatusModal && !updateStatusModal.dataset.bound) {
        updateStatusModal.dataset.bound = 'true';
        updateStatusModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button) return;
            document.getElementById('modalOrderId').value = button.getAttribute('data-id');
            document.getElementById('modalParticipantName').textContent = button.getAttribute('data-name');
            document.getElementById('modalOrderStatus').value = button.getAttribute('data-status');
        });
    }

    // Re-attach detail pesanan modal events
    const detailPesananModal = document.getElementById('detailPesananModal');
    if (detailPesananModal && !detailPesananModal.dataset.bound) {
        detailPesananModal.dataset.bound = 'true';
        detailPesananModal.addEventListener('show.bs.modal', function(event) {
            const btn = event.relatedTarget;
            if (!btn) return;
            const map = {
                'detailOrderNum': 'data-order', 'detailName': 'data-name',
                'detailPhone': 'data-phone', 'detailEmail': 'data-email',
                'detailInstansi': 'data-instansi', 'detailAlamat': 'data-alamat',
                'detailKelas': 'data-kelas', 'detailHarga': 'data-harga'
            };
            for (const [id, attr] of Object.entries(map)) {
                const el = document.getElementById(id);
                if (el) el.textContent = btn.getAttribute(attr) || '-';
            }
        });
    }
}

function showModal(id) {
    const el = document.getElementById(id);
    if (el) {
        const modal = bootstrap.Modal.getOrCreateInstance(el);
        modal.show();
    }
}
</script>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

</body>
</html>
