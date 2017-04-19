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

/**
 * Class UsersController
 * @package OxApp\controllers\html
 */
class UsersController extends App
{
    public function get()
    {
        return View::build('usersTest');
    }
}
