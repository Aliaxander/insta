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
use OxApp\models\ProfileGenerate;

class ShowProfileController extends App
{
    public function get() {
        $profiles = ProfileGenerate::find();
        $profile = $profiles->rows;
        return View::build("showProfile", ['profiles' => $profile]);
    }
}