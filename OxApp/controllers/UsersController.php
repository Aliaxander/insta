<?php
/**
 * Created by OxGroup.
 * User: Aliaxander
 * Date: 26.03.16
 * Time: 13:53
 */

namespace OxApp\controllers;

use Ox\App;
use Ox\View;
use OxApp\models\TaskType;
use OxApp\models\UserGroup;
use OxApp\models\Users;

/**
 * Class UsersController
 *
 * @package OxApp\controllers\api
 */
class UsersController extends App
{
    /**
     * GET method
     */
    public function get()
    {
        $taskType = TaskType::find()->rows;
        $group = UserGroup::find()->rows;
        return View::build('users', [
            'taskTypes' => $taskType,
            'groups' => $group
        ]);
    }
    
    public function post()
    {
        $id = explode(',', $this->request->request->get('id'));
        Users::where([
            'id/in' => $id
        ])->update([
            'userGroup' => $this->request->request->get('userGroup')
        ]);
        
        return $this->get();
    }
}
