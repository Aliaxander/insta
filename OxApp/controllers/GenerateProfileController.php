<?php
/**
 * Created by PhpStorm.
 * User: kinkytail
 * Date: 05.04.17
 * Time: 12:17
 */

namespace OxApp\controllers;

use Ox\App;
use Ox\View;
use OxApp\helpers\TextTemplateGenerator;
use OxApp\models\ProfileGenerate;

class GenerateProfileController extends App
{
    public function get()
    {
        return View::build("generateProfile");
    }

    public function post()
    {
        $biography = $this->request->request->get('biography');
        $domain = $this->request->request->get('domain');
        $macros = $this->request->request->get('macros');
        $count = $this->request->request->get('count');
        $generator = new TextTemplateGenerator($biography);
        $generator1 = new TextTemplateGenerator($macros);
        $biography = $generator->generate($count);
        $macros = $generator1->generate($count);
        foreach ($biography as $key => $item) {
            $url = 'http://' . $domain . '/' . $macros[$key];
            ProfileGenerate::add(['`description`' => $item, '`url`' => $url, '`status`' => 1]);
        }

        header("Location: /showProfile");
    }
}