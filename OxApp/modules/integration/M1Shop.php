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
 * Class M1Shop
 *
 * @package OxApp\modules\integration
 */
class M1Shop extends AbstractIntegrationsApi implements IntegrationInterface
{
    public $name = "M1Shop";
    public $cronFactory = "* * * * * *";
    
    /**
     * @param $integer
     *
     * @return boolean
     */
    public function checkLeads($integer)
    {
        /*
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
        foreach ($campaignResult as $campaign) {
            if (!empty($actionId[$campaign->id])) {
                $url = "http://m1-shop.ru/get_order_status/?ref=" . $campaign->integration->wmid .
                    "&api_key=" .
                    $campaign->integration->apikey .
                    "&id=" . implode(",", $actionId[$campaign->id]);

                echo "\n\nURL {$url}:\n";
                $result = (array)$this->submitAction($url);
                //print_r($result);
                if (!empty($result->result)) {
                    foreach ($result->result as $key => $val) {
                        $m1Id = $val->m1_id;
                        print_r($key);
                        print_r($val);
                        $newStatus = 1;
                        $customStatus = 1;
                        switch ($val->status) {
                            case ("0"):
                                $newStatus = 1;
                                $customStatus = 1;
                                break;
                            case ("1"):
                                $newStatus = 2;
                                $customStatus = 2;
                                break;
                            case ("2"):
                                $newStatus = 3;
                                $customStatus = 3;
                                break;
                            case ("3"):
                                $newStatus = 3;
                                $customStatus = 5;
                                break;
                            case ("4"):
                                $newStatus = 3;
                                $customStatus = 6;
                                break;
                        }
                        $oldCustomStatus = $actionsData[$m1Id]->customStatus;
                        echo "\n Custom status {$customStatus} != {$oldCustomStatus} and status {$customStatus} != 1";
                        if ($customStatus != $oldCustomStatus && $customStatus != 1) {
                            echo "Change:";
                            print_r(Leads::where(
                                ["id" => $actionsData[$m1Id]->id]
                            )->update(
                                ["customStatus" => $customStatus]
                            ));
                        }

                        echo "\n New status {$newStatus} != {$actionsData[$m1Id]->status} old status";
                        if ($newStatus !== $actionsData[$m1Id]->status) {
                            var_dump(LeadsControl::changeStatus($actionsData[$m1Id]->id, $newStatus));
                        }
                        $comment = $val->callcenter_comment;
                        if (!empty($comment)) {
                            $logs = LogLeads::find([
                                "text" => $comment,
                                "actionId" => $actionsData[$m1Id]->id,
                                "status" => $newStatus
                            ]);
                            if ($logs->count == 0) {
                                LogLeads::add([
                                    "text" => $comment,
                                    "actionId" => $actionsData[$m1Id]->id,
                                    "status" => $newStatus
                                ]);
                            }
                        }
                    }
                }
            }
        }

        return $status;*/
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
                    'name' => @$clientData->name,
                    'phone' => @$clientData->phone,
                    'ip' => @$clientData->ip
                ]
            ];
            $url = "http://m1-shop.ru/send_order/?ref={$campaign->integration->wmid}&api_key=" .
                $campaign->integration->apikey . "&product_id={$campaign->integration->productId}";
            
            echo "\n\nURL {$url}:\n";
            print_r($params);
            try {
                $result = $this->submitAction($url, $params);
            } catch (\Exception $e) {
                echo "error";
                // print_r($e);
                print_r(Leads::where(["id" => $action->id])->update(["exportId" => "error"]));
                $result = "";
                var_dump(LeadsControl::changeStatus($action->id, 3));
                print_r(Leads::where(["id" => $action->id])->update(["customStatus" => 7]));
            }
            
            if (!empty($result->result) && $result->result === "ok") {
                $newId = $result->id;
                echo "change: ", $newId;
                print_r($result);
                print_r(Leads::where(["id" => $action->id])->update(["`exportId`" => $newId]));
            } else {
                $status = false;
            }
        }
        
        return $status;
    }
    
    /**
     * @return array
     */
    public function getParams()
    {
        return [
            "apikey" => "Ключ API",
            "productID" => "ID оффера",
            "wmid" => "ID вебмастера"
        ];
    }
}
