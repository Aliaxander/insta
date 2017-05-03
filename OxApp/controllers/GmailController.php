<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 03.05.17
 * Time: 11:43
 */

namespace OxApp\controllers;

use Ox\View;

class GmailController
{
     public function get()
     {
         View::build('gmail');
     }
}