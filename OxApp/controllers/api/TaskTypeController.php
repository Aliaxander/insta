<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 19.04.17
 * Time: 20:15
 */

namespace OxApp\controllers\api;

use Ox\App;
use OxApp\models\TaskType;

class TaskTypeController extends App
{
    public function get()
    {
        $taskType = [];
        $taskTypes = TaskType::find()->rows;
        foreach ($taskTypes as  $item) {
            $taskType[$item->id] = $item->name;
        }

        return json_encode($taskType);
    }
}
