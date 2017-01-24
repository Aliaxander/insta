<?php
/**
 * Created by OxGroup.
 * User: Aliaxander
 * Date: 04.05.16
 * Time: 16:07
 */

namespace OxApp\modules\jobs;

/**
 * Interface Cron
 *
 * @package OxApp\modules\jobs
 */
interface CronJobInterface
{
    /**
     * @return int Count work fobs
     */
    public function start();
}
