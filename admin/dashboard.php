<?php
session_start();
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../auth/admin_login.php");
    exit;
}

require_once '../config/database.php';

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
        $stats['revenue'] = (int) $pdo->query("SELECT COALESCE(SUM(amount),0) FROM orders WHERE payment_status='paid'")->fetchColumn();
        $stats['paid_orders'] = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status='paid'")->fetchColumn();
        $stats['waiting_payment'] = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status IN ('unpaid','pending')")->fetchColumn();
        $stats['failed_orders'] = $pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status IN ('failed','expired')")->fetchColumn();
        
        $stmt = $pdo->query("SELECT o.*, c.name as class_name FROM orders o JOIN classes c ON o.class_id = c.id ORDER BY o.created_at DESC LIMIT 5");
        $recent_orders = $stmt->fetchAll();

        // Data for Chart: Registrations by Month (Last 6 Months)
        $chart_data = $pdo->query("SELECT DATE_FORMAT(MIN(created_at), '%M') as month, COUNT(*) as count 
                                   FROM orders 
                                   WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                                   GROUP BY YEAR(created_at), MONTH(created_at)
                                   ORDER BY MIN(created_at) ASC")->fetchAll(PDO::FETCH_ASSOC);
        
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
        $sql = "SELECT o.*, c.name as class_name, c.category as class_category, ins.name as instructor_name 
                FROM orders o 
                JOIN classes c ON o.class_id = c.id
                LEFT JOIN instructors ins ON o.instructor_id = ins.id";
        
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
if ($page === 'classes' || $page === 'materials' || $page === 'certs') {
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
$certTemplates = [];
if ($page === 'certs') {
    try {
        $stmt = $pdo->query("SELECT * FROM certifications ORDER BY created_at DESC");
        $certs = $stmt->fetchAll();
    } catch (PDOException $e) {}

    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `certificate_templates` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(150) NOT NULL,
          `class_id` int(11) DEFAULT NULL,
          `layout` varchar(50) NOT NULL DEFAULT 'default',
          `bg_image` varchar(255) DEFAULT NULL,
          `accent_color` varchar(20) DEFAULT NULL,
          `is_default` tinyint(1) NOT NULL DEFAULT 0,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        $certTemplates = $pdo->query("SELECT ct.*, c.name AS class_name FROM certificate_templates ct LEFT JOIN classes c ON ct.class_id = c.id ORDER BY ct.created_at DESC")->fetchAll();
    } catch (PDOException $e) {}
}

// Fetch Categories
$categories = [];
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `class_categories` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) NOT NULL,
      `slug` varchar(100) NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `slug` (`slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    $stmt = $pdo->query("SELECT * FROM class_categories ORDER BY name ASC");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {}

// Fetch Testimonials
$testimonials = [];
$avgRating = 0;
if ($page === 'testimonials') {
    try {
        $testimonials = $pdo->query("SELECT t.*, c.name AS class_name FROM testimonials t LEFT JOIN classes c ON t.class_id = c.id ORDER BY t.created_at DESC")->fetchAll();
        $avg = $pdo->query("SELECT AVG(rating) FROM testimonials WHERE status = 'approved'")->fetchColumn();
        $avgRating = $avg ? round((float)$avg, 1) : 0;
    } catch (PDOException $e) {}
}

// Fetch Users (Peserta Terdaftar)
$users = [];
if ($page === 'users') {
    try {
        $users = $pdo->query("SELECT u.*, 
            (SELECT COUNT(*) FROM enrollments e WHERE e.user_id = u.id) AS total_kelas,
            (SELECT GROUP_CONCAT(c.name SEPARATOR ', ') FROM enrollments e JOIN classes c ON e.class_id = c.id WHERE e.user_id = u.id) AS enrolled_classes
            FROM users u ORDER BY u.created_at DESC")->fetchAll();
    } catch (PDOException $e) {}
}

// Fetch Materials
$materials = [];
if ($page === 'materials') {
    try {
        $materials = $pdo->query("SELECT m.*, c.name AS class_name FROM materials m JOIN classes c ON m.class_id = c.id ORDER BY m.class_id ASC, m.sort_order ASC, m.id ASC")->fetchAll();
    } catch (PDOException $e) {}
}

// Fetch Chat Messages
$chats = [];
$threadNumber = '';
if ($page === 'chat') {
    $threadNumber = preg_replace('/\D+/', '', $_GET['thread'] ?? '');
    try {
        $chats = $pdo->query("SELECT * FROM chat_messages ORDER BY created_at DESC, id DESC LIMIT 500")->fetchAll();
    } catch (PDOException $e) {}
}

// Fetch Chatbot Intents
$chatbotIntents = [];
if ($page === 'chatbot') {
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `chatbot_intents` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `intent` varchar(50) NOT NULL UNIQUE,
          `keywords` text NOT NULL,
          `reply` text NOT NULL,
          `enabled` tinyint(1) NOT NULL DEFAULT 1,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        require_once __DIR__ . '/../includes/whatsapp_client.php';
        wa_seed_intent_defaults($pdo);
        $chatbotIntents = $pdo->query("SELECT * FROM chatbot_intents ORDER BY id ASC")->fetchAll();
    } catch (PDOException $e) {}
}

// Auto-create finance_transactions (rekap keuangan)
if ($page === 'finance') {
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `finance_transactions` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `type` ENUM('in','out') NOT NULL,
          `category` varchar(50) NOT NULL,
          `description` text DEFAULT NULL,
          `amount` int(11) NOT NULL,
          `transaction_date` date NOT NULL,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    } catch (PDOException $e) {}
}

// Fetch Facility Locations & Categories
$facilities = [];
$facilityCategories = [];
if ($page === 'facilities') {
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `facility_categories` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `slug` varchar(50) NOT NULL UNIQUE,
          `name` varchar(100) NOT NULL,
          `icon` varchar(50) NOT NULL DEFAULT 'fa-building',
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS `facility_locations` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `category` varchar(50) NOT NULL,
          `title` varchar(255) NOT NULL,
          `image` varchar(255) NOT NULL,
          `description` text DEFAULT NULL,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Seed default categories if empty
        $catCount = (int)$pdo->query("SELECT COUNT(*) FROM facility_categories")->fetchColumn();
        if ($catCount === 0) {
            $defaultCats = [
                ['alat_pemadam', 'Alat Pemadam', 'fa-fire-extinguisher'],
                ['balkon',       'Balkon',       'fa-door-open'],
                ['kantor',       'Kantor',       'fa-building'],
                ['kelas',        'Kelas',        'fa-chalkboard-teacher'],
                ['lobby',        'Lobby',        'fa-couch'],
                ['mushola',      'Mushola',      'fa-mosque'],
                ['parkiran',     'Parkiran',     'fa-parking'],
                ['toilet',       'Toilet',       'fa-restroom']
            ];
            $ins = $pdo->prepare("INSERT IGNORE INTO facility_categories (slug, name, icon) VALUES (?, ?, ?)");
            foreach ($defaultCats as $d) {
                $ins->execute($d);
            }
        }

        $count = (int)$pdo->query("SELECT COUNT(*) FROM facility_locations")->fetchColumn();
        if ($count === 0) {
            $baseDir = dirname(__DIR__) . '/assets/img/fasilitas';
            $cats = [
                'alat_pemadam' => 'Alat Pemadam',
                'balkon'       => 'Balkon',
                'kantor'       => 'Kantor',
                'kelas'        => 'Kelas',
                'lobby'        => 'Lobby',
                'mushola'      => 'Mushola',
                'parkiran'     => 'Parkiran',
                'toilet'       => 'Toilet'
            ];
            foreach ($cats as $slug => $cName) {
                $dir = $baseDir . '/' . $slug;
                if (is_dir($dir)) {
                    $files = scandir($dir);
                    $num = 1;
                    foreach ($files as $f) {
                        if ($f !== '.' && $f !== '..' && preg_match('/\.(jpe?g|png|webp)$/i', $f)) {
                            $imgPath = 'assets/img/fasilitas/' . $slug . '/' . $f;
                            $t = 'Foto ' . $cName . ' MCM #' . $num;
                            $stmt = $pdo->prepare("INSERT INTO facility_locations (category, title, image, description) VALUES (?, ?, ?, ?)");
                            $stmt->execute([$slug, $t, $imgPath, 'Dokumentasi area ' . strtolower($cName) . ' LPK Mitra Cipta Mandiri']);
                            $num++;
                        }
                    }
                }
            }
        }

        $facilityCategories = $pdo->query("SELECT * FROM facility_categories ORDER BY name ASC")->fetchAll();
        $facilities = $pdo->query("SELECT * FROM facility_locations ORDER BY id DESC")->fetchAll();
    } catch (PDOException $e) {}
}

?>

<?php require __DIR__ . '/includes/head.php'; ?>

<?php if ($page === 'dashboard') include __DIR__ . '/pages/dashboard.php'; ?>
<?php if ($page === 'orders') include __DIR__ . '/pages/orders.php'; ?>
<?php if ($page === 'classes') include __DIR__ . '/pages/classes.php'; ?>
<?php if ($page === 'gallery') include __DIR__ . '/pages/gallery.php'; ?>
<?php if ($page === 'facilities') include __DIR__ . '/pages/facilities.php'; ?>
<?php if ($page === 'instructors') include __DIR__ . '/pages/instructors.php'; ?>
<?php if ($page === 'certs') include __DIR__ . '/pages/certs.php'; ?>
<?php if ($page === 'reports') include __DIR__ . '/pages/reports.php'; ?>
<?php if ($page === 'finance') include __DIR__ . '/pages/finance.php'; ?>
<?php if ($page === 'admins') include __DIR__ . '/pages/admins.php'; ?>
<?php if ($page === 'settings') include __DIR__ . '/pages/settings.php'; ?>
<?php if ($page === 'categories') include __DIR__ . '/pages/categories.php'; ?>
<?php if ($page === 'testimonials') include __DIR__ . '/pages/testimonials.php'; ?>
<?php if ($page === 'materials') include __DIR__ . '/pages/materials.php'; ?>
<?php if ($page === 'chat') include __DIR__ . '/pages/chat.php'; ?>
<?php if ($page === 'chatbot') include __DIR__ . '/pages/chatbot.php'; ?>
<?php if ($page === 'users') include __DIR__ . '/pages/users.php'; ?>

<?php require __DIR__ . '/includes/modals.php'; ?>

</div> <!-- End of main-content -->

<?php require __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
