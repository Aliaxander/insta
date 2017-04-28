<?php
/**
 * Created by PhpStorm.
 * User: irina
 * Date: 28.04.17
 * Time: 23:03
 */

namespace OxApp\controllers;


use Ox\App;
use Ox\View;
use OxApp\models\FreenomSessions;

/**
 * Class AutoClickController
 *
 * @package OxApp\controllers
 */
class AutoClickController extends App
{
    public function get()
    {
        $clickUrls = FreenomSessions::limit([0 => 1])->find(['iobb' => '']);
        
        View::build('autoclick', ['sess' => @$clickUrls->rows[0]->sessid]);
    }
    
}