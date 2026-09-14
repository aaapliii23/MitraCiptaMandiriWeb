<?php
if (file_exists(__DIR__ . '/secrets.php')) require_once __DIR__ . '/secrets.php';
$host = defined('DB_HOST') ? DB_HOST : (getenv('DB_HOST') ?: 'localhost');
$dbname = defined('DB_NAME') ? DB_NAME : (getenv('DB_NAME') ?: 'mcm_db');
$username = defined('DB_USER') ? DB_USER : (getenv('DB_USER') ?: 'root');
$password = defined('DB_PASS') ? DB_PASS : (getenv('DB_PASS') ?: 'password');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Optional: Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Auto-migrate: chat_messages.sender_type (dibutuhkan chat_api.php & whatsapp_client.php terbaru)
    try {
        $pdo->query("SELECT sender_type FROM chat_messages LIMIT 1");
    } catch (PDOException $e) {
        try { $pdo->exec("ALTER TABLE chat_messages ADD COLUMN sender_type ENUM('visitor','bot','admin') NOT NULL DEFAULT 'visitor' AFTER direction"); } catch (PDOException $e2) {}
        try { $pdo->exec("ALTER TABLE chat_messages ADD INDEX idx_chat_wa_number (wa_number)"); } catch (PDOException $e2) {}
        try { $pdo->exec("ALTER TABLE chat_messages ADD INDEX idx_chat_sender_type (sender_type)"); } catch (PDOException $e2) {}
    }
    // Auto-migrate: chat_messages.is_read & read_at (dibutuhkan fitur status dibaca/dilihat)
    try {
        $pdo->query("SELECT is_read, read_at FROM chat_messages LIMIT 1");
    } catch (PDOException $e) {
        try { $pdo->exec("ALTER TABLE chat_messages ADD COLUMN is_read TINYINT(1) NOT NULL DEFAULT 0 AFTER matched_intent"); } catch (PDOException $e2) {}
        try { $pdo->exec("ALTER TABLE chat_messages ADD COLUMN read_at DATETIME NULL AFTER is_read"); } catch (PDOException $e2) {}
        try { $pdo->exec("ALTER TABLE chat_messages ADD INDEX idx_chat_is_read (is_read)"); } catch (PDOException $e2) {}
    }
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

function mcm_wa_admin_link($text = '') {
    $raw = mcm_setting('admin_whatsapp', '628978902864');
    $digits = preg_replace('/\D+/', '', $raw);
    if (strpos($digits, '0') === 0) {
        $digits = '62' . substr($digits, 1);
    } elseif (strpos($digits, '62') !== 0 && $digits !== '') {
        $digits = '62' . $digits;
    }
    if ($digits === '') {
        $digits = '628978902864';
    }
    $url = 'https://wa.me/' . $digits;
    if ($text !== '') {
        $url .= '?text=' . rawurlencode($text);
    }
    return $url;
}
