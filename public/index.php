<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 18.05.15
 * Time: 10:34
 */

use OxApp\models\Domains;

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
//
//$domains = "lyveroot.tk
//lyveroot.ml
//lyveroot.ga
//lyveroot.cf
//lyveroot.gq
//zorkaholove.tk
//zorkaholove.ml
//zorkaholove.ga
//zorkaholove.cf
//zorkaholove.gq
//freemylove.tk
//freemylove.ml
//freemylove.ga
//freemylove.cf
//freemylove.gq
//mylovefree.tk
//mylovefree.ml
//mylovefree.ga
//mylovefree.cf
//mylovefree.gq
//grosselov.tk
//grosselov.ml
//grosselov.ga
//grosselov.cf
//grosselov.gq
//grossemadam.tk
//grossemadam.ml
//grossemadam.ga
//grossemadam.cf
//grossemadam.gq
//loveroot.tk
//loveroot.ml
//loveroot.cf
//loveroot.gq
//loveroot.ga
//terralove.tk
//terralove.ml
//terralove.ga
//terralove.cf
//terralove.gq";
//$domains = explode("\n", $domains);
//foreach ($domains as $domain) {
//    Domains::add(['domain' => $domain]);
//}
