<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_logged_in'])) {
    header('Location: /user/user_login.php');
    exit;
}