<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 18.04.17
 * Time: 11:24
 */

namespace OxApp\controllers;

use Ox\App;
use Ox\View;
use OxApp\models\UserGroup;

/**
 * Class UserGroupController
 * @package OxApp\controllers
 */
class UserGroupController extends App
{
    /**
     * GET method
     */
    public function get()
    {
        $userGroups = UserGroup::find()->rows;
        return View::build('userGroup', ['userGroups' => $userGroups]);

    }

    /**
     * POST method
     */
    public function post()
    {


    }
}
