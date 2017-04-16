<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 18.05.15
 * Time: 10:34
 */

use ox\freenom\Freenom_Client;
use ox\freenom\Freenom_Contact;
use ox\freenom\Freenom_Domain;
use ox\freenom\Freenom_Service;
use ox\freenom\Freenom_Authorize_Exception;
use ox\freenom\Freenom_Service_Exception;
use ox\freenom\Freenom_Request_Exception;
use ox\freenom\Freenom_Exception;
use OxApp\helpers\FreenomReg;
use OxApp\models\InstBase;
use OxApp\models\ProfileGenerate;
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
//require(__DIR__ . "/../OxApp/Routes.php");
$file = file('base.txt');
foreach ($file as $str) {
    $acc = @preg_replace("/[^0-9]/", '', $str);
    if (InstBase::where(['account' => $acc])->find()->count == 0) {
        InstBase::add(['account' => $acc]);
        echo $acc;
    }else {
        echo "no";
    }
}
//$smiles=explode("\n", $smiles);
//foreach ($smiles as $key=>$smile){
//    ProfileGenerate::where(['id'=>$key])->update(['description' => $smile]);
//}
//
//$urls = explode("\n", $urls);
//foreach ($urls as $key => $val) {
//    ProfileGenerate::add(['description' => $texts[$key], 'url' => $val]);
//}
//for ($i = 30001; $i < 30400; $i++) {
//    Proxy::add(['proxy' => '46.105.124.207:' . $i]);
//}
//$base=file_get_contents('/var/www/ox/instagram/public/base.txt');
//echo $base;
//
//$base=explode("\n",$base);
//foreach ($base as $acc) {
//    $acc = preg_replace("/[^0-9]/", '', $acc);
//    echo "$acc";
//    InstBase::add(['account'=>$acc]);
//}
//print_r(FreenomReg::regNewDomain("dssdasddsfdsf.tk","sdfsd"));
//
//
//
//$request = FreenomReg::request("https://my.freenom.com/clientarea.php");
////print_r($request);
//
//preg_match("/(name=\"token\" value=\"(.*)\")/i",
//    $request[1], $matches);
//$token = $matches[2];
//echo "Token: " . $token;
//$request = FreenomReg::request("https://my.freenom.com/dologin.php", [
//    'password' => '047b014138',
//    'rememberme' => 'on',
//    'token' => $token,
//    'username' => 'maste.craft@gmail.com'
//]);
//
//$request = FreenomReg::request('https://my.freenom.com/domains.php');
//
//$domain = rand(99999999, 2123122) . "-live";
//
//echo $domain;
//$request = FreenomReg::request("https://my.freenom.com/includes/domains/fn-available.php", [
//    'domain' => $domain,
//    'tld' => ''
//], 'https://my.freenom.com/domains.php');
//print_r(json_decode($request[1])->free_domains);
//
////
//$request = FreenomReg::request("https://my.freenom.com/includes/domains/fn-additional.php", [
//    'domain' => $domain,
//    'tld' => '.cf'
//], 'https://my.freenom.com/domains.php');
//print_r($request);
//
//$request = FreenomReg::request('https://my.freenom.com/cart.php?a=view');
//print_r($request[1]);