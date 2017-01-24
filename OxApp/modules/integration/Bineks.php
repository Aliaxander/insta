<?php
/**
 * Created by OxGroup.
 * User: Aliaxander
 * Date: 02.08.16
 * Time: 14:54
 *
 * @category  Leadvertex
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
 * Class Leadvertex
 *
 * @package OxApp\modules\integration
 */
class Bineks extends AbstractIntegrationsApi implements IntegrationInterface
{
    public $name = "Bineks";
    public $cronFactory = "* * * * * *";
    private $login = "oxcpa.wm";
    private $pass = "ahzC3XOx";
    
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
            $campaigns[$action->actionId] = $campaignResult[$action->actionId];
            $actionId[$action->actionId][] = $action->id;
            $actionsData[$action->id] = $action;
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
                $actionId[$action->actionId][] = $action->id;
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
        foreach ($leads as $action) {
            $campaign = $campaignResult[$action->actionId];
            $clientData = @json_decode($action->clientData);
            $params = [
                "form_params" => [
                    "Order" => [
                        'country' => strtoupper($campaign->actionData->geo->co),
                        'currency' => strtoupper($campaign->actionData->geo->codeCurr),
                        'discount' => '',
                        'client_address' => @$clientData->address,
                        'client_email' => @$clientData->email,
                        'client_fio' => @$clientData->name,
                        'client_phone' => @$clientData->phone,
                        'ip' => @$clientData->ip,
                        'total_amount' => $campaign->actionData->price,
                        'shop_dn' => $campaign->integration->shopDn,
                        'order_id' => $action->id
                    ],
                    'ULoginForm' =>
                        [
                            'login' => "oxcpa",
                            'password' => "4Z6znawb"
                        ]
                ]
            ];
            $url = "http://bm.bineks.ru/api/order/create";
            
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
            
            if (!empty($result->status) && $result->status === "ok") {
                print_r($result);
                print_r(Leads::where(["id" => $action->id])->update(["exportId" => "ok"]));
            } elseif (!empty($result->status)) {
                var_dump(LeadsControl::changeStatus($action->id, 3));
                print_r(Leads::where(["id" => $action->id])->update(["customStatus" => 7]));
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
                    if (!empty($apiIds)) {
                        $campaign = $campaignResult[$campaign->id];
                        $url = "http://bm.bineks.ru/api/order/getOxcpaStatuses?ULoginForm[login]=" .
                            $this->login . "&ULoginForm[password]=" . $this->pass;
                        echo "\n\nURL {$url}:\n";
                        $result = (array)$this->submitAction(
                            $url,
                            ["json" => ["ids" => @implode(",", $apiIds)]]
                        );
                        print_r($result);
                        foreach ($result as $val) {
                            $oldId = $val->order_id;
                            $leadId = $actionsData[$oldId]->id;
                            print_r($val);
                            $status = $this->getValidStatus($val->status);
                            
                            //Add comment:
                            $comment = @$val->rejection;
                            $logs = LogLeads::find(["actionId" => $leadId, "text" => $comment]);
                            if ($logs->count == 0 && $comment != "") {
                                LogLeads::add([
                                    "actionId" => $leadId,
                                    "text" => $comment,
                                    "status" => $status["status"]
                                ]);
                            }
                            
                            //Status
                            $this->changeOldStatus($actionsData[$oldId], $status, $leadId);
                        }
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
        $customStatus = 1;
        $newStatus = 1;
        switch ($integerStatus) {
            case ("0"):
                $customStatus = $newStatus = 1;
                break;
            case ("1"):
                $customStatus = $newStatus = 1;
                break;
            case ("2"):
                $customStatus = $newStatus = 2;
                break;
            case ("4"):
                $customStatus = $newStatus = 3;
                break;
            case ("6"):
                $newStatus = 3;
                $customStatus = 6;
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
            "shopDn" => "Домен лендинга"
        ];
    }
}
