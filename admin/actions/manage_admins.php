<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) exit;

require_once '../../config/database.php';

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $user = trim($_POST['username'] ?? '');
    $pass = password_hash($_POST['password'] ?? '', PASSWORD_DEFAULT);

    if (empty($user)) {
        echo json_encode(['status' => 'error', 'message' => 'Username wajib diisi.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->execute([$user, $pass]);
        echo json_encode(['status' => 'success', 'message' => 'Admin baru berhasil ditambahkan.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Username sudah terdaftar atau terjadi kesalahan.']);
    }
} elseif ($action === 'change_password') {
    $newPass = trim($_POST['new_password'] ?? '');
    if (strlen($newPass) < 6) {
        echo json_encode(['status' => 'error', 'message' => 'Password minimal 6 karakter.']);
        exit;
    }
    $hashed = password_hash($newPass, PASSWORD_DEFAULT);
    $adminUser = $_SESSION['admin_username'] ?? '';
    try {
        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = ?");
        $stmt->execute([$hashed, $adminUser]);
        echo json_encode(['status' => 'success', 'message' => 'Password berhasil diperbarui.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah password.']);
    }
} elseif ($action === 'update') {
    $id = trim($_POST['id'] ?? '');
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    if (empty($id) || empty($user)) {
        echo json_encode(['status' => 'error', 'message' => 'Username wajib diisi.']);
        exit;
    }
    if ($pass !== '' && strlen($pass) < 6) {
        echo json_encode(['status' => 'error', 'message' => 'Password minimal 6 karakter.']);
        exit;
    }

    try {
        if ($pass !== '') {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admins SET username = ?, password = ? WHERE id = ?");
            $stmt->execute([$user, $hashed, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE admins SET username = ? WHERE id = ?");
            $stmt->execute([$user, $id]);
        }
        echo json_encode(['status' => 'success', 'message' => 'Data admin berhasil diperbarui.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Username sudah terdaftar atau terjadi kesalahan.']);
    }
} elseif ($action === 'delete') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['status' => 'success', 'message' => 'Akun admin berhasil dihapus.']);
    }
} elseif ($action === 'bulk_delete') {
    $raw=$_POST['ids']??''; $ids=[]; if(is_array($raw))$ids=$raw; elseif(is_string($raw)&&$raw!==''){ $d=json_decode($raw,true); $ids=is_array($d)?$d:array_filter(array_map('trim',explode(',',$raw))); }
    $ids=array_values(array_unique(array_filter(array_map('intval',$ids))));
    if(empty($ids)){ echo json_encode(['status'=>'error','message'=>'Tidak ada data terpilih']); exit; }
    if(count($ids)>100){ echo json_encode(['status'=>'error','message'=>'Maksimal 100']); exit; }
    $selfId = null;
    try{ $selfRow=$pdo->prepare("SELECT id FROM admins WHERE username=?"); $selfRow->execute([$_SESSION['admin_username'] ?? '']); $selfId=$selfRow->fetchColumn(); }catch(PDOException $e){}
    $ids=array_values(array_filter($ids, fn($v)=> $v!=$selfId));
    if(empty($ids)){ echo json_encode(['status'=>'error','message'=>'Tidak bisa menghapus akun sendiri']); exit; }
    try{ $ph=implode(',',array_fill(0,count($ids),'?')); $stmt=$pdo->prepare("DELETE FROM admins WHERE id IN ($ph)"); $stmt->execute($ids); echo json_encode(['status'=>'success','message'=>$stmt->rowCount().' admin berhasil dihapus']); }catch(PDOException $e){ echo json_encode(['status'=>'error','message'=>'Gagal hapus massal']); }
    exit;
}


