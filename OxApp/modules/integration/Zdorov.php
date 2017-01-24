<?php
/**
 * Created by OxGroup.
 * User: Aliaxander
 * Date: 17.01.17
 * Time: 12:58
 *
 * @category  Zdorov
 * @package   OxApp\modules\integration
 * @author    Aliaxander
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://oxgroup.media/
 */

namespace OxApp\modules\integration;

use OxApp\helpers\integrations\AbstractIntegrationsApi;
use OxApp\helpers\LeadsControl;
use OxApp\models\Leads;
use OxApp\models\LogLeads;
use Salaros\Vtiger\VTWSCLib\WSClient;

/**
 * Class Zdorov
 *
 * @package OxApp\modules\integration
 */
class Zdorov extends AbstractIntegrationsApi implements IntegrationInterface
{
    public $name = "Zdorov";
    public $cronFactory = "* * * * * *";
    private $login = "oxcpa@crm.zdorov.top";
    private $password = "M6nk94gJjAgBcYKv";
    
    /**
     * @param $integer
     *
     * @return bool
     */
    public function checkLeads($integer)
    {
        echo "SET INTEGER check leads ID {$integer}\n";
        $status = true;
        $campaigns = $this->getIntegrationCampaigns($integer);
        $leads = $this->getLeadsForStatus($campaigns['id']);
        $campaignResult = $campaigns['campaigns'];
        print_r($leads);
        $actionId = [];
        foreach ($leads as $action) {
            if ($action->exportId !== "error") {
                $campaigns[$action->actionId] = $campaignResult[$action->actionId];
                $actionId[$action->actionId][] = $action->exportId;
                $actionsData[$action->exportId] = $action;
            }
        }
        if (isset($actionsData)) {
            $this->onlyCustomStatus = false;
            $this->changeStatus($campaignResult, $actionId, $actionsData);
        }
        
        return $status;
    }
    
    
    /**
     * @param $integer
     *
     * @return boolean
     */
    public function payStatusLeads($integer)
    {
        echo "SET INTEGER pay status leads ID {$integer}\n";
        $status = true;
        $campaigns = $this->getIntegrationCampaigns($integer);
        $leads = $this->getLeadsForPayStatus($campaigns['id']);
        $campaignResult = $campaigns['campaigns'];
        $actionId = [];
        foreach ($leads as $action) {
            if ($action->exportId !== "error") {
                $campaigns[$action->actionId] = $campaignResult[$action->actionId];
                $actionId[$action->actionId][] = $action->exportId;
                $actionsData[$action->exportId] = $action;
            }
        }
        if (isset($actionsData)) {
            $this->onlyCustomStatus = true;
            $this->changeStatus($campaignResult, $actionId, $actionsData);
        }
        
        return $status;
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
            
            $request = array();
            $request['sostatus'] = 'Новый'; // статус заказа новый
            if (empty($clientData->name)) {
                $clientData->name = "Уточнить по телефону";
            }
            $request['sp_firstname'] = @$clientData->name; // ФИО клиента
            $request['sp_client_mobile'] = @$clientData->phone; // Телефон клиента
            $request['sp_country'] = mb_strtoupper($campaign->integration->country);
            $request['sp_timezone'] = "+3";
            $request['sp_offer'] = $campaign->integration->offerCode; // оффер. коды офферов в таблице googledocs
            $request['sp_ip'] = @$clientData->ip; // ip-адрес клиента
            $request['sp_geo_country'] = mb_strtoupper($campaign->integration->country);
            if (empty($clientData->hostName)) {
                $clientData->hostName = "API";
            }
            $request['sp_landing_url'] = $clientData->hostName; // url лэндинга, с которого пришел заказ
            
            $request['language_landing'] = mb_strtoupper($campaign->integration->country); // язык лендинга
            /* RU KZ AZ EN DE FR */
            
            $request["sp_utm_source"] = "oxcpa"; //символьное название CPA-сети
            
            $request["sp_utm_content"] = $action->user;  //№ веб-мастера в CPA сети
            $request['sp_net_so_number'] = $action->id; //№ заказа в CPA-сети
            $request['sp_lead_cost_pp'] = $action->advertiserPay; //цена за лид ПП
            
            $request['sp_landing_type'] = "Адаптив"; // Тип лендинга "Мобильный", "Веб", "Айфрейм", "Адаптив"
            
            //обязательный массив
            $request['currency_id'] = '21x1';
            $request['productid'] = '14x22979';
            $request['LineItems'][0]['quantity'] = 1;
            $request['LineItems'][0]['productid'] = '14x22979';
            $request['LineItems'][0]['unit_price'] = 0;
            echo "request:" . $request;
            $vtigerConnector = new WSClient('http://crm.zdorov.top/webservice.php');
            $vtigerConnector->login($this->login, $this->password);
            $createResult = $vtigerConnector->entityCreate('SalesOrder', $request);
            $vtigerConnector->invokeOperation('logout');
            print_r($createResult);
            if (!empty($createResult)) {
                if (empty($createResult['salesorder_custom'])) {
                    print_r(Leads::where(["id" => $action->id])->update(["exportId" => "error"]));
                    var_dump(LeadsControl::changeStatus($action->id, 3));
                    print_r(Leads::where(["id" => $action->id])->update(["customStatus" => 7]));
                } else {
                    $newId = $createResult['salesorder_custom'];
                    echo "change: ", $newId;
                    print_r(Leads::where(["id" => $action->id])->update(["`exportId`" => $newId]));
                }
            } else {
                $status = false;
            }
        }
        
        return $status;
    }
    
    
    /**
     * @param $campaignResult
     * @param $actionId
     * @param $actionsData
     */
    private function changeStatus($campaignResult, $actionId, $actionsData)
    {
        foreach ($campaignResult as $campaign) {
            if (!empty($actionId[$campaign->id])) {
                $resultIds = array_chunk($actionId[$campaign->id], 50);
                foreach ($resultIds as $apiIds) {
                    $vtigerConnector = new WSClient('http://crm.zdorov.top/webservice.php');
                    var_dump($vtigerConnector->login($this->login, $this->password));  // Пользователь = CPA-сеть
                    $result = $vtigerConnector->query("SELECT * FROM SalesOrder where salesorder_custom in ('" .
                        implode("','", $apiIds) . "');");
                    
                    foreach ($result as $row) {
                        $leadId = $actionsData[$row['salesorder_custom']]->id;
                        $status = $this->getValidStatus($row['sostatus']);
                        
                        $comment = $row['comment'];
                        if (empty($comment) && !empty($status['comment'])) {
                            $comment = $status['comment'];
                        }
                        if (!empty($comment)) {
                            $logs = LogLeads::find(["actionId" => $leadId, "text" => $comment]);
                            if ($logs->count == 0 && $comment != "") {
                                LogLeads::add([
                                    "actionId" => $leadId,
                                    "text" => $comment,
                                    "status" => $status["status"]
                                ]);
                            }
                        }
                        $this->changeOldStatus($actionsData[$row['salesorder_custom']], $status, $leadId);
                    }
                    
                    $vtigerConnector->invokeOperation('logout');
                }
            }
        }
    }
    
    /**
     * @param $integerStatus
     *
     * @return array
     */
    protected function getValidStatus($integerStatus)
    {
        $customStatus = 1;
        $newStatus = 1;
        $comment = "";
        switch ($integerStatus) {
            case ("Брак"):
                $customStatus = 7;
                $newStatus = 3;
                $comment = "Фейк/Спам";
                break;
            case ("Дубль"):
                $customStatus = 7;
                $newStatus = 3;
                $comment = "Дубль";
                break;
            case ("Тест"):
                $customStatus = 7;
                $newStatus = 3;
                $comment = "Тест";
                break;
            case ("Фейк"):
                $customStatus = 7;
                $newStatus = 3;
                $comment = "Фейк/Спам";
                break;
            case ("Фейк вернулся"):
                $customStatus = 7;
                $newStatus = 3;
                $comment = "Фейк/Спам";
                break;
            case ("Отказ"):
                $customStatus = 3;
                $newStatus = 3;
                $comment = "Отказ по телефону";
                break;
            case ("Не целевой"):
                $customStatus = 3;
                $newStatus = 3;
                $comment = "Не целевой";
                break;
            case ("Консультация"):
                $customStatus = 3;
                $newStatus = 3;
                $comment = "Только консультация";
                break;
            case ("Недозвон"):
                $customStatus = 3;
                $newStatus = 3;
                $comment = "Окончательный недозвон";
                break;
            case ("Отправлять"):
                $customStatus = 2;
                $newStatus = 2;
                break;
            case ("Доставка согласована"):
                $customStatus = 2;
                $newStatus = 2;
                break;
            case ("Приостановлен"):
                $customStatus = 2;
                $newStatus = 2;
                break;
            case ("Доставить позже"):
                $customStatus = 2;
                $newStatus = 2;
                break;
            case ("Отправлен"):
                $customStatus = 2;
                $newStatus = 2;
                break;
            case ("Товар в точке получения"):
                $customStatus = 2;
                $newStatus = 2;
                break;
            case ("Товар получен"):
                $customStatus = 2;
                $newStatus = 2;
                break;
            case ("Требует доработки оператора"):
                $customStatus = 2;
                $newStatus = 2;
                break;
            case ("Доставлять"):
                $customStatus = 2;
                $newStatus = 2;
                break;
            case ("Деньги получены"):
                $customStatus = 5;
                $newStatus = 2;
                break;
            case ("Отказ от отправки"):
                $customStatus = 2;
                $newStatus = 2;
                break;
            case ("Отказ от получения"):
                $customStatus = 6;
                $newStatus = 2;
                break;
            case ("Посылка вернулась"):
                $customStatus = 6;
                $newStatus = 2;
                break;
            case ("Деньги получены от потери"):
                $customStatus = 2;
                $newStatus = 2;
                break;
        }
        
        return ["status" => $newStatus, "customStatus" => $customStatus, "comment" => $comment];
    }
    
    /**
     * @return array
     */
    public function getParams()
    {
        return [
            'country' => 'Код страны из таблицы',
            'offerCode' => 'Код оффера'
        ];
    }
}
