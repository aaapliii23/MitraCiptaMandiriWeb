<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login Administrator | Mitra Cipta Mandiri</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="shortcut icon" type="image/png" href="../assets/img/logo.png">
    <link rel="apple-touch-icon" href="../assets/img/logo.png">
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
