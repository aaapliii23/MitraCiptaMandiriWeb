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

function asset_src($path, $prefix = null) {
    if ($prefix === null) {
        $script_file = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME'] ?? '');
        $app_root = str_replace('\\', '/', dirname(__DIR__));
        if ($script_file !== '' && strpos($script_file, $app_root) === 0) {
            $rel = trim(substr(str_replace('\\', '/', dirname($script_file)), strlen($app_root)), '/');
            $prefix = ($rel === '') ? '' : str_repeat('../', substr_count($rel, '/') + 1);
        } else {
            $prefix = (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'index.php') ? '' : '../';
        }
    }
    if (empty($path)) return $prefix . 'assets/img/logo.png';
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0 || strpos($path, '//') === 0) {
        return $path;
    }
    return $prefix . ltrim($path, '/');
}
?>
