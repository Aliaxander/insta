<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 06.04.17
 * Time: 20:59
 */

namespace OxApp\controllers;


use Ox\App;
use Ox\View;
use OxApp\models\Proxy;

/**
 * Class AddProxyController
 *
 * @package OxApp\controllers
 */
class AddProxyController extends App
{
    public function get()
    {
        return View::build('addProxy');
    }
    
    public function post()
    {
        
        for ($i = $this->request->request->get('portIn'); $i < $this->request->request->get('portOut'); $i++) {
            Proxy::add([
                'proxy' => $this->request->request->get('ip') . $i . ";" . $this->request->request->get('authData'),
            ]);
        }
        $this->get();
    }
}