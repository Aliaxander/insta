<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 15.05.17
 * Time: 10:29
 */

namespace OxApp\controllers;

use Ox\App;
use Ox\View;

class ServersController extends App
{
    public function get()
    {
        return View::build("servers");
    }
}
