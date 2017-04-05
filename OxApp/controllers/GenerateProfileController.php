<?php
/**
 * Created by PhpStorm.
 * User: kinkytail
 * Date: 05.04.17
 * Time: 12:17
 */

namespace OxApp\controllers;


use Ox\App;
use Ox\View;

class GenerateProfileController extends App
{
    public function get()
    {
        return View::build("generateProfile");
    }

    public function post()
    {

    }
}