<?php
/**
 * Created by OxGroup.
 * User: Aliaxander
 * Date: 02.08.16
 * Time: 14:58
 */

namespace OxApp\modules\integration;

/**
 * Interface Cron
 *
 * @package OxApp\modules\jobs
 */
interface IntegrationInterface
{
    /**
     * @param $integer
     *
     * @return boolean
     */
    public function checkLeads($integer);
    
    /**
     * @param $integer
     *
     * @return boolean
     */
    public function payStatusLeads($integer);
    
    /**
     * @param $integer
     *
     * @return boolean
     */
    public function addLeads($integer);
    
    /**
     * @return array
     */
    public function getParams();
}
