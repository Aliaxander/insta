<?php
/**
 * Created by PhpStorm.
 * User: irina
 * Date: 19.04.17
 * Time: 23:16
 */

namespace OxApp\controllers;


use Ox\AbstractClass;
use Ox\App;
use OxApp\models\Users;

/**
 * Class ResetRequestsController
 *
 * @package OxApp\controllers
 */
class ResetUsersController extends App
{
    public function get()
    {
        $this->post();
    }
    
    public function post()
    {
        $id = explode(',', $this->request->request->get("id"));
        $resetType = $this->request->request->get("resetType");
        if (!empty($id)) {
            Users::where(['id/in' => $id])->update([$resetType => 0]);
            if ($resetType === 'ban') {
                Users::where(['id/in' => $id])->update(['requests' => 0]);
            }
        }
        
        
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
    
}