<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['admin_logged_in'])) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit; }
require_once '../../config/database.php';
try{
  $pdo->exec("CREATE TABLE IF NOT EXISTS `material_categories` (
    `id` int NOT NULL AUTO_INCREMENT, `class_id` int NOT NULL, `name` varchar(100) NOT NULL,
    `sort_order` int NOT NULL DEFAULT 0, `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`), UNIQUE KEY `uq_class_name` (`class_id`,`name`), KEY `idx_class` (`class_id`),
    CONSTRAINT `fk_matcat_class` FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
  $cols=$pdo->query("DESCRIBE materials")->fetchAll(PDO::FETCH_COLUMN);
  if(!in_array('category_id',$cols)){
    $pdo->exec("ALTER TABLE materials ADD COLUMN category_id INT NULL AFTER class_id, ADD KEY idx_category (category_id), ADD CONSTRAINT fk_mat_category FOREIGN KEY (category_id) REFERENCES material_categories(id) ON DELETE SET NULL");
  }
}catch(PDOException $e){}

$action=$_POST['action'] ?? $_GET['action'] ?? '';
if($action==='list'){
  $classId=(int)($_GET['class_id'] ?? 0);
  if(!$classId){ echo json_encode(['status'=>'success','categories'=>[]]); exit; }
  $stmt=$pdo->prepare("SELECT id,name,sort_order FROM material_categories WHERE class_id=? ORDER BY sort_order ASC, name ASC");
  $stmt->execute([$classId]);
  echo json_encode(['status'=>'success','categories'=>$stmt->fetchAll()]);
  exit;
}
if($action==='create'){
  $classId=(int)($_POST['class_id'] ?? 0);
  $name=trim($_POST['name'] ?? '');
  $sort=(int)($_POST['sort_order'] ?? 0);
  if(!$classId||$name===''){ echo json_encode(['status'=>'error','message'=>'Kelas dan nama kategori wajib diisi']); exit; }
  if(mb_strlen($name)>100){ echo json_encode(['status'=>'error','message'=>'Nama maksimal 100 karakter']); exit; }
  $chk=$pdo->prepare("SELECT id FROM classes WHERE id=?"); $chk->execute([$classId]);
  if(!$chk->fetchColumn()){ echo json_encode(['status'=>'error','message'=>'Kelas tidak ditemukan']); exit; }
  try{
    $stmt=$pdo->prepare("INSERT INTO material_categories (class_id,name,sort_order) VALUES (?,?,?)");
    $stmt->execute([$classId,$name,$sort]);
    echo json_encode(['status'=>'success','message'=>'Kategori ditambahkan','id'=>$pdo->lastInsertId()]);
  }catch(PDOException $e){
    if($e->getCode()==23000) echo json_encode(['status'=>'error','message'=>'Nama kategori sudah ada di kelas ini']);
    else echo json_encode(['status'=>'error','message'=>'Gagal: '.$e->getMessage()]);
  }
  exit;
}
if($action==='update'){
  $id=(int)($_POST['id'] ?? 0);
  $name=trim($_POST['name'] ?? '');
  $sort=(int)($_POST['sort_order'] ?? 0);
  if(!$id||$name===''){ echo json_encode(['status'=>'error','message'=>'Data tidak lengkap']); exit; }
  $cur=$pdo->prepare("SELECT class_id FROM material_categories WHERE id=?"); $cur->execute([$id]);
  $classId=$cur->fetchColumn();
  if(!$classId){ echo json_encode(['status'=>'error','message'=>'Kategori tidak ditemukan']); exit; }
  try{
    $stmt=$pdo->prepare("UPDATE material_categories SET name=?, sort_order=? WHERE id=?");
    $stmt->execute([$name,$sort,$id]);
    echo json_encode(['status'=>'success','message'=>'Kategori diperbarui']);
  }catch(PDOException $e){
    if($e->getCode()==23000) echo json_encode(['status'=>'error','message'=>'Nama kategori sudah ada di kelas ini']);
    else echo json_encode(['status'=>'error','message'=>'Gagal: '.$e->getMessage()]);
  }
  exit;
}
if($action==='delete'){
  $id=(int)($_POST['id'] ?? 0);
  if(!$id){ echo json_encode(['status'=>'error','message'=>'ID tidak valid']); exit; }
  $cnt=$pdo->prepare("SELECT COUNT(*) FROM materials WHERE category_id=?"); $cnt->execute([$id]);
  $used=(int)$cnt->fetchColumn();
  $pdo->prepare("DELETE FROM material_categories WHERE id=?")->execute([$id]);
  $msg=$used ? "Kategori dihapus, $used materi jadi Tanpa Kategori" : "Kategori dihapus";
  echo json_encode(['status'=>'success','message'=>$msg]);
  exit;
}
echo json_encode(['status'=>'error','message'=>'Aksi tidak dikenal']);
