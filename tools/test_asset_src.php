<?php
require_once dirname(__DIR__) . '/config/database.php';

$_SERVER['SCRIPT_FILENAME'] = 'C:/xampp/htdocs/Mitra Citra Mandiri/index.php';
echo "From root (index.php): " . asset_src('assets/img/hero-bg.jpg') . "\n";
echo "From root uploads: " . asset_src('uploads/gallery/hero_workspace.jpg') . "\n";

$_SERVER['SCRIPT_FILENAME'] = 'C:/xampp/htdocs/Mitra Citra Mandiri/pages/programs.php';
echo "From pages (programs.php): " . asset_src('assets/img/hero-bg.jpg') . "\n";
echo "From pages uploads: " . asset_src('uploads/gallery/hero_workspace.jpg') . "\n";
