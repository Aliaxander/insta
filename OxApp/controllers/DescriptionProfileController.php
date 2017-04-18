<?php
/**
 * Created by PhpStorm.
 * User: kinkytail
 * Date: 05.04.17
 * Time: 14:30
 */

namespace OxApp\controllers;

use Ox\App;
use Ox\View;
use OxApp\helpers\TextTemplateGenerator;
use OxApp\models\ProfileGenerate;

/**
 * Class ShowProfileController
 * @package OxApp\controllers
 */
class DescriptionProfileController extends App
{
    protected $alerts = [];

    public function get() {
        $profiles = ProfileGenerate::find();
        $profile = $profiles->rows;
        return View::build("showProfile", ['profiles' => $profile, 'alerts' => $this->alerts]);
    }

    public function post()
    {
        $biography = $this->request->request->get('biography');
        $domain = $this->request->request->get('domain');
        $count = $this->request->request->get('count');

        $generator = new TextTemplateGenerator($biography);
        $generator1 = new TextTemplateGenerator($domain);
        $biography = array_unique($generator->generate($count));
        $domains = array_unique($generator1->generate($count));

        $counts = count($domains)-1;
        foreach ($biography as $key => $item) {
            $url = @$domains[mt_rand(0, $counts)];
            $data= ['description' => $item, 'url' => $url, 'status' => 0];
            if(!empty($item) && !empty($url)) {
                ProfileGenerate::add($data);
            }
        }
        $this->alerts = ['success' => 'Pforile generate'];

        return $this->get();
    }
}