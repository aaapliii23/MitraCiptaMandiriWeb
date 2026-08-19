<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/payment_gateway.php';

$orderNumber = $_GET['order'] ?? ($argv[1] ?? '');
$status = $_GET['status'] ?? ($argv[2] ?? 'paid');
$method = $_GET['method'] ?? ($argv[3] ?? 'bank_transfer');

$allowed = ['paid', 'failed', 'expired'];
if ($orderNumber === '' || !in_array($status, $allowed)) {
    echo "Usage:\n  CLI : php tools/simulate_webhook.php ORD-XXXX paid [bank_transfer]\n  Web : tools/simulate_webhook.php?order=ORD-XXXX&status=paid\n";
    exit(1);
}

$stmt = $pdo->prepare("SELECT order_number FROM orders WHERE order_number = ?");
$stmt->execute([$orderNumber]);
if (!$stmt->fetch()) {
    echo "Order tidak ditemukan: $orderNumber\n";
    exit(1);
}

$signature = pg_mock_signature($orderNumber);
$payload = json_encode([
    'order_number' => $orderNumber,
    'payment_status' => $status,
    'payment_method' => $method,
    'gateway_ref' => 'MOCK-SIM-' . strtoupper(uniqid()),
    'signature' => $signature,
]);

$ch = curl_init('http://127.0.0.1:8000/payment/payment_webhook.php');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => $payload,
]);
$resp = curl_exec($ch);
curl_close($ch);

echo "Mengirim status '$status' untuk $orderNumber...\n";
echo $resp . "\n";