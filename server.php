<?php

$publicPath = getcwd();

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

$checkPath = $publicPath.'/public'.$uri;
$exists = file_exists($checkPath) ? 'YES' : 'NO';
file_put_contents('php://stdout', "PATH CHECK: $checkPath -> $exists\n");

if ($uri !== '/' && file_exists($checkPath)) {
    return false;
}

require_once $publicPath.'/public/index.php';
