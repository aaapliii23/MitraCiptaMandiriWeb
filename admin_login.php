<?php
// Legacy redirect — file dipindah ke auth/admin_login.php pada restrukturisasi bb1067d
// Dibuat kembali untuk kompatibilitas bookmark lama http://mitraciptamandiriweb.test/admin_login.php
header('Location: auth/admin_login.php', true, 302);
exit;
