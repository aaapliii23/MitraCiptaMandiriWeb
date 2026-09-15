<?php
// Partial favicon MCM — dipakai di semua <head> (publik, admin, LMS, payment).
// Variabel opsional sebelum include: $favicon_base = prefix relatif ke root
// ('' di root via $base_url, '..' di admin/lms/payment, absolut via pg_base_url()).
if (!isset($favicon_base)) $favicon_base = '';
$__fav = rtrim($favicon_base, '/');
$__fav = $__fav === '' ? '' : $__fav . '/';
?>
<link rel="icon" type="image/x-icon" href="<?php echo $__fav; ?>favicon.ico">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $__fav; ?>assets/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $__fav; ?>assets/favicon/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $__fav; ?>assets/favicon/apple-touch-icon.png">
