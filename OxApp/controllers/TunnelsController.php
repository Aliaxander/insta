<?php
/**
 * Created by PhpStorm.
 * User: irina
 * Date: 15.05.17
 * Time: 5:11
 */

namespace OxApp\controllers;


use Ox\App;
use Ox\View;
use OxApp\helpers\TunnelBroker;
use OxApp\models\TechAccount;
use OxApp\models\Tunnels;
use OxApp\models\Users;

/**
 * Class TunnelsController
 *
 * @package OxApp\controllers
 */
class TunnelsController extends App
{
    public function get()
    {
        /*
         * Status:
         * 0 - Delete
         * 1 - Wait Create
         * 2 - Wait settings server
         * 3 - Settings server
         * 4 - Work
         *
         */
        return View::build("tunnel");
    }
    
    public function post()
    {
        $id = $this->request->request->get('id');
        $tunnel = Tunnels::find(['id' => $id]);
        if ($tunnel->count > 0) {
            $tunnel = $tunnel->rows[0];
            Users::delete(['proxy/like' => $tunnel->serverIp . ':%', 'userGroup' => 1, 'ban' => 0]);
            $tunnelData = TechAccount::find(['id' => $tunnel->tunnelAccountId])->rows[0];
            $tunel = new TunnelBroker();
            $tunel->login($tunnelData->name, $tunnelData->password);
            $tunel->deleteTunnel($tunnel->tunnelId);
            Tunnels::where(['id' => $id])->update(['status' => 0]);
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}