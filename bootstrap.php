<?php
include_once  __DIR__ . '/vendor/autoload.php';
if (is_file(__DIR__. '/.env')) {
    $dotenv = new Dotenv\Dotenv(__DIR__);
    $dotenv->load();
}
