<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 15.05.17
 * Time: 10:24
 */

namespace OxApp\controllers\api;

use Ox\App;
use OxApp\models\Tunnels;

class TunnelsController extends App
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
        $total = Tunnels::selectBy("count(id) as count")
            ->where($where)
            ->orderBy(["id" => "desc"])
            ->find();
        $proxy = Tunnels::orderBy($orderBy)
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
     * @return string
     */
    public function put()
    {

        $proxy = Tunnels::update(
            ['status' => $this->request->request->get('status')],
            ['id' => $this->request->request->get('id')]
        );
        if ($proxy->count === 1) {
            $result = ['status' => 200];
        } else {
            $result = ['status' => 500];
        }


        return json_encode($result);
    }
}