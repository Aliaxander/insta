<?php
/**
 * Created by OxGroup.
 * User: Aliaxander
 * Date: 26.03.16
 * Time: 13:53
 */

namespace OxApp\controllers;

use Ox\App;
use Ox\View;
use OxApp\models\Users;

/**
 * Class UsersController
 *
 * @package OxApp\controllers\api
 */
class UsersController extends App
{
    /**
     * GET method
     */
    public function get()
    {
        if (!empty($this->request->query->get("limit"))) {
            $limit = $this->request->query->get("limit");
        } else {
            $limit = 50;
        }
        if (!empty($this->request->query->get("page"))) {
            $page = $this->request->query->get("page");
        } else {
            $page = 1;
        }
        $startPage = $page * $limit - $limit;
        $paging = array($startPage => $limit);
        $total = Users::selectBy("count(id) as count")
            ->orderBy(["id" => "desc"])
            ->find();
        $users = Users::orderBy(["id" => "desc"])
            ->limit($paging)
            ->find()
            ->rows;
        return View::build('users', [
            'users' => $users,
            "setPage" => $page,
            "totalRows" => (int)@$total->rows[0]->count,
            "totalPages" => ceil(@$total->rows[0]->count / $limit),
        ]);
    }
}
