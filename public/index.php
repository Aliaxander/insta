<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 18.05.15
 * Time: 10:34
 */
use Ox\DataBase\DbConfig;
use OxApp\helpers\IgApi;
use OxApp\models\Users;

ini_set('display_errors', '1');
require(__DIR__ . '/../OxApp/Routes.php');
$loader = require __DIR__ . '/../vendor/autoload.php';

$config = include("migrations-db.php");
DbConfig::$dbhost = $config["host"];
DbConfig::$dbname = $config["dbname"];
DbConfig::$dbuser = $config["user"];
DbConfig::$dbuserpass = $config["password"];
$api = new IgApi();
//$api->proxy = "164.132.168.121:30963";
//$api->create();
$user = Users::find(['id' => 25])->rows[0];
$api->proxy = $user->proxy;
$api->username = $user->userName;
$api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
