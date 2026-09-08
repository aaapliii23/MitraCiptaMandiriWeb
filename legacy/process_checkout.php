<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classId = $_POST['class_id'] ?? '';
    $customerName = $_POST['customer_name'] ?? '';
    $customerPhone = $_POST['customer_phone'] ?? '';
    $customerEmail = $_POST['customer_email'] ?? '';
    $customerInstitution = $_POST['customer_institution'] ?? '-';
    $customerAddress = $_POST['customer_address'] ?? '';
    
    if (empty($classId) || empty($customerName) || empty($customerPhone) || empty($customerEmail) || empty($customerAddress)) {
        die("Data tidak lengkap.");
    }
    
    // Get class details
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
    $stmt->execute([$classId]);
    $class = $stmt->fetch();
    
    if (!$class) {
        die("Class not found.");
    }
    
    $amount = $class['price'];
    $orderNumber = 'ORD-' . strtoupper(uniqid()) . '-' . time();
    
    // Insert order (Direct Checkout)
    $stmt = $pdo->prepare("INSERT INTO orders (order_number, customer_name, customer_phone, customer_email, customer_address, customer_institution, class_id, amount, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
    if ($stmt->execute([$orderNumber, $customerName, $customerPhone, $customerEmail, $customerAddress, $customerInstitution, $classId, $amount])) {
        // Prepare Formal WhatsApp Message
        $adminPhone = '628978902864';
        $message = "*KONFIRMASI PENDAFTARAN BARU - MCM*\n\n";
        $message .= "Halo Admin Mitra Cipta Mandiri,\n";
        $message .= "Saya ingin mengonfirmasi pendaftaran pelatihan saya dengan detail sebagai berikut:\n\n";
        
        $message .= "*DETAIL PROGRAM*\n";
        $message .= "- ID Pesanan: *" . $orderNumber . "*\n";
        $message .= "- Program: *" . $class['name'] . "*\n";
        $message .= "- Investasi: *Rp " . number_format($amount, 0, ',', '.') . "*\n\n";
        
        $message .= "*DATA DIRI PESERTA*\n";
        $message .= "- Nama: *" . $customerName . "*\n";
        $message .= "- WhatsApp: *" . $customerPhone . "*\n";
        $message .= "- Email: *" . $customerEmail . "*\n";
        $message .= "- Instansi: *" . $customerInstitution . "*\n";
        $message .= "- Alamat: *" . $customerAddress . "*\n\n";
        
        $message .= "*BUKTI DIGITAL*\n";
        $message .= "Lihat Bukti Pendaftaran: " . (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . dirname(dirname($_SERVER['PHP_SELF'])) . "/payment/generate_pdf.php?order=" . $orderNumber . "\n\n";
        
        $message .= "Terima kasih. Mohon segera diproses pendaftaran saya.";
        
        $waLink = "https://wa.me/" . $adminPhone . "?text=" . urlencode($message);
        
        // Save WA link in session so PDF page can provide a "Continue to WA" button if needed
        $_SESSION['last_wa_link'] = $waLink;
        
        // Redirect to PDF Generation Page with auto-print
        header("Location: ../payment/generate_pdf.php?order=" . $orderNumber . "&print=true");
        exit;
    } else {
        die("Failed to create order. Please try again.");
    }
} else {
    die("Invalid request.");
}
?>
