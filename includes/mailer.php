<?php
// includes/mailer.php — wrapper PHPMailer untuk MCM
// SEMENTARA: pakai SMTP portfgil.my.id, nanti ganti ke mitraciptamandiri.com (lihat config/mail_config.php)

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/phpmailer/src/Exception.php';
require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/src/SMTP.php';

/**
 * Kirim email via SMTP hosting
 * @param string $to Email tujuan
 * @param string $subject Subjek
 * @param string $htmlBody Body HTML
 * @param string $textBody Body plain text fallback
 * @return array ['success'=>bool, 'message'=>string]
 */
function mcm_send_email($to, $subject, $htmlBody, $textBody = '') {
    $config = require __DIR__ . '/../config/mail_config.php';
    
    // Validasi config
    if (empty($config['password'])) {
        $msg = 'SMTP password belum diisi di config/mail_config.php';
        error_log("[MAILER] $msg");
        // Fallback: tetap log ke file untuk debugging
        mcm_log_email_fallback($to, $subject, $htmlBody, "CONFIG: $msg");
        return ['success' => false, 'message' => $msg];
    }

    $mail = new PHPMailer(true);
    try {
        // Server settings — SEMENTARA: portfgil.my.id
        $mail->isSMTP();
        $mail->Host       = $config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['username'];
        $mail->Password   = $config['password'];
        $mail->SMTPSecure = $config['encryption']; // 'ssl' untuk 465
        $mail->Port       = $config['port'];
        $mail->Timeout    = $config['timeout'] ?? 15;
        $mail->SMTPDebug  = $config['debug'] ?? 0;
        $mail->CharSet    = 'UTF-8';

        // Recipients
        $mail->setFrom($config['from_address'], $config['from_name']);
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = $textBody ?: strip_tags($htmlBody);

        $mail->send();

        // Sukses — tetap log untuk audit (opsional)
        error_log("[MAILER] Email terkirim ke $to | Subject: $subject");
        return ['success' => true, 'message' => 'Email terkirim'];

    } catch (Exception $e) {
        $error = $mail->ErrorInfo ?: $e->getMessage();
        error_log("[MAILER] Gagal kirim ke $to: $error");

        // Fallback: log ke file supaya link reset tetap bisa diambil saat SMTP gagal (untuk debugging)
        // SEMENTARA: fallback ini sengaja dibiarkan aktif, hapus/nonaktifkan setelah SMTP stabil jika mau
        mcm_log_email_fallback($to, $subject, $htmlBody, "SMTP ERROR: $error");

        return ['success' => false, 'message' => "Gagal kirim email: $error"];
    }
}

/**
 * Fallback log ke file — untuk debugging saat SMTP gagal
 * File: logs/reset_links.log (jangan commit, sudah di .gitignore)
 */
function mcm_log_email_fallback($to, $subject, $htmlBody, $reason = '') {
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
    $logFile = $logDir . '/reset_links.log';
    
    // Extract link reset dari body jika ada
    $link = '';
    if (preg_match('/https?:\/\/[^\s"\'<>]+token=[^\s"\'<>]+/', $htmlBody, $m)) {
        $link = $m[0];
    }
    
    $entry = date('Y-m-d H:i:s') . " | To: $to | Subject: $subject";
    if ($reason) $entry .= " | Reason: $reason";
    if ($link) $entry .= " | Link: $link";
    $entry .= "\n";
    
    @file_put_contents($logFile, $entry, FILE_APPEND);
}
