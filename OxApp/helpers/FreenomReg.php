<?php
/**
 * Created by PhpStorm.
 * User: irina
 * Date: 08.04.17
 * Time: 1:48
 */

namespace OxApp\helpers;


class FreenomReg
{
    /**
     * @param $domain
     *
     * @return array
     */
    public static function freedomReg(
        $domain
    ) {
        $data = "forward_url={$domain}&email=maste.craft@gmail.com&password=047b014138";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.freenom.com/v2/domain/register.xml");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        if ($data) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $resp = curl_exec($ch);
        $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($resp, 0, $header_len);
        $body = substr($resp, $header_len);
        curl_close($ch);
        
        return [$header, $body];
    }
    
    
}