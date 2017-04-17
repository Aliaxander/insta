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
        $rule = [
            "LogIn",
            "ban"
        ];
        $where = [];
        foreach ($rule as $item) {
            if (!empty($this->request->query->get($item)) || $this->request->query->get($item) == 0) {
                $where[$item] = $this->request->query->get($item);
            }
        }

        if (!empty($this->request->query->get('orderBy')) && !empty($this->request->query->get('sort'))) {
            $orderBy = [$this->request->query->get('orderBy') => $this->request->query->get('sort')];
        } else {
            $orderBy = ["id" => "desc"];
        }
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
            ->where($where)
            ->orderBy(["id" => "desc"])
            ->find();
        $users = Users::orderBy($orderBy)
            ->where($where)
            ->limit($paging)
            ->find()
            ->rows;
        if($this->request->query->get('detail') == 'all') {
            $template = 'userDetail';
        } else {
            $template = 'users';
        }
  
        $usersSum = @Users::selectBy(['sum(likes) as likes'])->find()->rows[0]->likes;
        return View::build($template, [
            'users' => $users,
            "setPage" => $page,
            'sumLikes' => $usersSum,
            "totalRows" => (int)@$total->rows[0]->count,
            "totalPages" => ceil(@$total->rows[0]->count / $limit),
        ]);
    }
}
