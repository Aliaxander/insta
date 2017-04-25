<?php
/**
 * Created by PhpStorm.
 * User: irina
 * Date: 25.04.17
 * Time: 3:41
 */

namespace OxApp\models;


use Ox\DataBase\AbstractModel;

/**
 * Class SystemSettings
 *
 * @package OxApp\models
 */
class SystemSettings extends AbstractModel
{
    protected static $from = 'systemSettings';
    
    public static function get($name)
    {
        return @self::find(['name' => $name])->rows[0]->value;
    }
}