<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 15.05.17
 * Time: 10:26
 */

namespace OxApp\controllers\api;


use Ox\App;
use OxApp\models\Servers;
use OxApp\models\Users;

class ServersController extends App
{
    /**
     * @return string
     */
    public function get()
    {
        $where = [];
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

        if (!empty($this->request->query->get("search"))) {
            $where['proxy/like'] = '%' . $this->request->query->get("search") . '%';
        }
        $total = Servers::selectBy("count(id) as count")
            ->where($where)
            ->orderBy(["id" => "desc"])
            ->find();
        $server = Servers::orderBy($orderBy)
            ->where($where)
            ->limit($paging)
            ->find()
            ->rows;

        foreach ($server as $key => $item) {
            $server[$key]->accountcount = Users::selectBy(['count(id) as count'])
                ->find(['proxy/like' => $item->ip . '%', 'ban' => 0])
                ->rows[0]->count;
        }

        return json_encode([
            'total' => (int)@$total->rows[0]->count,
            'rows' => $server,
        ]);
    }

    /**
     * POST method
     */
    public function post()
    {
        $server = [];
        $ip = trim($this->request->request->get('ip'));
        $password = trim($this->request->request->get('password'));
        if (!empty($ip) && !empty($password)) {
            $server[] = Servers::add([
                'ip' => $ip,
                'password' => $password
            ]);
        }
        if (count($server) > 0) {
            $result = ['status' => 200];
        } else {
            $result = ['status' => 500];
        }

        return json_encode($result);
    }

    /**
     * @return string
     */
    function delete()
    {
        $result = ['status' => 500];
        $id = $this->request->request->get('id');
        if (!empty($id)) {
            $domains = Servers::delete(['id/in' => $id]);
            if (count($domains) > 0) {
                $result = ['status' => 200];
            }
        }

        return json_encode($result);
    }
}