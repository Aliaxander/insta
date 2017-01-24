<?php
/**
 * Created by OxGroup.
 * User: Aliaxander
 * Date: 07.11.16
 * Time: 13:45
 *
 * @category  Kma
 * @package   OxApp\modules\integration
 * @author    Aliaxander
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://oxgroup.media/
 */

namespace OxApp\modules\integration;

use OxApp\helpers\LeadsControl;
use OxApp\helpers\integrations\AbstractIntegrationsApi;
use OxApp\models\Leads;

/**
 * Class Kma
 *
 * @package OxApp\modules\integration
 */
class Ad1 extends AbstractIntegrationsApi implements IntegrationInterface
{
    public $name = "ad1.ru";
    public $cronFactory = "* * * * * *";
    private $apiKey = "a28f576eb7";
    
    /**
     * @param $integer
     *
     * @return bool
     */
    public function checkLeads($integer)
    {
        //Не поддерживается рекламодателем
        return isset($integer);
    }
    
    
    /**
     * @param $integer
     *
     * @return bool
     */
    public function payStatusLeads($integer)
    {
        //Не поддерживается рекламодателем
        return isset($integer);
    }
    
    
    /**
     * @param $integer
     *
     * @return boolean
     */
    public function addLeads($integer)
    {
        echo "SET INTEGER add leads ID {$integer}\n";
        $status = true;
        echo "POST actions:\n";
        $campaigns = $this->getIntegrationCampaigns($integer);
        $leads = $this->getLeadsForPost($campaigns['id']);
        print_r($leads);
        $campaignResult = $campaigns['campaigns'];
        foreach ($leads as $action) {
            $campaign = $campaignResult[$action->actionId];
            $clientData = @json_decode($action->clientData);
            $params = [
                "json" =>
                    [
                        'orders' => [
                            [
                                'country' => @$clientData->detectGeo, //страна доставки
                                'fio' => @$clientData->name, // Имя
                                'phone' => @$clientData->phone, // Телефон
                                'user_ip' => @$clientData->ip, //ip пользователя
                                'user_agent' => @$clientData->userAgent, //UserAgent пользователя
                                'order_time' => time(), //timestamp времени заказа
                            ]
                        ],
                        'system' => [
                            'network' => 'ad1', // название сети
                            'thread' => $campaign->integration->thread, // id потока из ad1.ru, например bakm
                            'site_key' => $this->apiKey, // ключ
                            'subid' => $action->id
                        ]
                    ]
            ];
            $url = "http://infocdn.org/interface/api.php";
            
            echo "\n\nURL {$url}:\n";
            print_r($params);
            
            try {
                $result = $this->submitAction($url, $params);
            } catch (\Exception $e) {
                echo "error";
                print_r($e);
                print_r(Leads::where(["id" => $action->id])->update(["exportId" => "error"]));
                $result = "";
                var_dump(LeadsControl::changeStatus($action->id, 3));
                print_r(Leads::where(["id" => $action->id])->update(["customStatus" => 7]));
            }
            print_r($result);
            
            print_r(Leads::where(["id" => $action->id])->update(["`exportId`" => "ok"]));
        }
        
        return $status;
    }
    
    
    /**
     * @return array
     */
    public function getParams()
    {
        return [
            "thread" => "ID потока",
        ];
    }
}
