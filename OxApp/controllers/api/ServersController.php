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
        $proxy = Servers::orderBy($orderBy)
            ->where($where)
            ->limit($paging)
            ->find()
            ->rows;

        return json_encode([
            'total' => (int)@$total->rows[0]->count,
            'rows' => $proxy,
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
}