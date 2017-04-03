<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 18.05.15
 * Time: 10:34
 */
use Ox\DataBase\DbConfig;
use OxApp\helpers\IgApi;
use OxApp\models\Proxy;

$loader = require __DIR__ . '/../vendor/autoload.php';

$config = include("migrations-db.php");
DbConfig::$dbhost = $config["host"];
DbConfig::$dbname = $config["dbname"];
DbConfig::$dbuser = $config["user"];
DbConfig::$dbuserpass = $config["password"];


$proxy = Proxy::limit([0, 10])->find(['status' => 0]);
$api = new IgApi();
if ($proxy->count > 0) {
    foreach ($proxy->rows as $row) {
        $api->proxy = $row->proxy;
        $api->create();
        Proxy::where(['id' => $row->id])->update(['status' => 1]);
    }
}

//$create->login();
//$create->changeProfilePicture('http://www.codeproject.com/KB/GDI-plus/ImageProcessing2/img.jpg');