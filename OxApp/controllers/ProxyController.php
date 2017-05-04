<?php
/**
 * Created by PhpStorm.
 * User: kinkytail
 * Date: 06.04.17
 * Time: 11:12
 */

namespace OxApp\controllers;

use Ox\App;
use Ox\View;

class ProxyController extends App
{
    /**
     * Get method
     */
    public function get()
    {

        return View::build('proxy');
    }
}
