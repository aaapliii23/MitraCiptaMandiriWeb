<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

require_once '../../config/database.php';

// Auto-create tables
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

    // Seed default categories if table empty
    $catCount = (int)$pdo->query("SELECT COUNT(*) FROM facility_categories")->fetchColumn();
    if ($catCount === 0) {
        $defaults = [
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
        foreach ($defaults as $d) {
            $ins->execute($d);
        }
    }
} catch (PDOException $e) {}

$action = $_POST['action'] ?? '';

// ACTION: CREATE CATEGORY
if ($action === 'create_category') {
    $name = trim($_POST['name'] ?? '');
    $icon = trim($_POST['icon'] ?? 'fa-building');

    if (empty($name)) {
        echo json_encode(['status' => 'error', 'message' => 'Nama kategori wajib diisi.']);
        exit;
    }

    $slug = trim($_POST['slug'] ?? '');
    if (empty($slug)) {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', trim($name)));
    } else {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9_]+/', '', trim($slug)));
    }

    // Check duplicate
    $chk = $pdo->prepare("SELECT COUNT(*) FROM facility_categories WHERE slug = ?");
    $chk->execute([$slug]);
    if ($chk->fetchColumn() > 0) {
        echo json_encode(['status' => 'error', 'message' => "Kategori dengan slug '$slug' sudah ada."]);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO facility_categories (slug, name, icon) VALUES (?, ?, ?)");
    if ($stmt->execute([$slug, $name, $icon])) {
        // Also create physical folder in assets/img/fasilitas/
        $dir = '../../assets/img/fasilitas/' . $slug;
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        echo json_encode(['status' => 'success', 'message' => "Kategori '$name' berhasil ditambahkan."]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan kategori.']);
    }
    exit;
}

// ACTION: DELETE CATEGORY
if ($action === 'delete_category') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID kategori tidak valid.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT slug, name FROM facility_categories WHERE id = ?");
    $stmt->execute([$id]);
    $cat = $stmt->fetch();

    if (!$cat) {
        echo json_encode(['status' => 'error', 'message' => 'Kategori tidak ditemukan.']);
        exit;
    }

    $del = $pdo->prepare("DELETE FROM facility_categories WHERE id = ?");
    if ($del->execute([$id])) {
        echo json_encode(['status' => 'success', 'message' => "Kategori '{$cat['name']}' berhasil dihapus."]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus kategori.']);
    }
    exit;
}

// ACTION: CREATE PHOTOS
if ($action === 'create') {
    $categoryRaw = trim($_POST['category'] ?? '');
    $category = strtolower(preg_replace('/[^a-z0-9]+/', '_', $categoryRaw));
    $category = trim($category, '_');
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($category)) {
        echo json_encode(['status' => 'error', 'message' => 'Pilih kategori lokasi.']);
        exit;
    }

    // Get category name
    $stmtCat = $pdo->prepare("SELECT name FROM facility_categories WHERE slug = ?");
    $stmtCat->execute([$category]);
    $catName = $stmtCat->fetchColumn() ?: ucfirst(str_replace('_', ' ', $category));

    if (empty($title)) {
        $title = 'Foto ' . $catName . ' MCM';
    }

    if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
        echo json_encode(['status' => 'error', 'message' => 'Minimal satu gambar foto lokasi wajib dipilih.']);
        exit;
    }

    $uploadDir = '../../uploads/facilities';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'heic'];
    $successCount = 0;
    $totalFiles = count($_FILES['images']['name']);
    $errors = [];

    for ($i = 0; $i < $totalFiles; $i++) {
        $filename = $_FILES['images']['name'][$i];
        $errCode = $_FILES['images']['error'][$i];

        if ($errCode !== UPLOAD_ERR_OK) {
            $errors[] = "'$filename' gagal terupload (error $errCode).";
            continue;
        }

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = "'$filename' format tidak didukung (hanya jpg, jpeg, png, webp).";
            continue;
        }

        $newFilename = uniqid('fac_') . '_' . $i . '.' . ($ext === 'heic' ? 'jpg' : $ext);
        $destination = 'uploads/facilities/' . $newFilename;
        $fullPath = '../../' . $destination;

        if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $fullPath)) {
            $itemTitle = ($totalFiles > 1) ? ($title . ' (' . ($i + 1) . ')') : $title;
            $stmt = $pdo->prepare("INSERT INTO facility_locations (category, title, image, description) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$category, $itemTitle, $destination, $description])) {
                $successCount++;
            }
        }
    }

    if ($successCount > 0) {
        $msg = "$successCount foto lokasi berhasil ditambahkan.";
        if (!empty($errors)) $msg .= ' ' . implode(' ', $errors);
        echo json_encode(['status' => 'success', 'message' => $msg]);
    } else {
        echo json_encode(['status' => 'error', 'message' => implode(' ', $errors) ?: 'Gagal mengupload foto lokasi.']);
    }
    exit;
}

// ACTION: UPDATE PHOTO
if ($action === 'update') {
    $id = (int)($_POST['id'] ?? 0);
    $categoryRaw = trim($_POST['category'] ?? '');
    $category = strtolower(preg_replace('/[^a-z0-9]+/', '_', $categoryRaw));
    $category = trim($category, '_');
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($id <= 0 || empty($category) || empty($title)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        exit;
    }

    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $uploadDir = '../../uploads/facilities';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $newFilename = uniqid('fac_') . '.' . $ext;
            $destination = 'uploads/facilities/' . $newFilename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], '../../' . $destination)) {
                $imagePath = $destination;
            }
        }
    }

    if ($imagePath) {
        $stmt = $pdo->prepare("UPDATE facility_locations SET category = ?, title = ?, description = ?, image = ? WHERE id = ?");
        $res = $stmt->execute([$category, $title, $description, $imagePath, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE facility_locations SET category = ?, title = ?, description = ? WHERE id = ?");
        $res = $stmt->execute([$category, $title, $description, $id]);
    }

    if ($res) {
        echo json_encode(['status' => 'success', 'message' => 'Data foto lokasi berhasil diperbarui.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data foto lokasi.']);
    }
    exit;
}

// ACTION: DELETE PHOTO
if ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak valid.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT image FROM facility_locations WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();

    if ($item && !empty($item['image']) && strpos($item['image'], 'uploads/facilities/') === 0) {
        $file = '../../' . $item['image'];
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    $del = $pdo->prepare("DELETE FROM facility_locations WHERE id = ?");
    if ($del->execute([$id])) {
        echo json_encode(['status' => 'success', 'message' => 'Foto lokasi berhasil dihapus.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus foto lokasi.']);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Aksi tidak dikenal.']);
