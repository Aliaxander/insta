<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 03.05.17
 * Time: 11:39
 */

namespace OxApp\controllers\api;

use Ox\App;
use OxApp\models\Gmail;

class GmailController extends App
{
    public function get()
    {
        $result = Gmail::find()->rows;

        return json_encode($result);
    }
}
