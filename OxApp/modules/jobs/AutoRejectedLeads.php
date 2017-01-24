<?php
/**
 * Created by OxGroup.
 * User: Aliaxander
 * Date: 30.12.16
 * Time: 20:56
 */

namespace OxApp\modules\jobs;

use OxApp\models\Leads;

/**
 * Class AutoRejectedLeads
 *
 * @package OxApp\modules\jobs
 */
class AutoRejectedLeads implements CronJobInterface
{
    /**
     * @return int
     */
    public function start()
    {
        Leads::where([
            "status/in" => [1, 4],
            "dateCreate/<=" => "//now()-interval 5 day//"
        ])->update(["status" => 3]);
        
        return 1;
    }
}
