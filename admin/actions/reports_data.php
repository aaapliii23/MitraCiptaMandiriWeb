<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}
require_once '../../config/database.php';

$type = $_GET['type'] ?? 'monthly';
$category = $_GET['category'] ?? 'all';
$classId = (int)($_GET['class_id'] ?? 0);
$year = $_GET['year'] ?? date('Y');

$filterJoin = " FROM orders o JOIN classes c ON o.class_id = c.id WHERE 1=1";
$filterParams = [];

if ($category !== 'all' && !empty($category)) {
    $filterJoin .= " AND c.category = :cat";
    $filterParams[':cat'] = $category;
}
if ($classId > 0) {
    $filterJoin .= " AND o.class_id = :cid";
    $filterParams[':cid'] = $classId;
}

try {
    if ($type === 'weekly') {
        // Last 7 days
        $sql = "SELECT DATE(o.created_at) as label, COUNT(o.id) as count $filterJoin AND o.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY DATE(o.created_at) ORDER BY DATE(o.created_at) ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($filterParams);
        $data = $stmt->fetchAll();
    } elseif ($type === 'monthly') {
        if ($year === 'all' || empty($year)) {
            $sql = "SELECT DATE_FORMAT(o.created_at, '%b %Y') as label, COUNT(o.id) as count $filterJoin GROUP BY YEAR(o.created_at), MONTH(o.created_at) ORDER BY o.created_at ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($filterParams);
        } else {
            $sql = "SELECT DATE_FORMAT(o.created_at, '%b') as label, COUNT(o.id) as count $filterJoin AND YEAR(o.created_at) = :year GROUP BY MONTH(o.created_at) ORDER BY MONTH(o.created_at) ASC";
            $stmt = $pdo->prepare($sql);
            $filterParams[':year'] = (int)$year;
            $stmt->execute($filterParams);
        }
        $data = $stmt->fetchAll();
    } elseif ($type === 'yearly') {
        // Last 10 years
        $sql = "SELECT YEAR(o.created_at) as label, COUNT(o.id) as count $filterJoin AND o.created_at >= DATE_SUB(NOW(), INTERVAL 10 YEAR) GROUP BY YEAR(o.created_at) ORDER BY YEAR(o.created_at) ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($filterParams);
        $data = $stmt->fetchAll();
    }

    echo json_encode([
        'status' => 'success',
        'labels' => array_column($data, 'label'),
        'counts' => array_column($data, 'count')
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
