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
        $result = false;
        foreach ($dns as $item) {
            if (!empty($item['type']) && !empty($item['ip'])) {
                $result = $item['type'] . ' ' . $item['ip'];
            }
        }

        return $result;
    }
}
