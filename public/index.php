<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 18.05.15
 * Time: 10:34
 */

use OxApp\models\Proxy;

ini_set("allow_url_fopen", true);
ini_set('display_errors', '1');
date_default_timezone_set('Europe/Moscow');
header('Content-type: text/html; charset=utf-8');
header('Access-Control-Allow-Credentials: true');
$allowHeaders = "X-Requested-With, Content-Type, Origin, Authorization, Accept, Client-Security-Token, Accept-Encoding";
header('Access-Control-Allow-Headers: ' . $allowHeaders);
header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, OPTIONS');
header('Access-Control-Allow-Origin: *');
$loader = require __DIR__ . '/../vendor/autoload.php';
require(__DIR__ . "/../config.php");
require(__DIR__ . "/../OxApp/Routes.php");
//for ($i = 41699; $i < 41859; $i++) {
//    Proxy::add(['proxy' => '46.105.124.207:' . $i]);
//}