<?php
if (file_exists(__DIR__ . '/../config/secrets.php')) {
    require_once __DIR__ . '/../config/secrets.php';
}

function wa_log_inbound($pdo, $fromNumber, $message, $matchedIntent = null, $userId = null) {
    $stmt = $pdo->prepare("INSERT INTO chat_messages (user_id, wa_number, direction, sender_type, message, matched_intent) VALUES (?, ?, 'in', 'visitor', ?, ?)");
    $stmt->execute([$userId, $fromNumber, $message, $matchedIntent]);
}

function wa_normalize_number($number) {
    $d = preg_replace('/\D+/', '', $number);
    if ($d === '') return '';
    if (strpos($d, '0') === 0) $d = '62' . substr($d, 1);
    return $d;
}
function sendWhatsAppNotification($toNumber, $message) {
    $to = wa_normalize_number($toNumber);
    if (!preg_match('/^62[0-9]{9,13}$/', $to)) {
        return ['ok' => false, 'error' => 'Format nomor tidak valid: ' . $toNumber];
    }
    if (trim($message) === '') {
        return ['ok' => false, 'error' => 'Pesan kosong'];
    }
    $token = defined('FONNTE_TOKEN') ? FONNTE_TOKEN : '';
    if ($token === '') {
        error_log("[Fonnte] FONNTE_TOKEN kosong, skip kirim ke $to");
        return ['ok' => false, 'error' => 'FONNTE_TOKEN belum dikonfigurasi'];
    }
    $ch = curl_init('https://api.fonnte.com/send');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Authorization: ' . $token],
        CURLOPT_POSTFIELDS => ['target' => $to, 'message' => $message],
        CURLOPT_TIMEOUT => 15,
    ]);
    $resp = curl_exec($ch);
    $err = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($resp === false) {
        error_log("[Fonnte] curl gagal ke $to: $err");
        return ['ok' => false, 'error' => 'Koneksi Fonnte gagal: ' . $err];
    }
    $data = json_decode($resp, true);
    $ok = $data && isset($data['status']) && ($data['status'] == true || $data['status'] === 'true' || $data['status'] == 1);
    // Fonnte kadang pakai {"status":false,"reason":"..."}
    if (!$ok) {
        $reason = $data['reason'] ?? $data['detail'] ?? $resp;
        if ($code === 401) $reason = 'Token invalid / device tidak aktif';
        error_log("[Fonnte] gagal ke $to (HTTP $code): " . substr($resp, 0, 500));
        return ['ok' => false, 'error' => is_string($reason) ? $reason : json_encode($reason)];
    }
    return ['ok' => true, 'data' => $data];
}
function wa_send_message($pdo, $toNumber, $message, $matchedIntent = null, $senderType = 'bot') {
    if (!in_array($senderType, ['bot','admin','visitor'], true)) $senderType = 'bot';
    $stmt = $pdo->prepare("INSERT INTO chat_messages (wa_number, direction, sender_type, message, matched_intent) VALUES (?, 'out', ?, ?, ?)");
    $stmt->execute([$toNumber, $senderType, $message, $matchedIntent]);

    if (strpos($toNumber, 'web-') === 0) {
        return true;
    }

    // ponytail: Fonnte first (server-side), fallback ke Cloud API, jangan gagalkan flow utama
    $r = sendWhatsAppNotification($toNumber, $message);
    if ($r['ok']) return true;
    error_log('[Fonnte] wa_send_message gagal ke ' . $toNumber . ': ' . ($r['error'] ?? 'unknown'));
    // log sudah di sendWhatsAppNotification, lanjut fallback
    if (defined('WA_ACCESS_TOKEN') && WA_ACCESS_TOKEN !== '' && defined('WA_PHONE_NUMBER_ID') && WA_PHONE_NUMBER_ID !== '') {
        $ch = curl_init('https://graph.facebook.com/v19.0/' . WA_PHONE_NUMBER_ID . '/messages');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . WA_ACCESS_TOKEN, 'Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode([
                'messaging_product' => 'whatsapp',
                'to' => $toNumber,
                'type' => 'text',
                'text' => ['body' => $message],
            ]),
        ]);
        $resp = curl_exec($ch);
        curl_close($ch);
        return $resp !== false;
    }
    return $r['ok'];
}

function wa_seed_intent_defaults($pdo) {
    $seed = [
        ['kursus', 'kursus, program, kelas, pelatihan, materi, modul, belajar', "Program pelatihan MCM:\n- {classes}\n\nUntuk jadwal & harga, sebutkan program yang Anda minati."],
        ['jadwal', 'jadwal, mulai, kapan, tanggal, schedule', "Jadwal pelatihan MCM tertera di masing-masing program di halaman Program. Untuk detail tanggal mulai, sebutkan program yang Anda minati."],
        ['harga', 'harga, biaya, bayar, investasi, berapa, price, tarif', "Harga program MCM:\n- {prices}"],
        ['pendaftaran', 'daftar, daftarkan, register, ikut, enroll', "Cara daftar: pilih program di halaman Program, klik 'Daftar & Bayar', isi data diri, lalu selesaikan pembayaran. Setelah lunas, akses LMS langsung terbuka."],
        ['kontak', 'alamat, lokasi, dimana, telepon, hubungi, kontak', "Untuk info lebih lanjut, silakan hubungi admin via WhatsApp ini. Lokasi & detail MCM tersedia di halaman Tentang website kami."],
        ['penguji', 'penguji, asesor, guru, instruktur', "Penguji MCM adalah asesor berpengalaman. Lihat latar belakang penguji di halaman Profil Penguji website kami."],
    ];
    $stmt = $pdo->prepare("INSERT IGNORE INTO chatbot_intents (intent, keywords, reply) VALUES (?, ?, ?)");
    foreach ($seed as $row) {
        $stmt->execute($row);
    }
}

function wa_intents_map($pdo) {
    static $cache = null;
    if ($cache !== null) return $cache;
    $cache = [];
    try {
        $rows = $pdo->query("SELECT intent, keywords FROM chatbot_intents WHERE enabled = 1")->fetchAll();
        foreach ($rows as $r) {
            $kws = array_values(array_filter(array_map('trim', explode(',', $r['keywords']))));
            if ($kws) $cache[$r['intent']] = $kws;
        }
    } catch (PDOException $e) {}
    return $cache;
}

function wa_match_intent($message, $pdo = null) {
    $msg = strtolower($message);
    $defaults = [
        'kursus' => ['kursus', 'program', 'kelas', 'pelatihan', 'materi', 'modul', 'belajar'],
        'jadwal' => ['jadwal', 'mulai', 'kapan', 'tanggal', 'schedule'],
        'harga' => ['harga', 'biaya', 'bayar', 'investasi', 'berapa', 'price', 'tarif'],
        'pendaftaran' => ['daftar', 'daftarkan', 'register', 'ikut', 'enroll'],
        'kontak' => ['alamat', 'lokasi', 'dimana', 'telepon', 'hubungi', 'kontak'],
        'penguji' => ['penguji', 'asesor', 'guru', 'instruktur'],
    ];
    $intents = ($pdo !== null) ? (wa_intents_map($pdo) ?: $defaults) : $defaults;
    $best = null;
    $bestPos = PHP_INT_MAX;
    foreach ($intents as $intent => $keywords) {
        foreach ($keywords as $kw) {
            $kw = strtolower(trim($kw));
            if ($kw === '') continue;
            $pos = strpos($msg, $kw);
            if ($pos !== false && $pos < $bestPos) {
                $bestPos = $pos;
                $best = $intent;
            }
        }
    }
    return $best;
}

function wa_intent_reply($pdo, $intent) {
    if ($pdo !== null && $intent !== null) {
        try {
            $stmt = $pdo->prepare("SELECT reply FROM chatbot_intents WHERE intent = ? AND enabled = 1 LIMIT 1");
            $stmt->execute([$intent]);
            $dbReply = $stmt->fetchColumn();
            if ($dbReply) {
                if (strpos($dbReply, '{classes}') !== false) {
                    try {
                        $rows = $pdo->query("SELECT name FROM classes ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);
                        if ($rows) $dbReply = str_replace('{classes}', implode("\n- ", $rows), $dbReply);
                    } catch (PDOException $e) {}
                }
                if (strpos($dbReply, '{prices}') !== false) {
                    try {
                        $hasNew = true; try { $pdo->query("SELECT price_online FROM classes LIMIT 1"); } catch (PDOException $e) { $hasNew = false; }
                        $rows = $hasNew ? $pdo->query("SELECT name, price, price_online, price_offline, mode_available FROM classes ORDER BY id")->fetchAll() : $pdo->query("SELECT name, price FROM classes ORDER BY id")->fetchAll();
                        $out = [];
                        foreach ($rows as $r) {
                            if ($hasNew) {
                                $p = (int)($r['price'] ?? 0);
                                $po = isset($r['price_online']) && (int)$r['price_online'] > 0 ? (int)$r['price_online'] : (int)round($p*0.8);
                                $pf = isset($r['price_offline']) && (int)$r['price_offline'] > 0 ? (int)$r['price_offline'] : $p;
                                $ma = $r['mode_available'] ?? 'both';
                                if ($ma === 'online') $out[] = $r['name'] . ' (Online) - Rp ' . number_format($po,0,',','.');
                                elseif ($ma === 'offline') $out[] = $r['name'] . ' (Offline) - Rp ' . number_format($pf,0,',','.');
                                else {
                                    if ($po !== $pf) $out[] = $r['name'] . ' - Mulai Rp ' . number_format(min($po,$pf),0,',','.') . ' (Offline Rp '.number_format($pf,0,',','.').' / Online Rp '.number_format($po,0,',','.').')';
                                    else $out[] = $r['name'] . ' - Rp ' . number_format($p,0,',','.');
                                }
                            } else {
                                $out[] = $r['name'] . ' - Rp ' . number_format($r['price'], 0, ',', '.');
                            }
                        }
                        if ($out) $dbReply = str_replace('{prices}', implode("\n- ", $out), $dbReply);
                    } catch (PDOException $e) {}
                }
                return $dbReply;
            }
        } catch (PDOException $e) {}
    }
    if ($intent === 'kursus') {
        try {
            $rows = $pdo->query("SELECT name FROM classes ORDER BY id")->fetchAll(PDO::FETCH_COLUMN);
            if ($rows) return "Program pelatihan MCM:\n- " . implode("\n- ", $rows) . "\n\nUntuk jadwal & harga, sebutkan program yang Anda minati.";
        } catch (PDOException $e) {}
        return "Tersedia berbagai program pelatihan MCM. Silakan cek halaman Program di website kami.";
    }
    if ($intent === 'harga') {
        try {
            $hasNew = true; try { $pdo->query("SELECT price_online FROM classes LIMIT 1"); } catch (PDOException $e) { $hasNew = false; }
            $rows = $hasNew ? $pdo->query("SELECT name, price, price_online, price_offline, mode_available FROM classes ORDER BY id")->fetchAll() : $pdo->query("SELECT name, price FROM classes ORDER BY id")->fetchAll();
            $out = [];
            foreach ($rows as $r) {
                if ($hasNew) {
                    $p = (int)($r['price'] ?? 0);
                    $po = isset($r['price_online']) && (int)$r['price_online'] > 0 ? (int)$r['price_online'] : (int)round($p*0.8);
                    $pf = isset($r['price_offline']) && (int)$r['price_offline'] > 0 ? (int)$r['price_offline'] : $p;
                    $ma = $r['mode_available'] ?? 'both';
                    if ($ma === 'online') $out[] = $r['name'] . ' (Online) - Rp ' . number_format($po,0,',','.');
                    elseif ($ma === 'offline') $out[] = $r['name'] . ' (Offline) - Rp ' . number_format($pf,0,',','.');
                    else {
                        if ($po !== $pf) $out[] = $r['name'] . ' - Mulai Rp ' . number_format(min($po,$pf),0,',','.') . ' (Offline Rp '.number_format($pf,0,',','.').' / Online Rp '.number_format($po,0,',','.').')';
                        else $out[] = $r['name'] . ' - Rp ' . number_format($p,0,',','.');
                    }
                } else {
                    $out[] = $r['name'] . ' - Rp ' . number_format($r['price'], 0, ',', '.');
                }
            }
            if ($out) return "Harga program MCM:\n- " . implode("\n- ", $out);
        } catch (PDOException $e) {}
        return "Harga program MCM bervariasi. Silakan cek halaman Program di website kami.";
    }
    $replies = [
        'jadwal' => "Jadwal pelatihan MCM tertera di masing-masing program di halaman Program. Untuk detail tanggal mulai, sebutkan program yang Anda minati.",
        'pendaftaran' => "Cara daftar: pilih program di halaman Program, klik 'Daftar & Bayar', isi data diri, lalu selesaikan pembayaran. Setelah lunas, akses LMS langsung terbuka.",
        'kontak' => "Untuk info lebih lanjut, silakan hubungi admin via WhatsApp ini. Lokasi & detail MCM tersedia di halaman Tentang website kami.",
        'penguji' => "Penguji MCM adalah asesor berpengalaman. Lihat latar belakang penguji di halaman Profil Penguji website kami.",
    ];
    return $replies[$intent] ?? "Terima kasih! Pertanyaan Anda akan kami teruskan ke admin untuk dijawab lebih lanjut.";
}