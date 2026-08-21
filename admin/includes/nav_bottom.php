<!-- Bottom Navigation for Mobile -->
<div class="mcm-bottom-nav">
    <a href="?page=dashboard" class="mcm-nav-item <?php echo $page == 'dashboard' ? 'active' : ''; ?>">
        <div class="icon-wrapper"><i class="fas fa-th-large"></i></div>
        <span>Dasbor</span>
    </a>
    <a href="?page=orders" class="mcm-nav-item <?php echo $page == 'orders' ? 'active' : ''; ?>">
        <div class="icon-wrapper"><i class="fas fa-shopping-cart"></i></div>
        <span>Pesanan</span>
    </a>
    <a href="?page=classes" class="mcm-nav-item <?php echo $page == 'classes' ? 'active' : ''; ?>">
        <div class="icon-wrapper"><i class="fas fa-book-open"></i></div>
        <span>Paket</span>
    </a>
    <a href="?page=finance" class="mcm-nav-item <?php echo $page == 'finance' ? 'active' : ''; ?>">
        <div class="icon-wrapper"><i class="fas fa-money-bill-wave"></i></div>
        <span>Keuangan</span>
    </a>
    <a href="javascript:void(0)" class="mcm-nav-item" data-bs-toggle="offcanvas" data-bs-target="#adminOffcanvas" role="button">
        <div class="icon-wrapper"><i class="fas fa-bars"></i></div>
        <span>Semua Menu</span>
    </a>
</div>
