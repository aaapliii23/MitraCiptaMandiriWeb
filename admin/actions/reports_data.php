<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}
require_once '../../config/database.php';

$type = $_GET['type'] ?? 'monthly';

try {
    if ($type === 'weekly') {
        // Last 7 days
        $stmt = $pdo->query("SELECT DATE(created_at) as label, COUNT(*) as count 
                             FROM orders 
                             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                             GROUP BY DATE(created_at)
                             ORDER BY DATE(created_at) ASC");
        $data = $stmt->fetchAll();
    } elseif ($type === 'monthly') {
        $year = $_GET['year'] ?? date('Y');
        // Specific year monthly breakdown
        $stmt = $pdo->prepare("SELECT DATE_FORMAT(created_at, '%b') as label, COUNT(*) as count 
                             FROM orders 
                             WHERE YEAR(created_at) = ?
                             GROUP BY MONTH(created_at)
                             ORDER BY MONTH(created_at) ASC");
        $stmt->execute([$year]);
        $data = $stmt->fetchAll();
    } elseif ($type === 'yearly') {
        // Last 10 years
        $stmt = $pdo->query("SELECT YEAR(created_at) as label, COUNT(*) as count 
                             FROM orders 
                             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 10 YEAR)
                             GROUP BY YEAR(created_at)
                             ORDER BY YEAR(created_at) ASC");
        $data = $stmt->fetchAll();
    } elseif ($type === 'payment') {
        $stmt = $pdo->query("SELECT payment_status, COUNT(*) as count, SUM(amount) as total 
                             FROM orders 
                             GROUP BY payment_status");
        $data = $stmt->fetchAll();
        echo json_encode(['status' => 'success', 'payment' => $data]);
        exit;
    }

    echo json_encode([
        'status' => 'success',
        'labels' => array_column($data, 'label'),
        'counts' => array_column($data, 'count')
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
