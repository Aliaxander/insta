<?php
/**
 * Created by PhpStorm.
 * User: kinkytail
 * Date: 06.04.17
 * Time: 11:58
 */

namespace OxApp\controllers;


use Ox\App;
use Ox\View;
use OxApp\helpers\TextTemplateGenerator;

class TestMacrosController extends App
{
    public function get()
    {
        $data = [];
        if(!empty($this->request->query->get('macros'))) {
            $macros = $this->request->query->get('macros');
            $generator = new TextTemplateGenerator($macros);
            $data = $generator->generate(20);
        }
        return View::build("testMacros", ['data' => $data]);
    }
}