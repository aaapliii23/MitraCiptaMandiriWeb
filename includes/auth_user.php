<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_logged_in'])) {
    header('Location: ../auth/user_login.php');
    exit;
}