<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 09.04.17
 * Time: 21:25
 */

namespace OxApp\controllers;

use Ox\App;
use OxApp\models\Users;

/**
 * Class DeleteUsersController
 *
 * @package OxApp\controllers
 */
class DeleteUsersController extends App
{
    public function get()
    {
        $this->post();
    }
    
    public function post()
    {
        $id = explode(',', $this->request->get("id"));
        if (!empty($id)) {
            Users::delete(['id/in' => $id]);
        }
        
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
}
