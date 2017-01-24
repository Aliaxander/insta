<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 06.11.15
 * Time: 14:07
 */

namespace OxApp\modules\jobs;

use OxApp\helpers\SandMail;
use OxApp\models\QueueMail;

/**
 * Class CronMail
 *
 * @package OxApp\modules\jobs
 */
class CronMail implements CronJobInterface
{
    /**
     * @return int
     */
    public function start()
    {
        $count = 0;
        $mails = QueueMail::find(array("import" => 0));
        if ($mails->count > 0) {
            foreach ($mails->rows as $mail) {
                $status = 1;
                try {
                    SandMail::sand($mail->title, $mail->message, $mail->to);
                } catch (\Exception $error) {
                    $status = json_encode(
                        array(
                            "message" => $error->getMessage(),
                            "file" => $error->getFile(),
                            "line" => $error->getLine(),
                            "code" => $error->getCode(),
                            "previous" => $error->getPrevious(),
                            "trace" => $error->getTrace(),
                        )
                    );
                }
                QueueMail::where(array("id" => $mail->id))->update(array("import" => 1, "status" => $status));
                $count++;
            }
        }

        return $count;
    }
}
