<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 18.05.15
 * Time: 10:34
 */
use Ox\DataBase\DbConfig;
use OxApp\helpers\Device;
use OxApp\helpers\IgApi;
use OxApp\helpers\UserAgent;
use OxApp\models\Proxy;

$loader = require __DIR__ . '/../vendor/autoload.php';

$config = include("migrations-db.php");
DbConfig::$dbhost = $config["host"];
DbConfig::$dbname = $config["dbname"];
DbConfig::$dbuser = $config["user"];
DbConfig::$dbuserpass = $config["password"];

//

//$proxy = Proxy::limit([0=>20])->find(['status' => 0]);
$api = new IgApi();
$api->proxy = "164.132.168.121:30007";
$api->create();
//if ($proxy->count > 0) {
//    foreach ($proxy->rows as $row) {
//        Proxy::where(['id' => $row->id])->update(['status' => 1]);
//        $api->proxy = $row->proxy;
//        $api->create();
//    }
//}


//for($i=100;$i<1000;$i++){
//     echo "ifconfig eth0 inet6 add 2001:41d0:0002:ebcf::$i/64\n";
//}

//$create->login();
//$create->changeProfilePicture('http://www.codeproject.com/KB/GDI-plus/ImageProcessing2/img.jpg');