<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Akses tidak diizinkan.']);
    exit;
}

require_once '../../config/database.php';

$classId = (int)($_GET['class_id'] ?? 0);
$year = $_GET['year'] ?? 'all';

if ($classId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID Pelatihan tidak valid.']);
    exit;
}

try {
    $sql = "SELECT o.id, o.order_number, o.customer_name, o.customer_phone, o.customer_email, o.amount, o.status, o.payment_status, o.created_at, c.name as class_name
            FROM orders o
            JOIN classes c ON o.class_id = c.id
            WHERE o.class_id = :class_id";
    $params = [':class_id' => $classId];

    if ($year !== 'all' && !empty($year)) {
        $sql .= " AND YEAR(o.created_at) = :year";
        $params[':year'] = (int)$year;
    }

    $sql .= " ORDER BY o.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'class_id' => $classId,
        'count' => count($participants),
        'participants' => $participants
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
