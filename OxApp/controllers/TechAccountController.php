<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 03.05.17
 * Time: 11:43
 */

namespace OxApp\controllers;

use Ox\App;
use Ox\View;

class TechAccountController extends App
{
     public function get()
     {
         View::build('techAccount');
     }
}