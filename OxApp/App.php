<?php

/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 18.05.15
 * Time: 14:41
 */

/**
 * Class App
 */
namespace Ox;

use OxApp\helpers\DefaultRequest;

/**
 * Class App
 *
 * @package Ox
 */
class App
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    public $request;
    
    /**
     * @var array
     */
    public static $user = array();
    
    /**
     * @var array
     */
    public static $userGroup = array();

    /**
     * App constructor.
     */
    public function __construct()
    {
        $this->request = DefaultRequest::getRequest();
    }
}
