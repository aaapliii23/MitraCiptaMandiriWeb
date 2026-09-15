<?php
// SEMENTARA: helper keamanan terpusat — rate limiting + session timeout + CSRF admin
// Semua file admin & auth should include ini via require_once

// === 1. Session timeout (30 menit tidak aktif → logout) ===
function mcm_check_session_timeout($maxIdle = 1800) {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $maxIdle)) {
        session_unset();
        session_destroy();
        return false;
    }
    $_SESSION['last_activity'] = time();
    return true;
}

// === 2. Rate limiting sederhana (file-based, tanpa Redis) ===
function mcm_rate_limit($key, $maxAttempts = 5, $windowSeconds = 900) {
    $dir = __DIR__ . '/../logs';
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    $file = $dir . '/rate_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $key) . '.json';
    $now = time();
    $data = ['attempts' => [], 'blocked_until' => 0];
    if (file_exists($file)) {
        $raw = @file_get_contents($file);
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) $data = array_merge($data, $decoded);
    }
    // cek blokir
    if (!empty($data['blocked_until']) && $now < $data['blocked_until']) {
        $remain = $data['blocked_until'] - $now;
        return ['allowed' => false, 'remaining' => $remain, 'message' => "Terlalu banyak percobaan. Coba lagi dalam " . ceil($remain/60) . " menit."];
    }
    // bersihkan attempt di luar window
    $data['attempts'] = array_filter($data['attempts'], fn($t) => $now - $t < $windowSeconds);
    if (count($data['attempts']) >= $maxAttempts) {
        $data['blocked_until'] = $now + $windowSeconds;
        @file_put_contents($file, json_encode($data));
        return ['allowed' => false, 'remaining' => $windowSeconds, 'message' => "Terlalu banyak percobaan. Akun terkunci " . ceil($windowSeconds/60) . " menit."];
    }
    return ['allowed' => true, 'remaining' => 0, 'message' => ''];
}

function mcm_rate_limit_hit($key, $windowSeconds = 900) {
    $dir = __DIR__ . '/../logs';
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    $file = $dir . '/rate_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $key) . '.json';
    $now = time();
    $data = ['attempts' => [], 'blocked_until' => 0];
    if (file_exists($file)) {
        $raw = @file_get_contents($file);
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) $data = array_merge($data, $decoded);
    }
    $data['attempts'][] = $now;
    @file_put_contents($file, json_encode($data));
}

function mcm_rate_limit_reset($key) {
    $file = __DIR__ . '/../logs/rate_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $key) . '.json';
    if (file_exists($file)) @unlink($file);
}

// === 3. CORS helper — hanya izinkan origin resmi ===
function mcm_cors_headers($allowedOrigins = []) {
    if (empty($allowedOrigins)) {
        $allowedOrigins = [
            'https://mitraciptamandiri.com',
            'https://www.mitraciptamandiri.com',
            // SEMENTARA: portfgil untuk testing
            'https://portfgil.my.id',
            'https://www.portfgil.my.id',
        ];
        // izinkan localhost untuk dev
        if (($_SERVER['HTTP_HOST'] ?? '') === 'localhost:8000' || ($_SERVER['HTTP_HOST'] ?? '') === '127.0.0.1:8000' || strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') === 0) {
            $allowedOrigins[] = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
            $allowedOrigins[] = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
        }
    }
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if ($origin !== '' && in_array($origin, $allowedOrigins, true)) {
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, X-Requested-With, Authorization");
    }
    // Preflight
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

// === 4. CSRF untuk admin ===
function mcm_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function mcm_csrf_verify($token) {
    return !empty($_SESSION['csrf_token']) && !empty($token) && hash_equals($_SESSION['csrf_token'], $token);
}
