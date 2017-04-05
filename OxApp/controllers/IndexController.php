<?php
/**
 * Created by PhpStorm.
 * User: kinkytail
 * Date: 05.04.17
 * Time: 15:59
 */

namespace OxApp\controllers;


class IndexController
{
    public function get()
    {
        return View::build("index");
    }
}