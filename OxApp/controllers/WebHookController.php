<?php
/**
 * Created by PhpStorm.
 * User: irina
 * Date: 25.04.17
 * Time: 8:53
 */

namespace OxApp\controllers;


use Ox\App;
use OxApp\models\FreenomSessions;

/**
 * Class WebHookController
 *
 * @package OxApp\controllers
 */
class WebHookController extends App
{
    public function get()
    {
        $this->post();
    }
    
    public function post()
    {
        if (!empty($this->request->get('session'))) {
            FreenomSessions::where(['sessid' => $this->request->get('session')])->update([
                'iobb' => $this->request->get('iobb'),
                'fpbb' => $this->request->get('fpbb')
            ]);
        }
    }
    
}