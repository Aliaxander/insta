<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 19.04.17
 * Time: 11:52
 */

namespace OxApp\controllers\api;

use Ox\App;
use OxApp\models\BanStatus;
use OxApp\models\TaskType;
use OxApp\models\UserGroup;
use OxApp\models\Users;

class UsersController extends App
{
    public function get()
    {
        $rule = [
            'logIn',
            'ban',
            'userGroup',
            'userTask'
        ];
        $where = [];
        if (!empty($this->request->query->get('filter'))) {
            $filters = json_decode($this->request->query->get('filter'));
            foreach ($filters as $key => $item) {
                if (in_array($key, $rule) && $item != '') {
                    $where[$key] = $item;
                }
            }
        }
        if (!empty($this->request->query->get('order')) && !empty($this->request->query->get('sort'))) {
            $orderBy = [$this->request->query->get('sort') => $this->request->query->get('order')];
        } else {
            $orderBy = ['id' => 'desc'];
        }
        if (!empty($this->request->query->get('limit'))) {
            $limit = $this->request->query->get('limit');
        } else {
            $limit = 50;
        }
        if (!empty($this->request->query->get("offset"))) {
            $offset = $this->request->query->get("offset");
        } else {
            $offset = 0;
        }
        $paging = array($offset => $limit);
        $total = Users::selectBy("count(id) as count")
            ->where($where)
            ->orderBy(["id" => "desc"])
            ->find();
        $users = Users::orderBy($orderBy)
            ->where($where)
            ->limit($paging)
            ->find()
            ->rows;
        $banStatus = BanStatus::find()->rows;
        foreach ($banStatus as $item) {
            $bans[$item->id] = $item->name;
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
            $users[$key]->ban = $bans[$user->ban];
        }

        return json_encode([
            'total' => (int)@$total->rows[0]->count,
            'rows' => $users,
        ]);
    }
}
