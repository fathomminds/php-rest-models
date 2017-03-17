<?php
include_once  __DIR__ . '/vendor/autoload.php';
$env = is_file(__DIR__.'/.env') ? '.env' : '.env-example';
if (is_file(__DIR__. '/' . $env)) {
    $dotenv = new Dotenv\Dotenv(__DIR__, $env);
    $dotenv->load();
}
