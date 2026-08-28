<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/cloudinary.php';
$rows=$pdo->query("SELECT id, image FROM facility_locations WHERE image LIKE 'assets/%'")->fetchAll(PDO::FETCH_ASSOC);
echo "found ".count($rows)." assets to migrate\n";
$ok=0;$fail=0;$skip=0;
foreach($rows as $r){
  $path=$r['image'];
  $full=dirname(__DIR__) . '/' . ltrim($path,'/');
  if(!is_file($full)){ echo "skip missing $path\n"; $skip++; continue; }
  $ext=strtolower(pathinfo($path, PATHINFO_EXTENSION));
  if(!in_array($ext,['jpg','jpeg','png','webp','heic'],true)){ $skip++; continue; }
  $file=['name'=>basename($path),'type'=>mime_content_type($full)?:'image/'.$ext,'tmp_name'=>$full,'error'=>0,'size'=>filesize($full)];
  $res=uploadImageToCloudinary($file, 'mcm/fasilitas');
  if(!$res['ok']){ echo "fail $path: ".$res['error']."\n"; $fail++; continue; }
  $url=$res['url']; $pid=$res['public_id'] ?? cloudinaryPublicIdFromUrl($url);
  $stmt=$pdo->prepare('UPDATE facility_locations SET image=?, image_public_id=? WHERE id=?');
  $stmt->execute([$url, $pid, $r['id']]);
  echo "ok $path -> $url\n";
  $ok++;
  usleep(200000);
}
echo "done ok=$ok fail=$fail skip=$skip\n";
