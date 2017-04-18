<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 18.04.17
 * Time: 16:04
 */

namespace OxApp\controllers;


use Ox\App;
use Ox\View;

class InstBaseController extends App
{
    protected $alerts = [];

    public function get()
    {
        return View::build('instBase', ['alerts' => $this->alerts]);
    }

    public function post()
    {
        $this->alerts = ['success' => 'Add'];

        return $this->get();
    }
}
