<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 03.05.17
 * Time: 11:39
 */

namespace OxApp\controllers\api;

use Ox\App;
use OxApp\models\Domains;

class DomainsController extends App
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
            $where['domain/like'] = '%' . $this->request->query->get("search") . '%';
        }
        $total = Domains::selectBy("count(id) as count")
            ->where($where)
            ->orderBy(["id" => "desc"])
            ->find();
        $domains = Domains::orderBy($orderBy)
            ->where($where)
            ->limit($paging)
            ->find()
            ->rows;

        return json_encode([
            'total' => (int)@$total->rows[0]->count,
            'rows' => $domains,
        ]);
    }

    /**
     * @return string
     */
    public function post()
    {
        $domains = $this->request->request->get('domains');

        if (!empty($domains)) {
            $domains = explode("\n", $domains);
            foreach ($domains as $item) {
                $domains[] = Domains::data(['domain' => trim(str_replace('http://', '', $item))])->add();
            }
        }

        if (count($domains) > 0) {
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

        $domains = Domains::update(
            ['status' => $this->request->request->get('status')],
            ['id' => $this->request->request->get('id')]
        );
        if ($domains->count === 1) {
            return json_encode([
                'name' => $this->request->request->get('domains'),
                'status' => 'success',
            ]);
        }

        return json_encode([
            'name' => $this->request->request->get('domains'),
            'status' => 'danger'
        ]);
    }
}
