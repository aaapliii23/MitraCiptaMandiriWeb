<?php
require_once '../includes/auth_user.php';
require_once '../config/database.php';

$userId = (int)$_SESSION['user_id'];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Hapus item: hanya milik user login + masih belum dibayar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'hapus') {
    $token = $_POST['csrf_token'] ?? '';
    if (empty($token) || !hash_equals($csrf_token, $token)) {
        $_SESSION['flash_error'] = 'Token keamanan tidak valid. Muat ulang halaman lalu coba lagi.';
    } else {
        $orderId = (int)($_POST['order_id'] ?? 0);
        try {
            $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ? AND payment_status IN ('unpaid','pending') AND status = 'pending'");
            $stmt->execute([$orderId, $userId]);
            $_SESSION['flash_success'] = $stmt->rowCount() > 0 ? 'Kelas dihapus dari keranjang.' : 'Pesanan tidak ditemukan atau sudah diproses.';
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'Gagal menghapus. Coba lagi.';
        }
    }
    header('Location: cart.php');
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT o.id, o.order_number, o.amount, o.class_mode, o.payment_status, o.created_at,
               c.name AS class_name, c.category, c.image
        FROM orders o
        JOIN classes c ON o.class_id = c.id
        WHERE o.user_id = ? AND o.payment_status IN ('unpaid','pending') AND o.status = 'pending'
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$userId]);
    $cart = $stmt->fetchAll();
} catch (PDOException $e) {
    $cart = [];
}

$total = 0;
foreach ($cart as $row) $total += (int)$row['amount'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Saya - MCM</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="shortcut icon" type="image/png" href="../assets/img/logo.png">
    <link rel="apple-touch-icon" href="../assets/img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm bg-white" style="transition: all 0.4s ease;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="../index.php">
                <img src="../assets/img/logo.png" alt="MCM Logo" style="height: 40px;">
                <span class="fw-bold ms-2" style="font-size: 0.9rem; letter-spacing: 1px;">LMS MITRA CIPTA MANDIRI</span>
            </a>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small d-none d-md-inline"><i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="dashboard.php" class="btn btn-outline-primary btn-sm rounded-pill px-3"><i class="fas fa-tachometer-alt me-1"></i>Dashboard</a>
                <a href="../index.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">Beranda</a>
                <a href="../auth/user_logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3"><i class="fas fa-sign-out-alt me-1"></i>Keluar</a>
            </div>
        </div>
    </nav>

    <section class="pt-5" style="margin-top: 56px; min-height: 80vh; background: #f8fafc;">
        <div class="container py-4">
            <?php
            // Toast: ambil flash session untuk di-render sebagai toastr via JS di bawah
            $toastSuccess = $_SESSION['flash_success'] ?? '';
            $toastError = $_SESSION['flash_error'] ?? '';
            unset($_SESSION['flash_success'], $_SESSION['flash_error']);
            ?>
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
                <div>
                    <h3 class="fw-bold text-dark mb-1"><i class="fas fa-shopping-cart me-2 text-primary"></i>Keranjang Saya</h3>
                    <p class="text-muted mb-0">Kelas yang Anda pilih tapi belum dibayar.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2"><?php echo count($cart); ?> menunggu pembayaran</span>
                    <a href="../pages/programs.php" class="btn btn-primary rounded-pill fw-bold px-4 py-2 shadow-sm text-nowrap" style="background: linear-gradient(135deg, #0c4a6e, #0ea5e9); border: none;"><i class="fas fa-plus me-2"></i>Tambah Kelas</a>
                </div>
            </div>

            <?php if (empty($cart)): ?>
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                    <i class="fas fa-shopping-cart fs-1 text-primary opacity-25 mb-3"></i>
                    <h5 class="fw-bold text-dark">Keranjang kosong</h5>
                    <p class="text-muted">Tidak ada kelas yang menunggu pembayaran. Yuk pilih kelas dan mulai belajar!</p>
                    <div class="mt-2">
                        <a href="../pages/programs.php" class="btn btn-primary rounded-pill fw-bold px-4">Lihat Program Pelatihan</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($cart as $item):
                        $mode = strtolower(trim($item['class_mode'] ?? 'offline'));
                        $imgUrl = htmlspecialchars(asset_src($item['image']));
                    ?>
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 p-3">
                                <div class="d-flex flex-column flex-md-row align-items-md-center gap-3">
                                    <img src="<?php echo $imgUrl; ?>" alt="<?php echo htmlspecialchars($item['class_name']); ?>" class="rounded-3 flex-shrink-0" style="width: 120px; height: 84px; object-fit: cover;" loading="lazy" onerror="this.onerror=null;this.src='../assets/img/logo.png';">
                                    <div class="flex-grow-1">
                                        <div class="d-flex flex-wrap gap-2 mb-1">
                                            <span class="badge bg-soft-primary text-primary rounded-pill px-3"><?php echo htmlspecialchars($item['category']); ?></span>
                                            <span class="badge <?php echo $mode === 'online' ? 'bg-info bg-opacity-10 text-info' : 'bg-success bg-opacity-10 text-success'; ?> rounded-pill px-2" style="font-size:0.7rem;"><i class="fas <?php echo $mode === 'online' ? 'fa-laptop' : 'fa-chalkboard-teacher'; ?> me-1"></i><?php echo ucfirst($mode); ?></span>
                                            <span class="badge bg-warning bg-opacity-25 text-dark rounded-pill px-2" style="font-size:0.7rem;"><i class="fas fa-clock me-1"></i>Belum dibayar</span>
                                        </div>
                                        <h5 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($item['class_name']); ?></h5>
                                        <p class="text-muted small mb-0"><?php echo htmlspecialchars($item['order_number']); ?> &middot; Ditambahkan <?php echo date('d M Y H:i', strtotime($item['created_at'])); ?></p>
                                    </div>
                                    <div class="text-md-end flex-shrink-0">
                                        <div class="fw-bold text-primary fs-5 mb-2">Rp<?php echo number_format((int)$item['amount'], 0, ',', '.'); ?></div>
                                        <div class="d-flex gap-2 justify-content-md-end">
                                            <a href="../payment/custom_payment.php?order=<?php echo urlencode($item['order_number']); ?>" class="btn btn-primary btn-sm rounded-pill fw-bold px-3"><i class="fas fa-credit-card me-1"></i>Bayar Sekarang</a>
                                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 btn-hapus-cart" data-order-id="<?php echo (int)$item['id']; ?>" data-class-name="<?php echo htmlspecialchars($item['class_name']); ?>"><i class="fas fa-trash me-1"></i>Hapus</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 mt-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div>
                            <span class="text-muted small">Total yang harus dibayar</span>
                            <div class="fw-bold fs-4 text-dark">Rp<?php echo number_format($total, 0, ',', '.'); ?></div>
                        </div>
                        <p class="text-muted small mb-0"><i class="fas fa-info-circle me-1"></i>Setelah pembayaran lunas, kelas otomatis masuk ke Dashboard LMS Anda.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Modal konfirmasi hapus -->
    <div class="modal fade" id="hapusCartModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-trash-alt me-2 text-danger"></i>Hapus dari Keranjang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-0">Apakah Anda yakin ingin menghapus kelas <strong id="hapusCartNama" class="text-dark"></strong> dari keranjang?</p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <form method="POST" action="cart.php" class="d-inline" id="hapusCartForm">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                        <input type="hidden" name="aksi" value="hapus">
                        <input type="hidden" name="order_id" id="hapusCartOrderId" value="">
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold"><i class="fas fa-trash me-1"></i>Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
    <script>
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 3500,
            extendedTimeOut: 1000
        };
        <?php if ($toastSuccess !== ''): ?>
        toastr.success(<?php echo json_encode($toastSuccess); ?>);
        <?php endif; ?>
        <?php if ($toastError !== ''): ?>
        toastr.error(<?php echo json_encode($toastError); ?>);
        <?php endif; ?>

        document.querySelectorAll('.btn-hapus-cart').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('hapusCartNama').textContent = '"' + btn.getAttribute('data-class-name') + '"';
                document.getElementById('hapusCartOrderId').value = btn.getAttribute('data-order-id');
                new bootstrap.Modal(document.getElementById('hapusCartModal')).show();
            });
        });
    </script>
    <?php
    $base_url = '../';
    require_once '../config/database.php';
    require_once __DIR__ . '/../includes/chat_widget.php';
    ?>
</body>
</html>
