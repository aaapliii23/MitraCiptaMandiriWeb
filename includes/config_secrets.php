<?php
// =============================================================
// MCM - Konfigurasi Secrets (Jangan di-commit ke repository!)
// =============================================================

// Payment Mode: 'mock' | 'sandbox' | 'production'
if (!defined('PAYMENT_MODE')) define('PAYMENT_MODE', 'mock');

// WhatsApp Business API (Meta/Facebook Graph API)
// Isi jika ingin mengaktifkan pengiriman pesan WhatsApp nyata.
// Biarkan kosong untuk mode mock (pesan hanya tersimpan di DB).
if (!defined('WA_ACCESS_TOKEN'))    define('WA_ACCESS_TOKEN', '');
if (!defined('WA_PHONE_NUMBER_ID')) define('WA_PHONE_NUMBER_ID', '');
if (!defined('WA_VERIFY_TOKEN'))    define('WA_VERIFY_TOKEN', 'mcm_wa_verify_token');
