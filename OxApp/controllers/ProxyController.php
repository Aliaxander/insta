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
use OxApp\models\Users;

class ProxyController extends App
{
    public function get()
    {
        $proxy = Users::find();
        return View::build("proxy", $proxy->rows);
    }
}