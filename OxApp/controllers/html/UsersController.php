<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 19.04.17
 * Time: 13:22
 */

namespace OxApp\controllers\html;

use Ox\App;
use Ox\View;
use OxApp\models\TaskType;
use OxApp\models\UserGroup;

/**
 * Class UsersController
 * @package OxApp\controllers\html
 */
class UsersController extends App
{
    public function get()
    {
        $taskType = TaskType::find()->rows;
        $group = UserGroup::find()->rows;
        return View::build('usersTest', [
            'taskTypes' => $taskType,
            'groups' => $group
        ]);
    }
}
