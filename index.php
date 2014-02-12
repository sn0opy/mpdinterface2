<?php

$app = include 'lib/base.php';
$app->config('data/config.ini');
$app->config('data/routes.ini');
$app->run();