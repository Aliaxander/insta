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
use OxApp\models\Task;
use OxApp\models\TaskType;
use OxApp\models\UserGroup;
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
            'LogIn',
            'ban',
            'userGroup',
            'userTask'
        ];
        $where = [];
        foreach ($rule as $item) {
            if (!empty($this->request->query->get($item))) {
                $where[$item] = $this->request->query->get($item);
            }
        }

        if (!empty($this->request->query->get('orderBy')) && !empty($this->request->query->get('sort'))) {
            $orderBy = [$this->request->query->get('orderBy') => $this->request->query->get('sort')];
        } else {
            $orderBy = ['id' => 'desc'];
        }
        if (!empty($this->request->query->get('limit'))) {
            $limit = $this->request->query->get('limit');
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
        if ($this->request->query->get('detail') == 'all') {
            $template = 'userDetail';
        } else {
            $template = 'users';
        }
        $taskType = TaskType::find()->rows;
        foreach ($taskType as $item) {
            $taskTypes[$item->id] = $item->name;
        }
        $group = UserGroup::find()->rows;
        foreach ($group as $item) {
            $groups[$item->id] = $item->name;
        }
        foreach ($users as $key => $user) {
            $users[$key]->userGroup = $groups[$user->userGroup];
            $users[$key]->userTask = $taskTypes[$user->userTask];
        }
        $usersSum = @Users::selectBy(['sum(likes) as likes'])->find()->rows[0]->likes;
        if ($_SERVER['REQUEST_URI'] == "/users") {
            $url = $_SERVER['REQUEST_URI'] . "?";
        } else {
            $url = $_SERVER['REQUEST_URI'] . "&";
        }
        return View::build($template, [
            'url' => $url,
            'taskTypes' => $taskType,
            'groups' => $group,
            'users' => $users,
            'setPage' => $page,
            'sumLikes' => $usersSum,
            'totalRows' => (int)@$total->rows[0]->count,
            'totalPages' => ceil(@$total->rows[0]->count / $limit),
        ]);
    }

    public function post()
    {
        $id = explode(',', $this->request->request->get('id'));
        Users::update([
            'userGroup' => $this->request->request->get('userGroup')
        ], [
            'id/in' => $id
        ]);

        return $this->get();
    }
}
