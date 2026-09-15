<?php
// Salin file ini menjadi config/secrets.php dan isi kredensial nyata.
// config/secrets.php TIDAK di-commit ke VCS (ada di .gitignore).

// --- Payment Gateway ---
// Mode: 'mock' = simulasi lokal (tanpa API), 'sandbox' = uji coba, 'production' = live
define('PAYMENT_MODE', 'sandbox'); // 'mock' | 'sandbox' | 'production'

// Cukup isi 3 field di bawah — kode akan otomatis pakai ketiganya.
// Contoh: Midtrans, Xendit, Tripay, atau gateway lain yang pakai Client ID / API Key / Secret.
define('PAYMENT_GATEWAY_CLIENT_ID', 'your_client_id_here');   // Client ID — untuk frontend (Snap.js / checkout JS)
define('PAYMENT_GATEWAY_API_KEY', 'your_api_key_here');       // API Key / Server Key — untuk auth server-to-server
define('PAYMENT_GATEWAY_SECRET_KEY', 'your_secret_key_here'); // Secret Key — untuk verifikasi webhook/signature

// Opsional: URL endpoint gateway. Kosongkan jika pakai Midtrans (otomatis), atau isi untuk gateway custom.
define('PAYMENT_GATEWAY_API_URL', ''); // ex: https://api.sandbox.midtrans.com/v1/transactions atau https://api.xendit.co/v2/invoices

// Fallback lama (tetap didukung, tidak perlu diisi jika sudah pakai yang baru di atas):
define('PAYMENT_SERVER_KEY', ''); // alias untuk API Key
define('PAYMENT_CLIENT_KEY', ''); // alias untuk Client ID
define('PAYMENT_WEBHOOK_SIGNATURE_KEY', ''); // alias untuk Secret Key

// --- WhatsApp Cloud API ---
define('WA_PHONE_NUMBER_ID', '');
define('WA_ACCESS_TOKEN', '');
define('WA_VERIFY_TOKEN', '');

// Secret untuk verifikasi tanda tangan webhook masuk (X-Hub-Signature-256)
define('MCM_WA_WEBHOOK_SECRET', '');

// Nomor WhatsApp admin (alur checkout lama & notifikasi)
define('MCM_WA_ADMIN', '628978902864');

// Nomor WhatsApp chatbot (tombol mengambang & target balasan otomatis)
define('MCM_WA_CHATBOT', '628978902864');

// --- Cloudinary (upload gambar) ---
define('CLOUDINARY_CLOUD_NAME', 'your_cloud_name');
define('CLOUDINARY_API_KEY', 'your_api_key');
define('CLOUDINARY_API_SECRET', 'your_api_secret');

// --- Fonnte WhatsApp Gateway ---
define('FONNTE_TOKEN', 'your_fonnte_token'); // https://api.fonnte.com/send -> header Authorization: <token>
