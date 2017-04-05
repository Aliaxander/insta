<?php
/**
 * Created by PhpStorm.
 * User: kinkytail
 * Date: 05.04.17
 * Time: 15:15
 */

namespace OxApp\controllers;


use Ox\App;
use OxApp\models\ProfileGenerate;

class DeleteProfileController extends App
{
    public function get()
    {
        $selectId = $this->request->query->get("id");
        ProfileGenerate::delete(['id' => $selectId]);
        header("Location: /showProfile");
    }
}