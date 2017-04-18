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
        return View::build("taskType", ['taskTypes' => $taskTypes, 'alerts' => $this->alerts]);

    }

    /**
     * POST method
     */
    public function post()
    {
        $name = trim($this->request->request->get('name'));
        if(!empty($name)) {
            TaskType::add(['name' => $name]);
        }
        $this->alerts = ['success' => 'TaskType add'];

         return $this->get();
    }
}
