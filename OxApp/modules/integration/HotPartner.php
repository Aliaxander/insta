<?php
/**
 * Created by OxGroup.
 * User: Aliaxander
 * Date: 16.10.16
 * Time: 13:33
 *
 * @category  HotPartner
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
 * Class HotPartner
 *
 * @package OxApp\modules\integration
 */
class HotPartner extends AbstractIntegrationsApi implements IntegrationInterface
{
    public $name = "HotPartner";
    public $cronFactory = "* * * * * *";
    private $email = "oxcpa@hotpart.biz";
    private $token = "yefpES_o6_u_Dz4dyEUs";
    
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
        $status = true;
        echo "POST actions:\n";
        $campaigns = $this->getIntegrationCampaigns($integer);
        $leads = $this->getLeadsForPost($campaigns['id']);
        print_r($leads);
        $campaignResult = $campaigns['campaigns'];
        foreach ($leads as $lead) {
            $campaign = $campaignResult[$lead->actionId];
            $clientData = @json_decode($lead->clientData);
            $params = [
                "form_params" => [
                    "order" => [
                        'country' => $campaign->integration->country,
                        'name' => @$clientData->name,
                        'telephone' => @$clientData->phone,
                        'order_items_attributes' => [
                            [
                                "product_id" => $campaign->integration->productId,
                                "price" => $campaign->actionData->price,
                                "quantity" => 1
                            ]
                        ],
                        'prices_in_currency' => 1,
                        'pl' => $lead->user,
                        "delivery_price_xml" => $campaign->integration->delivery,
                    ]
                ]
            ];
            $url = "http://77.72.135.109:81/orders?user_email={$this->email}&user_token={$this->token}";
            $result = "error";
            echo "\n\nURL {$url}:\n";
            print_r($params);
            try {
                $result = $this->submitAction($url, $params);
            } catch (\Exception $e) {
                echo "error";
                print_r($e);
                $status = false;
            }
            print_r($result);
            if (!empty($result) and !empty($result[0]->order->id)) {
                $newId = $result[0]->order->id;
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
     * @param $actionId
     * @param $actionsData
     */
    private function changeStatus($campaignResult, $actionId, $actionsData)
    {
        foreach ($campaignResult as $campaign) {
            if (!empty($actionId[$campaign->id])) {
                $resultIds = array_chunk($actionId[$campaign->id], 50);
                foreach ($resultIds as $apiIds) {
                    print_r($apiIds);
                    $url = "http://77.72.135.109:81/orders?user_email={$this->email}&user_token=" . $this->token .
                        "&order_ids[]=" . implode("&order_ids[]=", $apiIds);
                    
                    echo "\n\nURL {$url}:\n";
                    $result = (array)$this->submitAction($url);
                    print_r($result);
                    foreach ($result as $val) {
                        $orderId = $val->order->id;
                        $leadId = $actionsData[$orderId]->id;
                        print_r($val);
                        $status = $this->getValidStatus([
                            "pay" => $val->order->substatus,
                            "status" => $val->order->status
                        ]);
                        //"status":"incorrect","substatus":"incorrect"
                        //Add comment:
                        //                        $comment = @$val->fields->comment;
                        //                        $logs = LogLeads::find(["actionId" => $leadId, "text" => $comment]);
                        //                        if ($logs->count == 0 && $comment != "") {
                        //                            LogLeads::add(["actionId" => $leadId, "text" => $comment,
                        // "status" => $status["status"]]);
                        //                        }
                        
                        //Status
                        $this->changeOldStatus($actionsData[$orderId], $status, $leadId);
                    }
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
        
        /**
         * new - новый
         * success - подтвержден
         * canceled - отменен
         * incorrect - некорретен
         */
        $newStatus = 1;
        $customStatus = 1;
        switch ($integerStatus['status']) {
            case ("canceled"):
                $newStatus = 3;
                break;
            case ("incorrect"):
                $newStatus = 3;
                $customStatus = 7;
                break;
            case ("new"):
                $newStatus = 1;
                break;
            case ("success"):
                $newStatus = 2;
                break;
        }
        
        switch ($integerStatus['pay']) {
            case ("payed"):
                $customStatus = 5;
                break;
            case ("return"):
                $customStatus = 6;
                break;
            case ("double"):
                $customStatus = 7;
                break;
            case ("incorrect"):
                $customStatus = 7;
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
            "productId" => "Id товара",
            "country" => "Код страны (3 символа ISO 3166)",
            "delivery" => "Цена доставки"
        ];
    }
}
