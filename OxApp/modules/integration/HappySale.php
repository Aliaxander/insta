<?php
/**
 * Created by OxGroup.
 * User: Aliaxander
 * Date: 15.10.16
 * Time: 11:33
 *
 * @category  HappySale
 * @package   OxApp\modules\integration
 * @author    Aliaxander
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://oxgroup.media/
 */

namespace OxApp\modules\integration;

use OxApp\helpers\LeadsControl;
use OxApp\helpers\integrations\AbstractIntegrationsApi;
use OxApp\models\Leads;
use OxApp\models\LogLeads;

/**
 * Class HappySale
 *
 * @package OxApp\modules\integration
 */
class HappySale extends AbstractIntegrationsApi implements IntegrationInterface
{
    public $name = "HappySale";
    public $cronFactory = "* * * * * *";
    private $uid = "34859234";
    private $secret = "349857ace2145";
    
    /**
     * @param $integer
     *
     * @return bool
     */
    public function checkLeads($integer)
    {
        $status = true;
        $campaigns = $this->getIntegrationCampaigns($integer);
        $leads = $this->getLeadsForStatus($campaigns['id']);
        $campaignResult = $campaigns['campaigns'];
        print_r($leads);
        //$actionId = [];
        foreach ($leads as $action) {
            if ($action->exportId !== "error") {
                $campaigns[$action->actionId] = $campaignResult[$action->actionId];
                //$actionId[$action->actionId][] = $action->exportId;
                $actionsData[$action->exportId] = $action;
            }
        }
        if (isset($actionsData)) {
            $this->onlyCustomStatus = false;
            $this->changeStatus($campaignResult, $leads, $actionsData);
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
        $status = true;
        $campaigns = $this->getIntegrationCampaigns($integer);
        $leads = $this->getLeadsForPayStatus($campaigns['id']);
        $campaignResult = $campaigns['campaigns'];
        //$actionId = [];
        foreach ($leads as $action) {
            if ($action->exportId !== "error") {
                $campaigns[$action->actionId] = $campaignResult[$action->actionId];
                // $actionId[$action->actionId][] = $action->exportId;
                $actionsData[$action->exportId] = $action;
            }
        }
        if (isset($actionsData)) {
            $this->onlyCustomStatus = true;
            $this->changeStatus($campaignResult, $leads, $actionsData);
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
        $status = true;
        echo "POST actions:\n";
        $campaigns = $this->getIntegrationCampaigns($integer);
        $leads = $this->getLeadsForPost($campaigns['id']);
        print_r($leads);
        $campaignResult = $campaigns['campaigns'];
        foreach ($leads as $lead) {
            $campaign = $campaignResult[$lead->actionId];
            $clientData = @json_decode($lead->clientData);
            $postData = [
                'country' => $campaign->actionData->geo->co,
                'addr' => @$clientData->address,
                'name' => @$clientData->name,
                'phone' => @$clientData->phone,
                'price' => $campaign->actionData->price,
                'order_id' => $lead->id,
                'offer' => $campaign->integration->offer,
                "secret" => $this->secret,
                'remote_ip' => @$clientData->ip
            ];
            
            $data = json_encode($postData);
            $hashStr = strlen($data) . md5($this->uid);
            $hash = hash('sha256', $hashStr);
            
            $params = [
                "form_params" => ["data" => $data]
            ];
            $url = "http://happy-crm.ru/api/send_order.php?uid=" . $this->uid . "&hash=" . $hash;
            
            echo "\n\nURL {$url}:\n";
            print_r($params);
            try {
                $result = $this->submitAction($url, $params);
            } catch (\Exception $e) {
                echo "error";
                print_r($e);
                $status = false;
            }
            
            if (!empty($result) and $result->result->success !== "FALSE") {
                $newId = $result->result->id;
                echo "change: ", $newId;
                print_r($result);
                print_r(Leads::where(["id" => $lead->id])->update(["exportId" => $newId]));
            } else {
                print_r(Leads::where(["id" => $lead->id])->update(["exportId" => "error"]));
                $result = "";
                var_dump(LeadsControl::changeStatus($lead->id, 3));
                print_r(Leads::where(["id" => $lead->id])->update(["customStatus" => 7]));
                $status = false;
            }
        }
        
        return $status;
    }
    
    
    /**
     * @param $campaignResult
     * @param $leads
     * @param $actionsData
     */
    private function changeStatus($campaignResult, $leads, $actionsData)
    {
        $leadResult = [];
        foreach ($leads as $lead) {
            if ($lead->exportId !== "error") {
                $leadResult[$lead->actionId][] = [
                    'id' => $lead->exportId,
                    'country' => $campaignResult[$lead->actionId]->actionData->geo->co
                ];
                $leadsData[$lead->exportId] = $lead;
            }
        }
        foreach ($campaignResult as $campaign) {
            if (!empty($leadResult[$campaign->id])) {
                $data = json_encode($leadResult[$campaign->id]);
                $hashStr = strlen($data) . md5($this->uid);
                $hash = hash('sha256', $hashStr);
                $url = 'http://happy-crm.ru/api/get_orders.php?uid=' .
                    $this->uid . '&hash=' . $hash . '&s=' . $this->secret;
                
                echo "{$url}:\n";
                $result = (array)$this->submitAction($url, ["form_params" => ["data" => $data]]);
                print_r($result);
                foreach ($result as $key => $val) {
                    $leadId = $leadsData[$key]->id;
                    print_r($key);
                    print_r($val);
                    if (isset($val->send_status)) {
                        $statusPay = $val->send_status;
                    } else {
                        $statusPay = $val->status;
                    }
                    $status = $this->getValidStatus(["pay" => $statusPay, "status" => $val->status]);
                    
                    $comment = @$val->fields->comment;
                    $logs = LogLeads::find(["actionId" => $leadId, "text" => $comment]);
                    if ($logs->count == 0 && $comment != "") {
                        LogLeads::add(["actionId" => $leadId, "text" => $comment, "status" => $status["status"]]);
                    }
                    //Status
                    $this->changeOldStatus($actionsData[$key], $status, $leadId);
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
        $newStatus = 1;
        $customStatus = 1;
        /*

         * 2 Отменён
         * 3 Перезвонить
         * 4 Недозвон
         * 5 Спам
         * 6 Черный список
         */
        switch ($integerStatus['status']) {
            case ("0"):
                $customStatus = $newStatus = 1;
                break;
            case ("1"):
                $customStatus = $newStatus = 2;
                break;
            case ("2"):
                $customStatus = $newStatus = 3;
                break;
            case ("3"):
                $customStatus = $newStatus = 1;
                break;
            case ("4"):
                $customStatus = $newStatus = 1;
                break;
            case ("5"):
                $customStatus = 7;
                $newStatus = 3;
                break;
            case ("6"):
                $customStatus = 7;
                $newStatus = 3;
                break;
        }
        
        /*
         * Доп. статусы: поле send_status
         * 1 Возврат
         * 2 Оплачен
         * false
         */
        switch ($integerStatus['pay']) {
            case ("1"):
                $customStatus = 6;
                break;
            case ("2"):
                $customStatus = 5;
                break;
        }
        
        return ["status" => $newStatus, "customStatus" => $customStatus];
    }
    
    /**
     * @return array
     */
    public function getParams()
    {
        return [
            "offer" => "Название оффера в CRM"
        ];
    }
}
