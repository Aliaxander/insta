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
        $dns = dns_get_record($domain);
        if (!empty($dns[0]['type']) && !empty($dns[0]['ip'])) {
            $result = $dns[0]['type'] . ' ' . $dns[0]['ip'];
        } else {
            $result = false;
        }


        return $result;
    }
}
