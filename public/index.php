<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 18.05.15
 * Time: 10:34
 */

use Faker\Factory;
use OxApp\helpers\IgApi;
use OxApp\models\Domains;
use OxApp\models\HashTags;
use OxApp\models\ParseBase;
use OxApp\models\Proxy;
use OxApp\models\Users;

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

//$accounts = "";
//$domains = explode("\n", $accounts);
//foreach ($domains as $domain) {
//    $proxy = str_replace(["\n", " "], "", $domain);
//    if (ParseBase::find(['account' => $domain])->count == 0) {
//        ParseBase::add(['account' => $domain]);
//    }
//}

//
//$accounts = "";
//$domains = explode("\n", $accounts);
//foreach ($domains as $domain) {
//    $proxy = str_replace(["\n", " "], "", $domain);
//    if (HashTags::find(['tag' => $domain])->count == 0) {
//        HashTags::add(['tag' => $domain]);
//    }
//}
//
//$domains='derentot.cu.cc
//sandritert.cu.cc
//tarejec.cu.cc
//fortronleft.cu.cc
//gotwilevent.cu.cc
//lothatsu.cu.cc
//ratressin.cu.cc
//herbesin.cu.cc
//rilighrol.cu.cc
//hersthetrol.cu.cc
//keddingthat.cu.cc
//justrebled.cu.cc
//rowbetrec.cu.cc
//patithap.cu.cc
//misparfa.cu.cc
//orrophis.cu.cc
//rangehar.cu.cc
//titbite.cu.cc
//persotont.cu.cc
//useretrof.cu.cc';
//$domains = explode("\n", $domains);
//foreach ($domains as $domain) {
//    $proxy = str_replace(["\n", " "], "", $domain);
//    Domains::add(['domain' => $domain]);
//}
//$domains = "";
//$domains = explode("\n", $domains);
//foreach ($domains as $domain) {
//    $proxy=str_replace(["\n"," "],"",$domain);
//    Proxy::add(['proxy' => $proxy]);
//}

//$faker = Factory::create();
//$result = [];
//for ($i = 0; $i < 1000; $i++) {
//    if (mt_rand(0, 4) == 1) {
//        $name = $faker->userName . rand(1100, 2017);
//    } elseif (mt_rand(0, 1) == 0) {
//        $name = $faker->firstNameFemale . $faker->lastName . rand(1100, 2017);
//    } elseif (mt_rand(0, 3) == 1) {
//        $name = $faker->lastName . $faker->firstNameFemale . rand(1100, 2017);
//    } else {
//        $name = $faker->userName;
//    }
//    $name = mb_strtolower($name);
//    $name = str_replace([".", "'", "`"], ['', '', ''], $name);
//    $subdomain = ['.tk', '.ml', '.ga', '.cf', '.gq'];
//
//    $domain = $name . $subdomain[rand(0, count($subdomain) - 1)];
//    $result[$domain] = 1;
//}
//foreach ($result as $domain => $val) {
//    echo $domain . "\n";
//}