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

class AddProxyController extends App
{
    public function get()
    {
        return View::build('addProxy');
    }

    public function post()
    {
        $proxy = trim($this->request->request->get('proxy'));
        $proxylist = explode("\r\n", $proxy);
        foreach ($proxylist as $item) {
            Proxy::add(['proxy' => $item, 'status' => 0]);
        }
        $this->get();
    }
}