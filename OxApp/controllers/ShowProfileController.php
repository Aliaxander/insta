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
        $profiles = ProfileGenerate::orderBy(['id'=> 'desc'])->find();
        return View::build("showProfile", ['profiles' => $profiles->rows]);
    }
}