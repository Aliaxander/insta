<?php
/**
 * Created by PhpStorm.
 * User: irina
 * Date: 19.04.17
 * Time: 23:16
 */

namespace OxApp\controllers;


use Ox\AbstractClass;
use OxApp\models\Users;

/**
 * Class ResetRequestsController
 *
 * @package OxApp\controllers
 */
class ResetRequestsController extends AbstractClass
{
    public function get()
    {
        $this->post();
    }
    
    public function post()
    {
        $id = explode(',', $this->request->get("id"));
        if (!empty($id)) {
            Users::where(['id/in' => $id])->update(['requests'=>0]);
        }
        
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
    
}