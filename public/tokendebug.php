<?php
require __DIR__ . '/../vendor/autoload.php';

use Isklad\MyorderCartWidgetMiddleware\ClientTokenStorage;
use Isklad\MyorderCartWidgetMiddleware\IskladEnv;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$env = IskladEnv::fromIniFile(__DIR__ . '/../env.ini');
$clientTokenStorage = new ClientTokenStorage($env);

var_dump($clientTokenStorage->getSavedTokenDto());