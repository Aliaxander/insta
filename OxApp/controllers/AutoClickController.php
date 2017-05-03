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
        $sessid = @$clickUrls->rows[0]->sessid;
        $data = '';
        if (!empty($sessid)) {
            $data = file_get_contents('http://insta.oxgroup.media/public/' . $sessid . '.html');
        }
        View::build('autoclick', ['sess' => $sessid, 'data' => $data]);
    }
    
}