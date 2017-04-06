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
use OxApp\models\Proxy;

class ProxyController extends App
{
    public function get()
    {
        $proxy = Proxy::find();
        return View::build('proxy',['proxyes' => $proxy->rows]);
    }
}