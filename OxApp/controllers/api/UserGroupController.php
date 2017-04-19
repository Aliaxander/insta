<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 19.04.17
 * Time: 20:10
 */

namespace OxApp\controllers\api;

use Ox\App;
use OxApp\models\UserGroup;

class UserGroupController extends App
{
    public function get()
    {
        $userGroup = [];
        $userGroups = UserGroup::find()->rows;
        foreach ($userGroups as $item) {
            $userGroup[$item->id] =  $item->name;
        }

        return json_encode($userGroup);
    }
}
