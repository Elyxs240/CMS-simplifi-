<?php
$env = fn($k, $d=null) => getenv($k) !== false ? getenv($k) : $d;
define('DB_HOST', $env('DB_HOST', 'localhost'));
define('DB_NAME', $env('DB_NAME', 'cms_simplifie'));
define('DB_USER', $env('DB_USER', 'root'));
define('DB_PASS', $env('DB_PASS', ''));

define('BASE_URL', '');
define('APP_NAME', 'CMS Simplifié');
date_default_timezone_set('Europe/Paris');

