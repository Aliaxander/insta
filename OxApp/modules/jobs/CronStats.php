<?php
/**
 * Created by OxGroup.
 * User: Александр
 * Date: 20.09.2015
 * Time: 22:06
 */

namespace OxApp\modules\jobs;

use OxApp\models\Leads;
use OxApp\models\AllStats;
use OxApp\models\SystemStats;

/**
 * Class CronStats
 *
 * @package OxApp\modules\jobs
 */
class CronStats implements CronJobInterface
{
    private $count = 0;
    
    /**
     * @return int
     */
    public function start()
    {
        $this->getLeads(15);
        $this->getStats(2);
        
        return $this->count;
    }
    
    
    /**
     * @param int $days
     */
    protected function getStats($days = 2)
    {
        $date1 = date('Y-m-d');
        $date2 = date('Y-m-d', strtotime('-' . $days . ' day', strtotime($date1)));
        
        $sql = SystemStats::where(array("date/>=" => "{$date2} 00:00:00"))->find();
        
        if ($sql->count > 0) {
            $this->addStatsToDb($sql->rows);
        }
    }
    
    /**
     * @param $rows
     */
    private function addStatsToDb($rows)
    {
        foreach ($rows as $row) {
            $promoId = "";
            $flow = "";
            $sub1 = "";
            $sub2 = "";
            $sub3 = "";
            $sub4 = "";
            $sub5 = "";
            $detectGeo = "";
            $detectDeviceType = "";
            $detectPlatform = "";
            $detectBrowser = "";
            $ip = "";
            $referer = "";
            $this->count++;
            $clientData = @json_decode($row->clientData);
            if (AllStats::where(array("statid" => $row->id))->find()->count === 0) {
                $params = [
                    "promoId",
                    "ip",
                    "flow",
                    "sub1",
                    "sub2",
                    "sub3",
                    "sub4",
                    "sub5",
                    "detectGeo",
                    "deviceType",
                    "detectPlatform",
                    "detectBrowser"
                ];
                foreach ($params as $param) {
                    $$param = isset($clientData->$param) ? $clientData->$param : '';
                }
                
                $data = array(
                    "statid" => $row->id,
                    "user" => $row->user,
                    "advertiser" => $row->advertiser,
                    "ip" => $ip,
                    "referer" => $referer,
                    "unics" => $row->unic,
                    "hits" => $row->hit,
                    "date" => $row->date,
                    "offer" => $row->offer,
                    "promo" => $promoId,
                    "flow" => $flow,
                    "action" => $row->actionId,
                    "sub1" => $sub1,
                    "sub2" => $sub2,
                    "sub3" => $sub3,
                    "sub4" => $sub4,
                    "sub5" => $sub5,
                    "detectGeo" => $detectGeo,
                    "deviceType" => $detectDeviceType,
                    "detectPlatform" => $detectPlatform,
                    "detectBrowser" => $detectBrowser,
                );
                print_r(AllStats::data($data)->add());
            }
        }
    }

    /**
     * @param int $days
     */
    protected function getLeads($days = 15)
    {
        $date1 = date('Y-m-d');
        $date2 = date('Y-m-d', strtotime('-' . $days . ' day', strtotime($date1)));
        $sql = Leads::where(array("dateCreate/>=" => "{$date2} 00:00:00"))->find();
        if ($sql->count > 0) {
            foreach ($sql->rows as $row) {
                $clientData = @json_decode($row->clientData);
                $statData = @json_decode($row->statData);
                $this->count++;
                $data = array(
                    "orderid" => $row->id,
                    "user" => $row->user,
                    "advertiser" => $row->advertiser,
                    "ip" => @$clientData->ip,
                    "referer" => @$clientData->referer,
                    "date" => $row->dateCreate,
                    "offer" => $row->offer,
                    "promo" => @$statData->promoId,
                    "flow" => @$statData->flow->id,
                    "action" => $row->actionId,
                    "sub1" => @$clientData->sub1,
                    "sub2" => @$clientData->sub2,
                    "sub3" => @$clientData->sub3,
                    "sub4" => @$clientData->sub4,
                    "sub5" => @$clientData->sub5,
                    "`order`" => 1,
                    "detectGeo" => @$clientData->detectGeo,
                    "deviceType" => @$clientData->deviceType,
                    "detectPlatform" => @$clientData->detectPlatform,
                    "detectBrowser" => @$clientData->detectBrowser,
                );
                
                //Clean old data
                $data['declinedOrder'] = 0;
                $data['aproveOrder'] = 0;
                $data['waitOrder'] = 0;
                $data['declinedMoneyRub'] = 0;
                $data['declinedMoneyEur'] = 0;
                $data['declinedMoneyUsd'] = 0;
                $data['waitMoneyRub'] = 0;
                $data['waitMoneyEur'] = 0;
                $data['waitMoneyUsd'] = 0;
                $data['aproveMoneyRub'] = 0;
                $data['aproveMoneyEur'] = 0;
                $data['aproveMoneyUsd'] = 0;
                $data['advertiserPayRub'] = 0;
                $data['advertiserPayUsd'] = 0;
                $data['advertiserPayEur'] = 0;
                $data['waitOrder'] = 0;
                $data['aproveOrder'] = 0;
                $data['declinedOrder'] = 0;
                
                echo "\n\nSET STATUS = {$row->status}. Add data:\n";
                switch ($row->status) {
                    case (1):
                        $data['waitOrder'] = 1;
                        $data['waitMoneyRub'] = $row->actionPayRub;
                        $data['waitMoneyEur'] = $row->actionPayEur;
                        $data['waitMoneyUsd'] = $row->actionPayUsd;
                        break;
                    case (2):
                        $data['aproveOrder'] = 1;
                        $data['aproveMoneyRub'] = $row->actionPayRub;
                        $data['aproveMoneyEur'] = $row->actionPayEur;
                        $data['aproveMoneyUsd'] = $row->actionPayUsd;
                        switch ($row->advertiserCurrency) {
                            case (5):
                                $data['advertiserPayRub'] = $row->advertiserPay;
                                break;
                            case (9):
                                $data['advertiserPayUsd'] = $row->advertiserPay;
                                break;
                            case (12):
                                $data['advertiserPayEur'] = $row->advertiserPay;
                                break;
                        }
                        break;
                    case (3):
                        $data['declinedOrder'] = 1;
                        $data['declinedMoneyRub'] = $row->actionPayRub;
                        $data['declinedMoneyEur'] = $row->actionPayEur;
                        $data['declinedMoneyUsd'] = $row->actionPayUsd;
                        break;
                }
                print_r($data);
                if (AllStats::find(["orderid" => $row->id])->count === 0) {
                    echo "Add Lead:";
                    print_r($data);
                    print_r(AllStats::add($data));
                } else {
                    print_r(AllStats::where(["orderid" => $row->id])->update($data));
                }
            }
        }
    }
}
