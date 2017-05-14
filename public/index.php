<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 18.05.15
 * Time: 10:34
 */

use Faker\Factory;
use InstagramAPI\Checkpoint;
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
//
//$users = Users::find(['id' => 13353]);
//$user = $users->rows[0];
//print_r($user);
//$requestCou = $user->requests;
//$api = new IgApi();
//$api->proxy = $user->proxy;
//$api->username = $user->userName;
//$api->accountId = $user->accountId;
//$api->guid = $user->guid;
//$api->csrftoken = $user->csrftoken;
//Users::where(['id' => $user->id])->update(['login' => 1]);
//if (!file_exists("/home/insta/cookies/" . $user->userName . "-cookies.dat") || $user->logIn === 2) {
//    echo "login account:";
//    $result = $api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
//    Checkpoint::checkPoint($result, $user);
//}
//
//
//$accRow = \OxApp\models\ParseBase::limit([0 => 1])->find(['status' => 0]);
//if ($accRow->count > 0) {
//    $acc = @preg_replace("/[^0-9]/", '', $accRow->rows[0]->account);
//    if (!empty($acc)) {
//        // \OxApp\models\ParseBase::where(['id' => $accRow->rows[0]->id])->update(['status' => 1]);
////        $result = $api->getFeed($acc);
////        print_r($result);
//        //friendships/{$userId}/following/
//        print_r($api->getFollows($acc));
//    }
//}

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
//
//$domains = "";
//$domains = explode("\n", $domains);
//foreach ($domains as $domain) {
//    $proxy = str_replace(["\n", " "], "", $domain);
//    if (Proxy::find(['proxy' => $proxy])->count == 0) {
//        Proxy::add(['proxy' => $proxy]);
//    }
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