<?php
// Router untuk php -S — support docroot = repo dan docroot = parent htdocs
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = urldecode($uri);
$docRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? __DIR__, '/');
// cek file ada di docRoot+uri ATAU __DIR__+uri (cover subfolder)
$pathDoc = $docRoot . $uri;
$pathRepo = __DIR__ . $uri;
// untuk subfolder: /MitraCiptaMandiriWeb/assets/... -> pathRepo = __DIR__/Mitra... (double) tidak ada, tapi pathDoc ada
$exists = (file_exists($pathDoc) && is_file($pathDoc)) || (file_exists($pathRepo) && is_file($pathRepo));
if ($uri !== '/' && $exists) {
    return false;
}
// normalisasi untuk cek 404 (izinkan / dan /index.php dan /Mitra.../index.php)
$norm = rtrim($uri, '/');
if ($norm === '') $norm = '/';
$allow = ['/', '/index.php', '/404.php', '/router.php', '/MitraCiptaMandiriWeb', '/MitraCiptaMandiriWeb/index.php', '/MitraCiptaMandiriWeb/404.php'];
if (!in_array($norm, $allow, true) && !$exists) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    exit;
}
include __DIR__ . '/index.php';
