<?php
session_start();
if (isset($_SESSION['user_logged_in'])) {
    unset($_SESSION['user_logged_in']);
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_email']);
    session_regenerate_id(true);
}
header('Location: ../index.php');
exit;
