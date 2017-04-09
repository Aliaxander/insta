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

class DeleteUsersController extends App
{
    public function get()
    {
        $selectId = $this->request->query->get("id");
        $id = explode(",", $selectId);
        if (!empty($id)) {
            foreach ($id as $userId) {
                Users::delete(['id' => $userId]);
            }
        }

        header("Location: /users");
    }
}
