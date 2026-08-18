<?php
if (file_exists(__DIR__ . '/config_secrets.php')) {
    require_once __DIR__ . '/config_secrets.php';
}

function wa_log_inbound($pdo, $fromNumber, $message, $matchedIntent = null, $userId = null) {
    $stmt = $pdo->prepare("INSERT INTO chat_messages (user_id, wa_number, direction, message, matched_intent) VALUES (?, ?, 'in', ?, ?)");
    $stmt->execute([$userId, $fromNumber, $message, $matchedIntent]);
}

function wa_send_message($pdo, $toNumber, $message, $matchedIntent = null) {
    $stmt = $pdo->prepare("INSERT INTO chat_messages (wa_number, direction, message, matched_intent) VALUES (?, 'out', ?, ?)");
    $stmt->execute([$toNumber, $message, $matchedIntent]);

    if (strpos($toNumber, 'web-') === 0) {
        return true;
    }

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
    return true;
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
                        $rows = $pdo->query("SELECT name, price FROM classes ORDER BY id")->fetchAll();
                        $out = [];
                        foreach ($rows as $r) $out[] = $r['name'] . ' - Rp ' . number_format($r['price'], 0, ',', '.');
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
            $rows = $pdo->query("SELECT name, price FROM classes ORDER BY id")->fetchAll();
            $out = [];
            foreach ($rows as $r) $out[] = $r['name'] . ' - Rp ' . number_format($r['price'], 0, ',', '.');
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