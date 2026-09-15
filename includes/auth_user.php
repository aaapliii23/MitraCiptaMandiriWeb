<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/security.php';
if (!mcm_check_session_timeout(1800)) {
    header('Location: ../auth/user_login.php?timeout=1');
    exit;
}
if (!isset($_SESSION['user_logged_in'])) {
    header('Location: ../auth/user_login.php');
    exit;
}