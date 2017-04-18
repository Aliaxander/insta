<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 18.04.17
 * Time: 0:22
 */

namespace OxApp\controllers;

use Ox\App;
use Ox\View;
use OxApp\models\Task;

/**
 * Class TaskController
 * @package OxApp\controllers
 */
class TaskController extends App
{
    /**
     * GET method
     */
    public function get()
    {
        return View::build("task");
    }

    /**
     * POST method
     */
    public function post()
    {
        $id = explode(',', $this->request->request->get('id'));
        foreach ($id as $item) {
            if (Task::find(['userId' => $item])->count > 0) {
                Task::update([
                    'taskTypeId' => $this->request->request->get('taskTypeId'),
                    'status' => 0
                ],[
                    'userId' => $item
                ]);
            } else {
                Task::add([
                    'taskTypeId' => $this->request->request->get('taskTypeId'),
                    'userId' => $item,
                    'status' => 0
                ]);
            }
        }

        header("Location: /users");
    }
}
