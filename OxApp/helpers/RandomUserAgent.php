<?php
/**
 * Created by PhpStorm.
 * User: irina
 * Date: 23.05.17
 * Time: 19:23
 */

namespace OxApp\helpers;


class RandomUserAgent
{
    /**
     * Main public function which will choose random browser
     *
     * @return string user agent
     */
    public function random_uagent()
    {
        return \Campo\UserAgent::random();
    }
    
}