<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 25.04.17
 * Time: 12:59
 */

namespace OxApp\controllers;

use Ox\App;
use Ox\View;

/**
 * Class SystemSettingsController
 * @package OxApp\controllers
 */
class SystemSettingsController extends App
{
    /**
     *
     */
    public function get()
    {
        View::build('systemSettings');
    }
}
