<?php
// Salin file ini menjadi config/secrets.php dan isi kredensial nyata.
// config/secrets.php TIDAK di-commit ke VCS (ada di .gitignore).

// --- Payment Gateway ---
define('PAYMENT_MODE', 'sandbox'); // 'mock' | 'sandbox' | 'production'
define('PAYMENT_SERVER_KEY', '');
define('PAYMENT_CLIENT_KEY', '');
define('PAYMENT_WEBHOOK_SIGNATURE_KEY', '');

// --- WhatsApp Cloud API ---
define('WA_PHONE_NUMBER_ID', '');
define('WA_ACCESS_TOKEN', '');
define('WA_VERIFY_TOKEN', '');

// Secret untuk verifikasi tanda tangan webhook masuk (X-Hub-Signature-256)
define('MCM_WA_WEBHOOK_SECRET', '');

// Nomor WhatsApp admin (alur checkout lama & notifikasi)
define('MCM_WA_ADMIN', '6285793935707');

// Nomor WhatsApp chatbot (tombol mengambang & target balasan otomatis)
define('MCM_WA_CHATBOT', '6285793935707');

// --- Cloudinary (upload gambar) ---
define('CLOUDINARY_CLOUD_NAME', 'your_cloud_name');
define('CLOUDINARY_API_KEY', 'your_api_key');
define('CLOUDINARY_API_SECRET', 'your_api_secret');

// --- Fonnte WhatsApp Gateway ---
define('FONNTE_TOKEN', 'your_fonnte_token'); // https://api.fonnte.com/send -> header Authorization: <token>