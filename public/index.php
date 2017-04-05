<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 18.05.15
 * Time: 10:34
 */
use Ox\DataBase\DbConfig;

ini_set('display_errors', '1');
require(__DIR__ . '/../OxApp/Routes.php');
$loader = require __DIR__ . '/../vendor/autoload.php';

$config = include("migrations-db.php");
DbConfig::$dbhost = $config["host"];
DbConfig::$dbname = $config["dbname"];
DbConfig::$dbuser = $config["user"];
DbConfig::$dbuserpass = $config["password"];
