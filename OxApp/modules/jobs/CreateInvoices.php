<?php
/**
 * Created by OxGroup.
 * User: Aliaxander
 * Date: 27.11.15
 * Time: 17:19
 */

namespace OxApp\modules\jobs;

use Ox\DataBase\AbstractModel;
use OxApp\helpers\Permissions;
use OxApp\models\Payments;
use OxApp\models\UserGroups;
use OxApp\models\Users;

/**
 * Class CreateInvoices
 *
 * @package OxApp\modules\jobs
 */
class CreateInvoices implements CronJobInterface
{
    /**
     * @return int
     */
    public function start()
    {
        $count = 0;
        $groups = UserGroups::selectBy(["id"])->find(array(
            "status" => 1,
            "permissions/like" => "%minAutoPaySum%"
        ));
        if ($groups->count > 0) {
            $groupId = [];
            foreach ($groups->rows as $row) {
                $groupId[] = $row->id;
            }
            $users = Users::find(array(
                "`group`/in" => $groupId
            ));
            if ($users->count > 0) {
                foreach ($users->rows as $user) {
                    $count += $this->checkAndPay($user);
                }
            }
        }
        
        return $count;
    }
    
    /**
     * @param $user
     *
     * @return int
     */
    protected function checkAndPay($user)
    {
        $count = 0;
        if (Permissions::hasUserPermission($user->id, "createPayment")) {
            $balanceRub = $user->balanceRub - $user->paidRub;
            $balanceEur = $user->balanceEur - $user->paidEur;
            $balanceUsd = $user->balanceUsd - $user->paidUsd;
            $minSumRub = Permissions::hasUserPermission($user->id, "minAutoPaySumRub");
            $minSumEur = Permissions::hasUserPermission($user->id, "minAutoPaySumEur");
            $minSumUsd = Permissions::hasUserPermission($user->id, "minAutoPaySumUsd");
            if ($balanceRub >= $minSumRub && $balanceRub > 0) {
                $count++;
                $this->createInvoice($user, $balanceRub, "5");
            }
            if ($balanceEur >= $minSumEur && $balanceRub > 0) {
                $count++;
                $this->createInvoice($user, $balanceEur, "9");
            }
            if ($balanceUsd >= $minSumUsd && $balanceRub > 0) {
                $count++;
                $this->createInvoice($user, $balanceUsd, "12");
            }
        }
        
        return $count;
    }
    
    /**
     * @param $user
     * @param $sum
     * @param $currency
     */
    protected function createInvoice($user, $sum, $currency)
    {
        Payments::addPayment($user, $sum, $currency, "Автоматическая выплата");
    }
}
