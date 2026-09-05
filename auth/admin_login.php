<?php
session_start();
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: ../admin/dashboard.php");
    exit;
}

require_once '../config/database.php';
require_once '../includes/security.php';

$errorMessage = '';
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || 
          (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Rate limiting: 5 percobaan / 15 menit per IP+username
    $rateKey = 'admin_login_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . '_' . strtolower(trim($_POST['username'] ?? ''));
    $rl = mcm_rate_limit($rateKey, 5, 900);
    if (!$rl['allowed']) {
        $errorMessage = $rl['message'];
    } else {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $errorMessage = 'Username dan password wajib diisi.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password FROM admins WHERE username = :username LIMIT 1");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['password'])) {
                mcm_rate_limit_reset($rateKey);
                session_regenerate_id(true);
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['last_activity'] = time();
                
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'success']);
                    exit;
                } else {
                    header("Location: ../admin/dashboard.php");
                    exit;
                }
            } else {
                mcm_rate_limit_hit($rateKey);
                $errorMessage = 'Username atau password salah.';
            }
        } catch (PDOException $e) {
            mcm_rate_limit_hit($rateKey);
            $errorMessage = 'Terjadi kesalahan sistem.';
        }
    }
    } // end rate limit else

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $errorMessage]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<?php include '../admin/includes/admin_login_head.php'; ?>
<body>
    <?php include '../admin/includes/admin_login_body.php'; ?>

    <?php include '../admin/includes/admin_login_scripts.php'; ?>
</body>
</html>
