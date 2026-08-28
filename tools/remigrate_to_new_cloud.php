<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/cloudinary.php';
$oldCloud = 'ijpgxnt4';
$tables = [
    'gallery' => ['col'=>'image','pub'=>'image_public_id','folder'=>'mcm/gallery'],
    'classes' => ['col'=>'image','pub'=>'image_public_id','folder'=>'mcm/classes'],
    'certifications' => ['col'=>'image','pub'=>'image_public_id','folder'=>'mcm/certs'],
    'facility_locations' => ['col'=>'image','pub'=>'image_public_id','folder'=>'mcm/fasilitas'],
    'instructors' => ['col'=>'image','pub'=>'image_public_id','folder'=>'mcm/instructors'],
    'testimonials' => ['col'=>'image','pub'=>'image_public_id','folder'=>'mcm/testimonials'],
];
$total=0;$ok=0;$fail=0;$skip=0;
foreach($tables as $t=>$cfg){
  $col=$cfg['col']; $pub=$cfg['pub']; $folder=$cfg['folder'];
  try{ $pdo->query("SELECT 1 FROM `$t` LIMIT 1"); }catch(Exception $e){ echo "skip $t no table\n"; continue; }
  $rows=$pdo->query("SELECT id, `$col` as img, `$pub` as pid FROM `$t` WHERE `$col` LIKE '%$oldCloud%'")->fetchAll(PDO::FETCH_ASSOC);
  echo "[$t] found ".count($rows)." old cloud images to remigrate\n";
  foreach($rows as $r){
    $total++;
    $url=$r['img'];
    // download old image to temp
    $tmp=tempnam(sys_get_temp_dir(),'remig');
    $data=@file_get_contents($url);
    if($data===false){ echo " fail download $url\n"; $fail++; @unlink($tmp); continue; }
    file_put_contents($tmp,$data);
    $ext=strtolower(pathinfo(parse_url($url,PHP_URL_PATH),PATHINFO_EXTENSION)) ?: 'jpg';
    if(!in_array($ext,['jpg','jpeg','png','webp','heic'])) $ext='jpg';
    $file=['name'=>basename($url),'type'=>'image/'.$ext,'tmp_name'=>$tmp,'error'=>0,'size'=>filesize($tmp)];
    $res=uploadImageToCloudinary($file, $folder);
    @unlink($tmp);
    if(!$res['ok']){ echo " fail upload $url: ".$res['error']."\n"; $fail++; continue; }
    $newUrl=$res['url']; $newPid=$res['public_id'] ?? cloudinaryPublicIdFromUrl($newUrl);
    $stmt=$pdo->prepare("UPDATE `$t` SET `$col`=?, `$pub`=? WHERE id=?");
    $stmt->execute([$newUrl,$newPid,$r['id']]);
    echo " ok $t#{$r['id']} $url -> $newUrl\n";
    $ok++;
    usleep(300000);
  }
}
// logo in settings
try{
  $row=$pdo->query("SELECT value FROM settings WHERE `key`='logo_url'")->fetch(PDO::FETCH_ASSOC);
  if($row && str_contains($row['value'], $oldCloud)){
    $url=$row['value'];
    $tmp=tempnam(sys_get_temp_dir(),'logo');
    $data=@file_get_contents($url);
    if($data!==false){
      file_put_contents($tmp,$data);
      $ext=strtolower(pathinfo(parse_url($url,PHP_URL_PATH),PATHINFO_EXTENSION)) ?: 'png';
      $file=['name'=>'logo.'.$ext,'type'=>'image/'.$ext,'tmp_name'=>$tmp,'error'=>0,'size'=>filesize($tmp)];
      $res=uploadImageToCloudinary($file, 'mcm/logo');
      @unlink($tmp);
      if($res['ok']){
        $pdo->prepare("UPDATE settings SET value=? WHERE `key`='logo_url'")->execute([$res['url']]);
        $pdo->prepare("UPDATE settings SET value=? WHERE `key`='logo_public_id'")->execute([$res['public_id'] ?? cloudinaryPublicIdFromUrl($res['url'])]);
        echo "ok logo $url -> ".$res['url']."\n"; $ok++;
      } else { echo "fail logo: ".$res['error']."\n"; $fail++; }
    }
  }
}catch(Exception $e){ echo "logo err: ".$e->getMessage()."\n"; }
echo "done total=$total ok=$ok fail=$fail\n";
