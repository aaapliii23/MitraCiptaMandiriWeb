<?php
$host = 'localhost';
$dbname = 'mcm_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Optional: Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

function asset_src($path, $prefix = null) {    if ($prefix === null) {
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
// Ambil nilai pengaturan platform (tabel settings key/value, diisi dari admin ?page=settings).
// Cache statis per-request; fallback ke $default jika belum pernah disimpan.
function mcm_setting($key, $default = '') {
    static $cache = null;
    if ($cache === null) {
        global $pdo;
        $cache = [];
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS `settings` (
              `key` varchar(100) NOT NULL,
              `value` text NULL,
              PRIMARY KEY (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            foreach ($pdo->query("SELECT `key`, `value` FROM settings") as $r) {
                $cache[$r['key']] = $r['value'];
            }
        } catch (PDOException $e) {}
    }
    return (isset($cache[$key]) && $cache[$key] !== '') ? $cache[$key] : $default;
}
