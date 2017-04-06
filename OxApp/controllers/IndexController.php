<?php
/**
 * Created by PhpStorm.
 * User: kinkytail
 * Date: 05.04.17
 * Time: 15:59
 */

namespace OxApp\controllers;


use Ox\App;
use Ox\View;

class IndexController extends App
{
    public function get()
    {
        return View::build("index");
    }
}
