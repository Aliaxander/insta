<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 18.04.17
 * Time: 10:21
 */

namespace OxApp\controllers;

use Ox\App;
use Ox\View;
use OxApp\models\TaskType;
use OxApp\models\Users;

/**
 * Class TaskType
 * @package OxApp\controllers
 */
class TaskTypeController extends App
{
    protected $alerts = [];

    /**
     * GET method
     */
    public function get()
    {
        $taskTypes = TaskType::find()->rows;
        foreach ($taskTypes as $key => $item) {
            $users = @Users::selectBy(['count(id) as count', 'sum(likes) as sumLikes'])
                ->find(['userTask' => $item->id])->rows[0];
            $taskTypes[$key]->users = $users->count;
            $taskTypes[$key]->sumLikes = $users->sumLikes;
        }

        return View::build("taskType", ['taskTypes' => $taskTypes, 'alerts' => $this->alerts]);

    }

    /**
     * POST method
     */
    public function post()
    {
        $name = trim($this->request->request->get('name'));
        if (!empty($name)) {
            TaskType::add(['name' => $name]);
        }
        $this->alerts = ['success' => 'TaskType add'];

        return $this->get();
    }
}
