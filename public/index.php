<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 18.05.15
 * Time: 10:34
 */

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
require(__DIR__ . "/../OxApp/Routes.php");

//$email = "domainreg480@gmail.com";
//$password = "m9Rgx7UpoTiJjM8k";
//$params['account_email'] = $email;
//$params['account_password'] = $password;
////freenom_SaveContactDetails($params);
//$params['ns1'] = "ns01.freenom.com";
//$params['ns2'] = "ns02.freenom.com";
//$params['ns3'] = "ns03.freenom.com";
//$params['ns4'] = "ns04.freenom.com";
//$params['ns5'] = "ns05.freenom.com";
//
////for($i=0;$i<30;$i++) {
////    $params['domain'] = strtolower(substr(md5(number_format(microtime(true), 7, '', '')), mt_rand(15, 20))) . ".tk";
////    freenom_RegisterFreeDomain($params);
////}
//$domains = [
//    "117f9df7368224e6f.tk",
//    "b17ca8154605283.tk",
//    "1d52ad90b253e7.tk",
//    "a5a085f7e1d3.tk",
//    "25872e3e49e398dc.tk",
//    "6b268d4b98e6e8fc2.tk",
//    "f609717b7c7984783.tk",
//    "025d904bffa1067.tk",
//    "3d790de55fb9.tk",
//    "8ac4e176d39b913.tk",
//    "f7222627013eadbab.tk",
//    "ca2c1ad745cc63b.tk",
//    "c470db24818a61.tk",
//    "409412c96b5a307.tk",
//    "6a5b86898bd3f5.tk",
//    "8eee62aa9a23.tk",
//    "f50ac2e5588bd73e1.tk",
//    "63d8ccb02031cbf75.tk",
//    "5562c8105c10.tk",
//    "2e8d52aeb2bbe61.tk",
//    "2060c2b4de684fd.tk",
//    "d8f9aeeb3206.tk",
//];
//foreach ($domains as $domain) {
//    $params['domain'] = $domain;
//    $params['nameserver'] = 'www.' . $domain;
//    $params["ipaddress"] = '217.182.242.108';
//    freenom_RegisterNameserver($params);
//}
//function freenom_RegisterNameserver($params)
//{
//    $domainname = $params['domain'];
//
//    $params['function'] = 'nameserver/register';
//    $nameserver = $params["nameserver"];
//    $ipaddress = $params["ipaddress"];
//
//    $query = freenom_buildquery(
//        array(
//            "email" => $params['account_email'],
//            "password" => $params['account_password'],
//            "hostname" => $nameserver,
//            "domainname" => $domainname,
//            "ipaddress" => $ipaddress,
//            "method" => 'PUT',
//        )
//    );
//    $response = freenom_put($query, "POST", $params);
//
//    if ($response->error) {
//        return array("error" => $response->error);
//    }
//
//    return "success";
//}
//
//function freenom_RegisterFreeDomain($params)
//{      // {{{
//    $domainname = $params['domain'];
//    $params['function'] = 'domain/register';
//
//    $qstring = array(
//        "function" => 'domain/register',
//        "email" => $params['account_email'],
//        "password" => $params['account_password'],
//        "domainname" => $domainname,
//        "idshield" => "enabled",
//        "domaintype" => "FREE",
//        "period" => '12M',
//        "method" => "POST",
//    );
//
//    if ($params['ns1']) {
//        $qstring["nameserver1"] = $params['ns1'];
//    }
//    if ($params['ns2']) {
//        $qstring["nameserver2"] = $params['ns2'];
//    }
//    if ($params['ns3']) {
//        $qstring["nameserver3"] = $params['ns3'];
//    }
//    if ($params['ns4']) {
//        $qstring["nameserver4"] = $params['ns4'];
//    }
//    if ($params['ns5']) {
//        $qstring["nameserver5"] = $params['ns5'];
//    }
//
//    $query = freenom_buildquery($qstring);
//    $response = freenom_put($query, "POST", $params);
//
//    /* catch generic error */
//    if ($response->status == 'error') {
//        return array("error" => $response->error);
//    }
//
//    if ($response->domain['0']->status != 'REGISTERED') {
//        return array("error" => $response->domain['0']->status);
//    }
//
//    return $response;
//}
//
//function freenom_SaveContactDetails($params)
//{
//    $domainname = "sdfjsdhf83iwjk.tk";
//
//    # owner data
//    $params['function'] = 'contact/register';
//    $qstring = array(
//        "email" => $params['account_email'],
//        "password" => $params['account_password'],
//        "contact_firstname" => "FiTals",
//        "contact_lastname" => "naneba",
//        "contact_organization" => '',
//        "contact_address" => 'Julonala 34',
//        "contact_city" => 'homel',
//        "contact_countrycode" => 'by',
//        "contact_statecode" => 'Minsk',
//        "contact_zipcode" => '3453333',
//        "contact_email" => 'sjdfjhbdsf@gmail.com',
//        "contact_phone" => '+375334566775',
//        "contact_fax" => '',
//        'method' => 'PUT',
//    );
//    $query = freenom_buildquery($qstring);
//    $response = freenom_put($query, "POST", $params);
//
//    print_r($response);
//}
//
//function freenom_put($xml, $callmethod, $params)
//{  // {{{
//    $xurl = "http://api.freenom.com/v2/" . $params["function"] . ".json";
//    $headers = array("Accept: application/x-www-form-urlencoded", "Content-Type: application/x-www-form-urlencoded");
//    $session = curl_init();
//    curl_setopt($session, CURLOPT_URL, $xurl);
//    curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//    curl_setopt($session, CURLOPT_POSTFIELDS, $xml);
//    curl_setopt($session, CURLOPT_HTTPHEADER, $headers);
//    curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
//    curl_setopt($session, CURLOPT_SSL_VERIFYHOST, 0);
//    curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
//    $response = curl_exec($session);
//    print_r($response);
//    if (curl_errno($session)) {
//        return array(
//            'error' => 'curl error: ' . curl_errno($session) . " - " . curl_error($session),
//            'status' => 'error'
//        );
//        curl_close($session);
//
//        return $data;
//    }
//    curl_close($session);
//    sleep(1);
//    return json_decode($response);
//}
//
//// }}}
//
//function freenom_buildquery($formdata)
//{            // {{{
//    $query = "";
//    foreach ($formdata as $k => $v) {
//        if (substr($k, 0, 10) == "nameserver") {
//            $k = "nameserver";
//        }
//        $query .= "" . $k . "=" . urlencode($v) . "&";
//    }
//
//    return $query;
//}
//

//$file = file('base.txt');
//foreach ($file as $str) {
//    $acc = @preg_replace("/[^0-9]/", '', $str);
//    if (InstBase::where(['account' => $acc])->find()->count == 0) {
//        InstBase::add(['account' => $acc]);
//        echo $acc;
//    }else {
//        echo "no";
//    }
//}
//$smiles=explode("\n", $smiles);
//foreach ($smiles as $key=>$smile){
//    ProfileGenerate::where(['id'=>$key])->update(['description' => $smile]);
//}
//
//
//$urls = 'sdsdfkljsdk45.tk
//sdsdfkljsasdgdk45.tk
//sdsdfkljsdasdgdk45.tk
//3347db1ff80eefe.tk
//d45000e8ab04a8451.tk
//923e0029d854.tk
//70ccba3dd56f545.tk
//0d98a8c7ff4002.tk
//944425609e8f.tk
//a28c2e90cc6f76d1c.tk
//448a754a99fa.tk
//81449394be2e0c617.tk
//bdc79415d4c5695.tk
//56119e674efb.tk
//7bdd39c324968.tk
//68a5a51ecbb6c.tk
//0a45c4440beb6f765.tk
//d1305d0b21fd79a4.tk
//87cce5d5dcf7c.tk
//117f9df7368224e6f.tk
//b17ca8154605283.tk
//1d52ad90b253e7.tk
//a5a085f7e1d3.tk
//25872e3e49e398dc.tk
//6b268d4b98e6e8fc2.tk
//f609717b7c7984783.tk
//025d904bffa1067.tk
//3d790de55fb9.tk
//8ac4e176d39b913.tk
//f7222627013eadbab.tk
//ca2c1ad745cc63b.tk
//c470db24818a61.tk
//409412c96b5a307.tk
//6a5b86898bd3f5.tk
//8eee62aa9a23.tk
//f50ac2e5588bd73e1.tk
//63d8ccb02031cbf75.tk
//5562c8105c10.tk
//2e8d52aeb2bbe61.tk
//2060c2b4de684fd.tk
//d8f9aeeb3206.tk';
//$urls = explode("\n", $urls);
//$i = 1;
//foreach ($urls as $key => $val) {
//    echo $val."\n";
//    print_r(ProfileGenerate::where(['id' => $i])->update(['url' => "www." . $val]));
//    $i++;
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