<?php
session_start();
require_once '../config/database.php';

$order_number = $_GET['order'] ?? null;
if (!$order_number) die("Order tidak ditemukan.");

$stmt = $pdo->prepare("SELECT o.*, c.name as class_name, c.price FROM orders o JOIN classes c ON o.class_id = c.id WHERE o.order_number = ?");
$stmt->execute([$order_number]);
$order = $stmt->fetch();

if (!$order) die("Data pendaftaran tidak valid.");

if ($order['payment_status'] !== 'paid') {
    header("Location: payment_status.php?order=" . urlencode($order['order_number']));
    exit;
}

// Note: To truly generate a PDF on a server, we usually use libraries like Dompdf or FPDF.
// However, to provide an IMMEDIATE high-end solution that works without complex setup:
// We use a "Print-to-PDF" ready layout that looks stunning.
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pendaftaran - <?php echo $order['order_number']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
        }

        .invoice-card {
            background: white;
            border: none;
            border-radius: 2rem;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.05);
            max-width: 850px;
            margin: 40px auto;
            position: relative;
        }

        .invoice-header {
            background: linear-gradient(135deg, #0c4a6e, #0ea5e9);
            padding: 50px;
            color: white;
            position: relative;
        }

        .invoice-header::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 40px;
            background: white;
            clip-path: ellipse(50% 100% at 50% 100%);
        }

        .brand-logo {
            height: 60px;
            filter: drop-shadow(0 0 10px rgba(255,255,255,0.3));
        }

        .status-badge {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(5px);
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .info-label {
            font-size: 0.75rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .info-value {
            font-weight: 600;
            color: #0f172a;
        }

        .item-row {
            background: #f8fafc;
            border-radius: 1.25rem;
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid #f1f5f9;
        }

        .qr-placeholder {
            width: 100px;
            height: 100px;
            background: #f8fafc;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px dashed #e2e8f0;
        }

        .footer-note {
            background: #0f172a;
            color: rgba(255,255,255,0.6);
            padding: 30px;
            text-align: center;
            font-size: 0.8rem;
        }

        @media print {
            body { background: white; }
            .invoice-card { margin: 0; box-shadow: none; border-radius: 0; width: 100% !important; max-width: 100% !important; }
            .no-print { display: none !important; }
            .invoice-header { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    <div class="container no-print mt-4 px-3">
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm flex-fill flex-md-grow-0" style="background: #0c4a6e; border: none; min-width: 180px;">
                <i class="fas fa-print me-2"></i> Cetak PDF / Simpan
            </button>
            <?php if (isset($_SESSION['last_wa_link'])): ?>
                <a href="<?php echo $_SESSION['last_wa_link']; ?>" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm flex-fill flex-md-grow-0" style="background: linear-gradient(135deg, #25d366, #128c7e); border: none; min-width: 180px;">
                    <i class="fab fa-whatsapp me-2"></i> Konfirmasi ke WA
                </a>
            <?php endif; ?>
            <a href="../index.php" class="btn btn-light rounded-pill px-4 py-2 fw-bold shadow-sm flex-fill flex-md-grow-0 border" style="min-width: 120px;">
                Kembali
            </a>
        </div>
    </div>

    <div class="invoice-card mx-auto">
        <!-- Header -->
        <div class="invoice-header">
            <div class="d-flex justify-content-between align-items-start">
                <div class="d-flex align-items-center">
                    <img src="../assets/img/logo.png" alt="MCM Logo" class="brand-logo">
                    <div class="ms-3 ps-3 border-start border-white border-opacity-25 text-start">
                        <span class="d-block fw-bold fs-5" style="letter-spacing: 1px; line-height: 1.1;">MITRA CIPTA</span>
                        <span class="d-block fw-bold fs-5" style="letter-spacing: 1px; line-height: 1.1;">MANDIRI</span>
                    </div>
                </div>
                <div class="text-end">
                    <div class="status-badge mb-2">OFFICIAL RECEIPT</div>
                    <div class="small opacity-75"><?php echo date('d F Y', strtotime($order['created_at'])); ?></div>
                </div>
            </div>
            
            <div class="mt-5 pt-4">
                <h2 class="fw-800 mb-1" style="font-weight: 800;">BUKTI PENDAFTARAN</h2>
                <p class="opacity-75 mb-0">ID Transaksi: #<?php echo $order['order_number']; ?></p>
            </div>
        </div>

        <div class="p-5 mt-4">
            <!-- Customer & Order Info -->
            <div class="row mb-5 g-4">
                <div class="col-sm-6">
                    <div class="info-label">Diberikan Kepada:</div>
                    <div class="info-value fs-5"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                    <div class="small text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></div>
                    <div class="small text-muted"><?php echo htmlspecialchars($order['customer_phone']); ?></div>
                </div>
                <div class="col-sm-6 text-sm-end">
                    <div class="info-label">Asal Instansi:</div>
                    <div class="info-value"><?php echo htmlspecialchars($order['customer_institution']); ?></div>
                    <div class="info-label mt-3">Alamat Lengkap:</div>
                    <div class="small text-muted"><?php echo htmlspecialchars($order['customer_address']); ?></div>
                </div>
            </div>

            <!-- Item Details -->
            <div class="item-row d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4 me-4">
                        <i class="fas fa-graduation-cap fs-3"></i>
                    </div>
                    <div>
                        <div class="info-label">Program Pelatihan</div>
                        <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($order['class_name']); ?></h5>
                        <small class="text-muted">Standard Kurikulum Vokasi Nasional</small>
                    </div>
                </div>
                <div class="text-end">
                    <div class="info-label">Biaya</div>
                    <h5 class="fw-bold mb-0 text-primary">Rp <?php echo number_format($order['price'], 0, ',', '.'); ?></h5>
                </div>
            </div>

            <div class="row align-items-end mt-5">
                <div class="col-md-8">
                    <div class="bg-light p-4 rounded-4 border-start border-4 border-info">
                        <h6 class="fw-bold mb-2 small"><i class="fas fa-info-circle me-2 text-info"></i>Langkah Selanjutnya:</h6>
                        <ul class="small text-muted ps-3 mb-0">
                            <li>Simpan file PDF ini sebagai bukti sah pendaftaran.</li>
                            <li>Tunjukkan bukti ini kepada Admin via WhatsApp untuk validasi.</li>
                            <li>Tunggu konfirmasi jadwal mulai pelatihan dari tim kami.</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="info-label">Digital Verified</div>
                    <div class="qr-placeholder mx-auto mx-md-0 mt-2">
                        <i class="fas fa-qrcode fs-1 text-muted opacity-25"></i>
                    </div>
                    <p class="small text-muted mt-2 mb-0" style="font-size: 0.65rem;">MCM Authenticated Document</p>
                </div>
            </div>
        </div>

        <div class="footer-note">
            <p class="mb-1 fw-bold text-white">Mitra Cipta Mandiri - Empowering Independence</p>
            <p class="mb-0">www.mitraciptamandiri.com | © <?php echo date('Y'); ?> All Rights Reserved</p>
        </div>
    </div>

    <script>
        // Auto show print dialog if requested
        if (window.location.search.indexOf('print=true') > -1) {
            window.onload = function() { window.print(); }
        }
    </script>
</body>
</html>
