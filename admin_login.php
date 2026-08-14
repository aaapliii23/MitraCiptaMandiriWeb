<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin/dashboard.php");
    exit;
}

require_once 'includes/db_config.php';

$errorMessage = '';
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || 
          (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 'success']);
                    exit;
                } else {
                    header("Location: admin/dashboard.php");
                    exit;
                }
            } else {
                $errorMessage = 'Username atau password salah.';
            }
        } catch (PDOException $e) {
            $errorMessage = 'Terjadi kesalahan sistem.';
        }
    }

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $errorMessage]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login Administrator | Mitra Cipta Mandiri</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0c4a6e;
            --secondary-color: #0ea5e9;
            --accent-color: #f59e0b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #0c4a6e 50%, #0284c7 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 15px;
            margin: 0;
            position: relative;
            overflow-x: hidden;
        }

        /* Decorative background glow shapes */
        .bg-shape-1 {
            position: absolute;
            width: 350px;
            height: 350px;
            background: rgba(14, 165, 233, 0.25);
            filter: blur(90px);
            border-radius: 50%;
            top: -50px;
            left: -50px;
            pointer-events: none;
        }
        .bg-shape-2 {
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(245, 158, 11, 0.15);
            filter: blur(100px);
            border-radius: 50%;
            bottom: -80px;
            right: -80px;
            pointer-events: none;
        }

        /* Responsive Login Card */
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            width: 100%;
            max-width: 430px;
            padding: 2.5rem 2rem;
            position: relative;
            z-index: 10;
            margin: auto;
            transition: all 0.3s ease;
        }

        .brand-logo {
            height: 52px;
            width: auto;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
            transition: height 0.3s ease;
        }

        .brand-divider {
            border-color: var(--primary-color) !important;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 50rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px -5px rgba(14, 165, 233, 0.4);
            font-size: 1rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(14, 165, 233, 0.6);
            color: #fff;
        }

        .form-control {
            font-size: 0.95rem;
            border-left: none;
            background-color: #f8fafc;
            height: 46px;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.25rem rgba(14, 165, 233, 0.15);
            background-color: #ffffff;
        }

        .input-group-text {
            background-color: #f8fafc;
            border-right: none;
            font-size: 1rem;
            padding-left: 14px;
            padding-right: 14px;
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--secondary-color);
            background-color: #ffffff;
        }

        .toggle-password {
            cursor: pointer;
            border-left: none !important;
            border-right: 1px solid #dee2e6 !important;
            background-color: #f8fafc;
        }

        .back-to-home {
            color: #64748b;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .back-to-home:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }

        /* Responsive Breakpoints */
        @media (max-width: 576px) {
            body {
                padding: 15px 10px;
            }

            .login-card {
                padding: 1.75rem 1.25rem;
                border-radius: 1.2rem;
            }

            .brand-logo {
                height: 42px;
            }

            .brand-text-sm {
                font-size: 0.7rem !important;
            }

            h4.fw-bold {
                font-size: 1.25rem;
            }

            .bg-shape-1 {
                width: 250px;
                height: 250px;
            }

            .bg-shape-2 {
                width: 280px;
                height: 280px;
            }
        }
    </style>
</head>
<body>
    <div class="bg-shape-1"></div>
    <div class="bg-shape-2"></div>

    <div class="login-card">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center mb-3">
                <img src="assets/img/logo.png" alt="MCM Logo" class="brand-logo">
                <div class="ms-2 ps-2 border-start border-2 brand-divider text-start">
                    <span class="fw-bold d-block text-dark brand-text-sm" style="font-size: 0.78rem; letter-spacing: 1px; line-height: 1.1;">MITRA CIPTA</span>
                    <span class="fw-bold d-block text-dark brand-text-sm" style="font-size: 0.78rem; letter-spacing: 1px; line-height: 1.1;">MANDIRI</span>
                </div>
            </div>
            <h4 class="fw-bold text-dark mb-1">Akses Administrator</h4>
            <p class="text-muted small mb-0">Silakan login untuk mengelola sistem MCM.</p>
        </div>

        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center py-2 px-3 mb-4 rounded-3" role="alert">
                <i class="fas fa-exclamation-circle me-2 fs-5 flex-shrink-0"></i>
                <div class="small fw-medium flex-grow-1"><?php echo htmlspecialchars($errorMessage); ?></div>
                <button type="button" class="btn-close ms-2 p-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="admin_login.php" id="privateAdminLoginForm">
            <div class="mb-3">
                <label for="username" class="form-label small fw-bold text-secondary">Username</label>
                <div class="input-group">
                    <span class="input-group-text text-muted"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required autocomplete="username" autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label small fw-bold text-secondary">Password</label>
                <div class="input-group">
                    <span class="input-group-text text-muted"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required autocomplete="current-password">
                    <span class="input-group-text toggle-password text-muted" id="togglePasswordBtn" title="Tampilkan/Sembunyikan Password">
                        <i class="fas fa-eye" id="togglePasswordIcon"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100 mb-3">
                <i class="fas fa-sign-in-alt me-2"></i> Masuk ke Dashboard
            </button>
        </form>

        <div class="text-center mt-3 pt-3 border-top d-flex flex-column gap-1">
            <a href="index.php" class="back-to-home"><i class="fas fa-arrow-left me-1"></i> Kembali ke Beranda</a>
            <small class="text-muted mt-1" style="font-size: 0.75rem;">&copy; <?php echo date('Y'); ?> MCM - Mitra Cipta Mandiri</small>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        const togglePasswordBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');
        const togglePasswordIcon = document.getElementById('togglePasswordIcon');

        if (togglePasswordBtn && passwordInput) {
            togglePasswordBtn.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                togglePasswordIcon.classList.toggle('fa-eye');
                togglePasswordIcon.classList.toggle('fa-eye-slash');
            });
        }
    </script>
</body>
</html>
