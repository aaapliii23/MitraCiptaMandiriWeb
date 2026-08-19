<?php
$host = 'localhost';
$dbname = 'mcm_db';
$username = 'root';
$password = 'password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Optional: Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

function asset_src($path, $prefix = '../') {
    if (empty($path)) return $prefix . 'assets/img/logo.png';
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0 || strpos($path, $prefix) === 0 || strpos($path, '/') === 0) {
        return $path;
    }
    return $prefix . ltrim($path, '/');
}
?>
