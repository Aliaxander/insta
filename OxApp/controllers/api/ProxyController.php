<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 03.05.17
 * Time: 16:16
 */

namespace OxApp\controllers\api;

use Ox\App;
use OxApp\models\Proxy;

class ProxyController extends App
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
        $total = Proxy::selectBy("count(id) as count")
            ->where($where)
            ->orderBy(["id" => "desc"])
            ->find();
        $proxy = Proxy::orderBy($orderBy)
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
        $proxy = [];
        $ips = explode("\n", $this->request->request->get('ip'));

        if (!empty($ips)
            && empty($this->request->request->get('portOut'))
            && empty($this->request->request->get('portIn'))
            && empty($this->request->request->get('authData'))
        ) {
            foreach ($ips as $ip) {
                if (!empty($ip)) {
                    $proxy[] = Proxy::data(['proxy' => trim($ip), 'rand' => mt_rand(0, 10000)])
                        ->add();
                }
            }
        } else {
            foreach ($ips as $ip) {
                $ip = str_replace("\n", "", $ip);
                $ip = str_replace("\r", "", $ip);
                $ip = str_replace(" ", "", $ip);
                if (!empty($ip)) {
                    for ($i = $this->request->request->get('portIn'); $i < $this->request->request->get('portOut'); $i++) {
                        $proxy[] = Proxy::add([
                            'proxy' => $ip . ":" . $i . ";" . $this->request->request->get('authData'),
                            'rand' => mt_rand(0, 10000)
                        ]);
                    }
                }
            }
        }
        if (count($proxy) > 0) {
            $result = ['status' => 200];
        } else {
            $result = ['status' => 500];
        }

        return json_encode($result);
    }

    /**
     * @return string
     */
    public function put()
    {

        $proxy = Proxy::update(
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

    /**
     * @return string
     */
    function delete()
    {
        $result = ['status' => 500];
        $id = $this->request->request->get('id');
        if (!empty($id)) {
            $domains = Proxy::delete(['id/in' => $id]);
            if (count($domains) > 0) {
                $result = ['status' => 200];
            }
        }

        return json_encode($result);
    }
}