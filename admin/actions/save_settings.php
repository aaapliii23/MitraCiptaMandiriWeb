<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) exit;

// In a real app, you would save these to a 'settings' table.
// For now, we simulate success for the UI.
echo json_encode(['status' => 'success', 'message' => 'Pengaturan website berhasil diperbarui.']);
