<?php
if (file_exists(__DIR__ . '/../config/secrets.php')) {
    require_once __DIR__ . '/../config/secrets.php';
}

define('MCM_MOCK_SIGNATURE_SECRET', 'mcm-mock-signature-secret');

function pg_mode() {
    return defined('PAYMENT_MODE') ? PAYMENT_MODE : 'mock';
}

function pg_base_url() {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    return ($https ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
}

function pg_create_transaction($pdo, $order, $class) {
    $mode = pg_mode();
    if ($mode === 'mock') {
        $token = bin2hex(random_bytes(16));
        $ref = 'MOCK-' . $order['order_number'];
        $stmt = $pdo->prepare("UPDATE orders SET payment_gateway_ref = ? WHERE id = ?");
        $stmt->execute([$ref, $order['id']]);
        $url = pg_base_url() . '/payment/payment_mock.php?order=' . urlencode($order['order_number']) . '&token=' . $token;
        return ['status' => 'success', 'payment_url' => $url, 'payment_gateway_ref' => $ref];
    }
    return ['status' => 'error', 'message' => 'PAYMENT_MODE belum dikonfigurasi untuk sandbox/production.'];
}

function pg_verify_webhook($payload) {
    $mode = pg_mode();
    if ($mode === 'mock') {
        $expected = hash_hmac('sha256', (string)($payload['order_number'] ?? ''), MCM_MOCK_SIGNATURE_SECRET);
        return isset($payload['signature']) && hash_equals($expected, (string)$payload['signature']);
    }
    return false;
}

function pg_mock_signature($orderNumber) {
    return hash_hmac('sha256', (string)$orderNumber, MCM_MOCK_SIGNATURE_SECRET);
}