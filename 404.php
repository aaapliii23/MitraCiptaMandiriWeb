<?php
http_response_code(404);
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/database.php';
$hide_nav_items = false;
include __DIR__ . '/includes/header.php';
?>
<style>
  .mcm-404-wrap{min-height:70vh;display:flex;align-items:center;justify-content:center;padding:48px 0 32px;background:var(--light-bg,#f8fafc);}
  .mcm-404-card{max-width:560px;width:100%;background:#fff;border-radius:1.5rem;box-shadow:0 10px 30px rgba(2,6,23,.08);padding:40px 32px;text-align:center;border:1px solid #e2e8f0;}
  .mcm-404-code{font-size:5.5rem;font-weight:800;line-height:1;letter-spacing:-3px;background:linear-gradient(135deg,var(--primary-color,#0c4a6e),var(--secondary-color,#0ea5e9));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
  .mcm-404-title{font-size:1.35rem;font-weight:700;color:#0f172a;margin:8px 0 10px;}
  .mcm-404-desc{color:var(--text-muted,#64748b);font-size:.95rem;line-height:1.6;margin-bottom:24px;}
  .mcm-404-actions{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;}
  .mcm-404-actions .btn{padding:11px 22px;border-radius:999px;font-weight:700;}
</style>

<div class="mcm-404-wrap">
  <div class="container d-flex justify-content-center">
    <div class="mcm-404-card" data-aos="zoom-in" data-aos-duration="600">
      <div class="mcm-404-code">404</div>
      <h1 class="mcm-404-title">Halaman Tidak Ditemukan</h1>
      <p class="mcm-404-desc">
        Maaf, halaman yang Anda cari tidak tersedia atau sudah dipindahkan.
        Periksa kembali alamat URL atau kembali ke beranda untuk melanjutkan.
      </p>
      <div class="mcm-404-actions">
        <a href="<?php echo htmlspecialchars($__homeAbs ?? '/index.php'); ?>" class="btn text-white" style="background:linear-gradient(135deg,#0c4a6e,#0ea5e9);border:none;box-shadow:0 10px 20px -5px rgba(14,165,233,.4);">
          <i class="fas fa-home me-2"></i>Kembali ke Beranda
        </a>
        <a href="javascript:history.back()" class="btn btn-outline-secondary" onclick="if(history.length>1){history.back();return false;}">
          <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
