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
class Leadvertex extends AbstractIntegrationsApi implements IntegrationInterface
{
    public $name = "Leadvertex";
    public $cronFactory = "* * * * * *";
    
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
        foreach ($leads as $action) {
            $campaign = $campaignResult[$action->actionId];
            $clientData = @json_decode($action->clientData);
            $params = [
                "form_params" => [
                    'country' => $campaign->actionData->geo->co,
                    'address' => @$clientData->address,
                    'fio' => @$clientData->name,
                    'phone' => @$clientData->phone,
                    'price' => $campaign->actionData->price,
                    'total' => $campaign->actionData->price,
                    'quantity' => 1,
                    'additional1' => $action->user,
                    'additional2' => $campaign->title,
                    'ip' => @$clientData->ip
                ]
            ];
            if (!empty($campaign->integration->leadOptions)) {
                foreach ($campaign->integration->leadOptions as $values) {
                    foreach ($values as $key => $val) {
                        $params['form_params'][$key] = @$action->$val;
                    }
                }
            }
            if (!empty($campaign->integration->actionDataOptions)) {
                foreach ($campaign->integration->actionDataOptions as $values) {
                    foreach ($values as $key => $val) {
                        $params['form_params'][$key] = @$campaign->actionData->$val;
                    }
                }
            }
            if (!empty($campaign->integration->campaignDataOptions)) {
                foreach ($campaign->integration->campaignDataOptions as $values) {
                    foreach ($values as $key => $val) {
                        $params['form_params'][$key] = @$campaign->$val;
                    }
                }
            }
            $url = "https://" . $campaign->integration->domain .
                "/api/webmaster/v2/addOrder.html?webmasterID=" . $campaign->integration->webmaster .
                "&token=" . $campaign->integration->token;
            
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
            var_dump($result);
            if (!empty($result)) {
                $newId = @$result->OK;
                echo "change: ", $newId;
                print_r($result);
                if (!empty($newId)) {
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
                    $url = "https://" . $campaign->integration->domain .
                        "/api/webmaster/v2/getOrdersByIds.html?webmasterID=" . $campaign->integration->webmaster .
                        "&token=" . $campaign->integration->token .
                        "&ids=" . implode(",", $apiIds);
                    
                    echo "\n\nURL {$url}:\n";
                    $result = json_decode(file_get_contents($url));
                    // rint_r($result);
                    foreach ($result as $key => $val) {
                        $leadId = $actionsData[$key]->id;
                        print_r($val);
                        if ($val->status === null) {
                            $status = "wait";
                            //status - статус вознаграждения. -1 - отказано, 0 - в обработке, 1 - выплачено
                            switch ($val->payment->status) {
                                case (-1):
                                    $status = "canceled";
                                    break;
                                case (1):
                                    $status = "accepted";
                                    break;
                            }
                        } else {
                            $status = $val->status;
                        }
                        $status = $this->getValidStatus($status);
                        
                        //Add comment:
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
            case ("spam"):
                $customStatus = 7;
                $newStatus = 3;
                break;
            case ("return"):
                $customStatus = 6;
                $newStatus = 3;
                break;
            case ("canceled"):
                $customStatus = 4;
                $newStatus = 3;
                break;
            case ("accepted"):
                $customStatus = 2;
                $newStatus = 2;
                break;
            case ("paid"):
                $customStatus = 5;
                $newStatus = 2;
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
            "token" => "Ключ API",
            "domain" => "Домен оффера",
            "webmaster" => "ID вебмастера"
        ];
    }
}
