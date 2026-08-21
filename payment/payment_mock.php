<?php
require_once '../config/database.php';
require_once '../includes/payment_gateway.php';

$orderNumber = $_GET['order'] ?? '';
$token = $_GET['token'] ?? '';
$stmt = $pdo->prepare("SELECT o.*, c.name AS class_name FROM orders o LEFT JOIN classes c ON o.class_id = c.id WHERE o.order_number = ?");
$stmt->execute([$orderNumber]);
$order = $stmt->fetch();
if (!$order) {
    die('Order tidak ditemukan.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulasi Pembayaran - MCM</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="shortcut icon" type="image/png" href="../assets/img/logo.png">
    <link rel="apple-touch-icon" href="../assets/img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light" style="min-height: 100vh;">
    <div class="container py-5" style="max-width: 560px;">
        <div class="card border-0 shadow rounded-4 overflow-hidden">
            <div class="p-4 text-white text-center" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9);">
                <i class="fas fa-credit-card fa-3x mb-3"></i>
                <h4 class="fw-bold mb-1">Halaman Pembayaran (Simulasi)</h4>
                <p class="mb-0 small opacity-75">Mode mock — Payment Gateway belum terhubung.</p>
            </div>
            <div class="card-body p-4">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">No. Order</span>
                    <span class="fw-bold small"><?php echo htmlspecialchars($order['order_number']); ?></span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Program</span>
                    <span class="fw-bold small"><?php echo htmlspecialchars($order['class_name']); ?></span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Metode Belajar</span>
                    <span class="badge <?php echo ($order['learning_type'] ?? '') === 'online' ? 'bg-info' : 'bg-primary'; ?> rounded-pill small">
                        <?php echo ($order['learning_type'] ?? '') === 'online' ? '<i class="fas fa-laptop me-1"></i>Online (LMS)' : '<i class="fas fa-chalkboard-teacher me-1"></i>Offline (Tatap Muka)'; ?>
                    </span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted small">Nama</span>
                    <span class="fw-bold small"><?php echo htmlspecialchars($order['customer_name']); ?></span>
                </div>
                <div class="d-flex justify-content-between py-3">
                    <span class="text-muted small">Total</span>
                    <span class="fw-bold" style="color: #0c4a6e;">Rp <?php echo number_format($order['amount'], 0, ',', '.'); ?></span>
                </div>

                <div class="alert alert-info border-0 rounded-3 small mb-4">
                    <i class="fas fa-info-circle me-2"></i>Pilih salah satu tombol untuk mensimulasikan hasil pembayaran dari Payment Gateway.
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-success rounded-pill py-2 fw-bold" onclick="simulate('paid', 'bank_transfer')">
                        <i class="fas fa-check-circle me-2"></i>Bayar Sukses
                    </button>
                    <div class="row g-2">
                        <div class="col-6">
                            <button class="btn btn-outline-danger rounded-pill py-2 w-100 fw-bold" onclick="simulate('failed', 'bank_transfer')">
                                <i class="fas fa-times-circle me-1"></i>Gagal
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-warning rounded-pill py-2 w-100 fw-bold" onclick="simulate('expired', null)">
                                <i class="fas fa-clock me-1"></i>Kedaluwarsa
                            </button>
                        </div>
                    </div>
                </div>

                <div id="progressMsg" class="text-center small text-muted mt-3 d-none">
                    <span class="spinner-border spinner-border-sm me-2"></span>Memproses pembayaran...
                </div>
            </div>
        </div>
        <p class="text-center small text-muted mt-4 mb-0"><a href="../index.php" class="text-decoration-none">&larr; Kembali ke Beranda</a></p>
    </div>

    <script>
        function simulate(status, method) {
            const btnWrap = document.querySelector('.d-grid');
            btnWrap.style.opacity = '0.5';
            btnWrap.style.pointerEvents = 'none';
            document.getElementById('progressMsg').classList.remove('d-none');

            const orderNumber = <?php echo json_encode($order['order_number']); ?>;
            const signature = <?php echo json_encode(pg_mock_signature($order['order_number'])); ?>;
            const payload = {
                order_number: orderNumber,
                payment_status: status,
                payment_method: method,
                gateway_ref: 'MOCK-' + orderNumber,
                signature: signature
            };

            fetch('payment_webhook.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                window.location.href = 'payment_status.php?order=' + encodeURIComponent(orderNumber);
            })
            .catch(() => {
                window.location.href = 'payment_status.php?order=' + encodeURIComponent(orderNumber);
            });
        }
    </script>
</body>
</html>