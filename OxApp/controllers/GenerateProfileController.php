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
        $count = $this->request->request->get('count');
        $generator = new TextTemplateGenerator($biography);
        $generator1 = new TextTemplateGenerator($domain);
        $biography = $generator->generate($count);
        $domain = $generator1->generate($count);
        $counts = count($domain);
        foreach ($biography as $key => $item) {
            $rand = mt_rand(0, $counts - 1);
            $url = $domain[$rand];
            ProfileGenerate::add(['description' => $item, 'url' => $url, 'status' => 1]);
        }

        header("Location: /showProfile");
    }
}