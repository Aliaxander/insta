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
    protected $alerts = [];

    /**
     * GET method
     */
    public function get()
    {
        $userGroups = UserGroup::find()->rows;
        return View::build('userGroup', ['userGroups' => $userGroups, 'alerts' => $this->alerts]);
    }

    /**
     * POST method
     */
    public function post()
    {
        $name = trim($this->request->request->get('name'));
        if(!empty($name)) {
            UserGroup::add(['name' => $name]);
        }
        $this->alerts = ['success' => 'userGroup add'];

        return $this->get();
    }
}
