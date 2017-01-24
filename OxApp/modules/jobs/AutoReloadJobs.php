<?php
/**
 * Created by OxGroup.
 * User: Aliaxander
 * Date: 10.11.16
 * Time: 13:42
 *
 * @category  AutoReloadJobs
 * @package   OxApp\modules\jobs
 * @author    Aliaxander
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://oxgroup.media/
 */

namespace OxApp\modules\jobs;

use OxApp\models\CronJobs;

/**
 * Class AutoReloadJobs
 *
 * @package OxApp\modules\jobs
 */
class AutoReloadJobs implements CronJobInterface
{
    /**
     * @return int
     */
    public function start()
    {
        $count = 0;
        $jobs = CronJobs::find(["status" => 1]);
        if ($jobs->count > 0) {
            foreach ($jobs->rows as $row) {
                if ($row->stop === "0000-00-00 00:00:00") {
                    echo "\n wait...";
                    $date1 = new \DateTime($row->start);
                    $date2 = new \DateTime(date('Y-m-d H:i:s'));
                    $diff = $date1->diff($date2);
                    if ($diff->i >= 10) {
                        echo "\nKILL NOW!";
                        $result = CronJobs::where(["id" => $row->id])->update(["stop" => date("Y-m-d H:i:s")]);
                        print_r($result);
                    }
                }
                $count++;
            }
        }
        
        return $count;
    }
}
