<?php
include_once  __DIR__ . '/vendor/autoload.php';
if (!is_file(__DIR__. '/clusterpoint.php')) {
    copy(__DIR__. '/clusterpoint-example.php', __DIR__. '/clusterpoint.php');
}
if (!is_file(__DIR__. '/.env')) {
    copy(__DIR__. '/.env-example', __DIR__. '/.env');
}
if (is_file(__DIR__. '/.env')) {
    $dotenv = new Dotenv\Dotenv(__DIR__);
    $dotenv->load();
}
