<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 03.05.17
 * Time: 13:46
 */

namespace OxApp\controllers;

use Ox\App;
use Ox\View;

class DomainsController extends App
{
    public function get()
    {
        View::build('domains');
    }
}