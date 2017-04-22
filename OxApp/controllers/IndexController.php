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
use OxApp\models\Domains;
use OxApp\models\InstBase;
use OxApp\models\ProfileGenerate;
use OxApp\models\Proxy;
use OxApp\models\Users;

class IndexController extends App
{
    public function get()
    {
        $allUsers = Users::selectBy(['count(id) as count'])->find(['ban' => 0])->rows[0]->count;
        $banUsers = Users::selectBy(['count(id) as count'])->find(["ban" => 1])->rows[0]->count;
        $proxy = Proxy::selectBy(['count(id) as count'])->find(['status' => 1])->rows[0]->count;
        $description = ProfileGenerate::selectBy(['count(id) as count'])->find(['status' => 1])->rows[0]->count;
        $instBase = instBase::selectBy(['count(id) as count'])->find(['status' => 1])->rows[0]->count;
        $domains = Domains::selectBy(['count(id) as count'])->find(['status' => 1])->rows[0]->count;
        
        View::build("index", [
            'data' =>
                [
                    "instBase" => $instBase,
                    "domains" => $domains,
                    "allUsers" => $allUsers,
                    "banUsers" => $banUsers,
                    "proxy" => $proxy,
                    "description" => $description,
                ]
        ]);
    }
}
