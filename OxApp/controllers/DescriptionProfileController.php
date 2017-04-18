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
        return View::build("descriptionProfile", ['profiles' => $profile, 'alerts' => $this->alerts]);
    }

    public function post()
    {
        $biography = $this->request->request->get('biography');
        $count = $this->request->request->get('count');
        $generator = new TextTemplateGenerator($biography);
        $biography = array_unique($generator->generate($count));


        foreach ($biography as $key => $item) {
            $data= ['description' => $item, 'status' => 0];
            if(!empty($item)) {
                ProfileGenerate::add($data);
            }
        }
        $this->alerts = ['success' => 'Pforile generate'];

        return $this->get();
    }
}