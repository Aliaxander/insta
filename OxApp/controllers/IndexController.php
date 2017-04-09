<?php
/**
 * Created by PhpStorm.
 * User: kinkytail
 * Date: 05.04.17
 * Time: 15:59
 */

namespace OxApp\controllers;

use Ox\App;
use Ox\View;
use OxApp\models\ProfileGenerate;
use OxApp\models\Proxy;
use OxApp\models\Users;

class IndexController extends App
{
    public function get()
    {
        $allUsers = Users::find();
        $banUsers = Users::find(["ban" => 1]);
        $proxy = Proxy::find();
        $description = ProfileGenerate::find();
        return View::build("index", [
            "allUsers" => $allUsers->count,
            "banUsers" => $banUsers->count,
            "proxy" => $proxy->count,
            "description" => $description->count,
        ]);
    }
}
