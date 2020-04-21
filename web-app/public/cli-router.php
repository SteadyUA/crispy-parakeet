<?php

if (php_sapi_name() !== 'cli-server') {
    die("Only for php cli server usage.\n");
}

$res = parse_url($_SERVER["REQUEST_URI"]);
if (file_exists(__DIR__ . $res['path'])) {
    return false;
}

include __DIR__ . '/index.php';
