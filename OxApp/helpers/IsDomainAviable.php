<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 04.05.17
 * Time: 10:55
 */

namespace OxApp\helpers;

/**
 * Class IsDomainAviable
 * @package OxApp\helpers
 */
class IsDomainAviable
{
    public static function isAviable($domain)
    {
//        if (!filter_var($domain, FILTER_VALIDATE_URL)) {
//            return false;
//        }

        $curlInit = curl_init($domain);
        curl_setopt($curlInit, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curlInit, CURLOPT_HEADER, true);
        curl_setopt($curlInit, CURLOPT_NOBODY, true);
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlInit, CURLOPT_FOLLOWLOCATION, false);

        curl_exec($curlInit);
        if (!curl_errno($curlInit)) {
            $http_code = curl_getinfo($curlInit, CURLINFO_PRIMARY_IP);
            return $http_code;
        }
        curl_close($curlInit);


        return false;
    }
}
