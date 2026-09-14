<?php
// Notification dropdown buttons for dashboard header
$pendingOrdersCount = (int)($stats['waiting_payment'] ?? 0);
$unreadChatsCount = (int)($stats['unread_chats'] ?? 0);
$pendingOrders = $pending_orders_list ?? [];
$unreadChats = $unread_chats_list ?? [];
?>
<!-- NOTIFIKASI PESANAN -->
<div class="dropdown d-inline-block position-relative" style="z-index: 25;">
    <button type="button" class="btn btn-white shadow-sm border rounded-circle position-relative d-inline-flex align-items-center justify-content-center text-primary dropdown-toggle-no-arrow header-notif-btn" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false" style="width: 38px; height: 38px; background: #fff;" title="Notifikasi Pesanan">
        <i class="fas fa-shopping-bag"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark js-header-order-badge shadow-sm" style="<?php echo ($pendingOrdersCount === 0) ? 'display:none;' : ''; ?>font-size: 0.65rem; padding: 0.25em 0.5em;">
            <?php echo $pendingOrdersCount; ?>
        </span>
        <span class="notif-red-dot js-order-dot" style="<?php echo ($pendingOrdersCount === 0) ? 'display:none;' : ''; ?>"></span>
    </button>
    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0 mt-2 header-notif-menu animate__animated animate__fadeIn" style="width: 330px; max-width: 90vw; z-index: 1060 !important; border: 1px solid rgba(0,0,0,0.08);">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light rounded-top-4">
            <div class="d-flex align-items-center gap-2">
                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary" style="width:26px;height:26px;font-size:0.75rem;">
                    <i class="fas fa-shopping-bag"></i>
                </span>
                <strong class="text-dark small">Pesanan Baru</strong>
            </div>
            <span class="badge bg-warning text-dark rounded-pill js-header-order-pill" style="font-size: 0.7rem;">
                <?php echo $pendingOrdersCount; ?> Baru
            </span>
        </div>
        <div class="js-header-order-items" style="max-height: 280px; overflow-y: auto;">
            <?php if (!empty($pendingOrders)): ?>
                <?php foreach ($pendingOrders as $po): ?>
                    <a href="?page=orders&search=<?php echo urlencode($po['order_number']); ?>" class="dropdown-item py-2 px-3 border-bottom border-light text-wrap d-flex align-items-start gap-2 notif-item">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0 mt-1" style="width: 30px; height: 30px; font-size: 0.75rem;">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div class="flex-grow-1" style="min-width: 0;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-dark small text-truncate" style="max-width: 145px;"><?php echo htmlspecialchars($po['customer_name'] ?: 'Pelanggan'); ?></span>
                                <small class="text-muted" style="font-size: 0.7rem;"><?php echo date('H:i', strtotime($po['created_at'])); ?></small>
                            </div>
                            <div class="text-muted small text-truncate" style="font-size: 0.75rem;"><?php echo htmlspecialchars($po['class_name'] ?: 'Pelatihan'); ?> • Rp <?php echo number_format((int)$po['amount'], 0, ',', '.'); ?></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-4 px-3 text-muted small js-header-order-empty">
                    <i class="fas fa-box-open fs-3 text-secondary opacity-50 mb-2 d-block"></i>
                    Tidak ada pesanan baru yang menunggu
                </div>
            <?php endif; ?>
        </div>
        <div class="p-2 text-center bg-light rounded-bottom-4 border-top">
            <a href="?page=orders" class="small text-primary fw-bold text-decoration-none d-block py-1">
                Buka Semua Pesanan <i class="fas fa-arrow-right ms-1 small"></i>
            </a>
        </div>
    </div>
</div>

<!-- NOTIFIKASI CHAT -->
<div class="dropdown d-inline-block position-relative" style="z-index: 25;">
    <button type="button" class="btn btn-white shadow-sm border rounded-circle position-relative d-inline-flex align-items-center justify-content-center text-success dropdown-toggle-no-arrow header-notif-btn" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false" style="width: 38px; height: 38px; background: #fff;" title="Notifikasi Pesan Chat">
        <i class="fab fa-whatsapp fs-5"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger text-white js-header-chat-badge shadow-sm" style="<?php echo ($unreadChatsCount === 0) ? 'display:none;' : ''; ?>font-size: 0.65rem; padding: 0.25em 0.5em;">
            <?php echo $unreadChatsCount; ?>
        </span>
        <span class="notif-red-dot js-chat-dot" style="<?php echo ($unreadChatsCount === 0) ? 'display:none;' : ''; ?>"></span>
    </button>
    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0 mt-2 header-notif-menu animate__animated animate__fadeIn" style="width: 330px; max-width: 90vw; z-index: 1060 !important; border: 1px solid rgba(0,0,0,0.08);">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light rounded-top-4">
            <div class="d-flex align-items-center gap-2">
                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 text-success" style="width:26px;height:26px;font-size:0.75rem;">
                    <i class="fab fa-whatsapp"></i>
                </span>
                <strong class="text-dark small">Pesan Chat Baru</strong>
            </div>
            <span class="badge bg-danger text-white rounded-pill js-header-chat-pill" style="font-size: 0.7rem;">
                <?php echo $unreadChatsCount; ?> Baru
            </span>
        </div>
        <div class="js-header-chat-items" style="max-height: 280px; overflow-y: auto;">
            <?php if (!empty($unreadChats)): ?>
                <?php foreach ($unreadChats as $uc): 
                    $isAnon = str_starts_with($uc['wa_number'], 'web-');
                    $isUser = str_starts_with($uc['wa_number'], 'user-');
                    if ($isAnon) {
                        $senderLabel = 'Pengunjung Web (' . substr($uc['wa_number'], 4, 6) . ')';
                        $iconClass = 'fas fa-desktop text-secondary';
                        $bgClass = 'bg-secondary bg-opacity-10';
                    } elseif ($isUser) {
                        $senderLabel = !empty($uc['user_name']) ? ($uc['user_name'] . ' (Siswa)') : ('Siswa #' . substr($uc['wa_number'], 5));
                        $iconClass = 'fas fa-graduation-cap text-primary';
                        $bgClass = 'bg-primary bg-opacity-10';
                    } else {
                        $senderLabel = $uc['wa_number'];
                        $iconClass = 'fab fa-whatsapp text-success';
                        $bgClass = 'bg-success bg-opacity-10';
                    }
                    $cleanMsg = mb_strimwidth(strip_tags($uc['message']), 0, 65, '...');
                ?>
                    <a href="?page=chat&thread=<?php echo urlencode($uc['wa_number']); ?>" class="dropdown-item py-2 px-3 border-bottom border-light text-wrap d-flex align-items-start gap-2 notif-item">
                        <div class="rounded-circle <?php echo $bgClass; ?> d-flex align-items-center justify-content-center flex-shrink-0 mt-1" style="width: 30px; height: 30px; font-size: 0.75rem;">
                            <i class="<?php echo $iconClass; ?>"></i>
                        </div>
                        <div class="flex-grow-1" style="min-width: 0;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-dark small text-truncate" style="max-width: 145px;"><?php echo htmlspecialchars($senderLabel); ?></span>
                                <small class="text-muted" style="font-size: 0.7rem;"><?php echo date('H:i', strtotime($uc['created_at'])); ?></small>
                            </div>
                            <div class="text-muted small text-truncate" style="font-size: 0.75rem;"><?php echo htmlspecialchars($cleanMsg); ?></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-4 px-3 text-muted small js-header-chat-empty">
                    <i class="far fa-comments fs-3 text-secondary opacity-50 mb-2 d-block"></i>
                    Tidak ada pesan chat baru
                </div>
            <?php endif; ?>
        </div>
        <div class="p-2 text-center bg-light rounded-bottom-4 border-top">
            <a href="?page=chat" class="small text-success fw-bold text-decoration-none d-block py-1">
                Buka Panel Chat <i class="fas fa-arrow-right ms-1 small"></i>
            </a>
        </div>
    </div>
</div>

<style>
.header-notif-menu {
    z-index: 1060 !important;
}
.header-notif-btn {
    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.header-notif-btn:hover {
    transform: scale(1.08) !important;
}
.notif-item:hover {
    background-color: #f8fafc;
}

/* Titik merah berdenyut di sudut kanan bawah icon */
.notif-red-dot {
    position: absolute !important;
    bottom: 2px !important;
    right: 2px !important;
    width: 10px !important;
    height: 10px !important;
    background: #ef4444 !important;
    border-radius: 50% !important;
    border: 2px solid #fff !important;
    pointer-events: none;
    z-index: 5;
    animation: notif-pulse 1.6s ease-in-out infinite;
}
@keyframes notif-pulse {
    0%   { transform: scale(1);   box-shadow: 0 0 0 0 rgba(239,68,68,0.6); }
    55%  { transform: scale(1.15); box-shadow: 0 0 0 5px rgba(239,68,68,0); }
    100% { transform: scale(1);   box-shadow: 0 0 0 0 rgba(239,68,68,0); }
}
</style>
