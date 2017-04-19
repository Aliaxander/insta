<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 19.04.17
 * Time: 11:52
 */

namespace OxApp\controllers\api;

use Ox\App;
use OxApp\models\TaskType;
use OxApp\models\UserGroup;
use OxApp\models\Users;

class UsersController extends App
{
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
        if (!empty($this->request->query->get("offset"))) {
            $page = $this->request->query->get("offset");
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
        return json_encode([
            'total' => (int)@$total->rows[0]->count,
            'rows' => $users,
            'page' => $page,
        ]);
    }
}