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
class Kma extends AbstractIntegrationsApi implements IntegrationInterface
{
    public $name = "kma.biz";
    public $cronFactory = "* * * * * *";
    public $apiId;
    public $apiHash;
    
    /**
     * @return bool
     */
    public function kmaAuth()
    {
        $result = $this->submitAction(
            "http://api.kma1.biz/?method=auth&username=oxcpa.ru@gmail.com&pass=A5842M7fnbpdxN8S"
        );
        if (!empty($result->authid)) {
            $this->apiId = @$result->authid;
            $this->apiHash = @$result->authhash;
        }
        sleep(1);
        
        return true;
    }
    
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
        $this->kmaAuth();
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
                    "method" => "addlead",
                    "authid" => $this->apiId,
                    "authhash" => $this->apiHash,
                    "phone" => @$clientData->phone,
                    "name" => @$clientData->name,
                    "ip" => @$clientData->ip,
                    "channel" => $campaign->integration->channel
                ]
            ];
            $url = "http://api.kma1.biz/";
            
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
            if (!empty($result)) {
                if (empty($result->orderid)) {
                    print_r(Leads::where(["id" => $action->id])->update(["exportId" => "error"]));
                    var_dump(LeadsControl::changeStatus($action->id, 3));
                    print_r(Leads::where(["id" => $action->id])->update(["customStatus" => 7]));
                } else {
                    $newId = $result->orderid;
                    echo "change: ", $newId;
                    print_r($result);
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
        $this->kmaAuth();
        foreach ($campaignResult as $campaign) {
            if (!empty($actionId[$campaign->id])) {
                $resultIds = array_chunk($actionId[$campaign->id], 50);
                foreach ($resultIds as $apiIds) {
                    $url = "http://api.kma1.biz/";
                    $params = [
                        "form_params" => [
                            "method" => "getstatuses",
                            "authid" => $this->apiId,
                            "authhash" => $this->apiHash,
                            "ids" => implode(",", $apiIds)
                        ]
                    ];
                    echo "\n\nURL {$url}:\n";
                    $result = $this->submitAction($url, $params);
                    print_r($result);
                    foreach ($result->statuses as $row) {
                        $leadId = $actionsData[$row->id]->id;
                        $status = $this->getValidStatus($row->status);
                        $this->changeOldStatus($actionsData[$row->id], $status, $leadId);
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
            case ("F"):
                $customStatus = 7;
                $newStatus = 3;
                break;
            case ("D"):
                $customStatus = 6;
                $newStatus = 3;
                break;
            case ("A"):
                $customStatus = 2;
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
            "channel" => "Код потока",
        ];
    }
}
