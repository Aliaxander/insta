<?php
/**
 * Created by OxGroup.
 * User: Aliaxander
 * Date: 14.12.16
 * Time: 14:33
 *
 * @category  Contest20016
 * @package   OxApp\modules\jobs
 * @author    Aliaxander
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://oxgroup.media/
 */

namespace OxApp\modules\jobs;

use OxApp\models\AllStats;
use OxApp\models\ContestUsers;

/**
 * Class Contest20016
 *
 * @package OxApp\modules\jobs
 */
class Contest20016 implements CronJobInterface
{
    public function start()
    {
        $time = strtotime(date("d.m.Y H:i:s"));
        if ($time <= strtotime("28.02.2017 23:59") &&
            $time >= strtotime("22.12.2016 00:00:00")
        ) {
            $users = ContestUsers::find(['contestId' => 1, 'status' => 1]);
            $result = [];
            $fullResult = [];
            foreach ($users->rows as $row) {
                $json = json_decode($row->progress);
                $fullResult[$json->position] = $row->userId;
                
                $stats = AllStats::selectBy(['count(aproveOrder) as count'])->find([
                    'user' => $row->userId,
                    'date/>=' => $row->dateCreate,
                    'aproveOrder' => 1,
                    ['%and', 'date/>=' => "2016-12-22 00:00:00"],
                ]);
                $count = $stats->rows[0]->count;
                $result[$count] = [
                    "id" => $row->id,
                    "user" => $row->userId,
                    "total" => $count,
                    "position" => $json->position
                ];
            }
            krsort($result);
            print_r($result);
            foreach ($result as $row) {
                $row = (object)$row;
                //100 500 1000 2500 5000
                if ($row->total >= 5000 && empty($fullResult[1])) {
                    $position = 1;
                } elseif ($row->total >= 2500 && empty($fullResult[2])) {
                    $position = 2;
                } elseif ($row->total >= 1000 && empty($fullResult[3])) {
                    $position = 3;
                } elseif ($row->total >= 500 && empty($fullResult[4])) {
                    $position = 4;
                } elseif ($row->total >= 100 && empty($fullResult[5])) {
                    $position = 5;
                } else {
                    $position = $row->position;
                }
                $fullResult[$position] = $row->user;
                echo "user: " . $row->user;
                $newObject = json_encode([
                    "total" => $row->total,
                    "position" => $position
                ]);
                print_r($newObject);
                ContestUsers::where(["id" => $row->id])->update(["progress" => $newObject]);
                echo "\n\n";
            }
        }
    }
}
