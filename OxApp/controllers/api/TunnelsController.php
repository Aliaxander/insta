<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 15.05.17
 * Time: 10:24
 */

namespace OxApp\controllers\api;

use Ox\App;
use OxApp\helpers\TunnelBroker;
use OxApp\models\Proxy;
use OxApp\models\TechAccount;
use OxApp\models\Tunnels;
use OxApp\models\Users;

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
        $tunels = Tunnels::orderBy($orderBy)
            ->where($where)
            ->limit($paging)
            ->find()
            ->rows;
        
        foreach ($tunels as $key => $item) {
            $tunels[$key]->accountcount = Users::selectBy(['count(id) as count'])
                ->find([
                    'proxy/like' => $item->serverIp . '%',
                    'ban' => 0,
                    'userGroup/not in' => [2],
                    'userTask/!=' => 7
                ])
                ->rows[0]->count;
        }
        
        return json_encode([
            'total' => (int)@$total->rows[0]->count,
            'rows' => $tunels,
        ]);
    }
    
    /**
     * @return string
     */
    public function put()
    {
        $id = $this->request->request->get('id');
        $tunnel = Tunnels::find(['id' => $id]);
        if ($tunnel->count > 0) {
            $tunnel = $tunnel->rows[0];
            Users::where([
                'proxy/like' => $tunnel->serverIp . ':%',
                'userGroup' => 1,
                'ban' => 0
            ])->update(['userGroup' => 18]);
            Proxy::delete(['proxy/like' => $tunnel->serverIp . ':%']);
            $tunels = Tunnels::where(['id' => $id])->update(['status' => 0]);
        }
        
        if (!empty($tunels->count) && $tunels->count === 1) {
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
            $domains = Tunnels::delete(['id/in' => $id]);
            if (count($domains) > 0) {
                $result = ['status' => 200];
            }
        }
        
        return json_encode($result);
    }
}