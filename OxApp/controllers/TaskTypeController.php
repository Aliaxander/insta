<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 18.04.17
 * Time: 10:21
 */

namespace OxApp\controllers;

use Ox\App;
use OxApp\models\TaskType;

/**
 * Class TaskType
 * @package OxApp\controllers
 */
class TaskTypeController extends App
{
    /**
     * GET method
     */
    public function get()
    {
        TaskType::find();

    }

    /**
     * POST method
     */
    public function post()
    {
        //
    }
}
