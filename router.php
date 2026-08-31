<?php
// Router untuk php -S — agar file tidak ada menampilkan 404.php kustom
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = __DIR__ . $uri;
// biarkan file statis / php yang ada dijalankan normal
if ($uri !== '/' && file_exists($path) && is_file($path)) {
    return false;
}
// cegah info sensitif: jangan expose path, langsung 404
if ($uri !== '/' && $uri !== '/index.php' && $uri !== '/404.php' && !file_exists($path)) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    exit;
}
include __DIR__ . '/index.php';
