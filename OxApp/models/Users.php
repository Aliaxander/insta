<?php
/**
 * Created by OxGroup.
 * User: ���������
 * Date: 08.09.2015
 * Time: 16:02
 */

namespace OxApp\models;

use Ox\DataBase\AbstractModel;
use Faker\Factory;

/**
 * Class Users
 *
 * @package OxApp\models
 */
class Users extends AbstractModel
{
    protected static $from = 'users';

    /**
     * @param $count
     */
    public static function addFakeData($count)
    {
        $faker = Factory::create('ru_RU');
        $arrStatus[1] = 'webmaster';
        $arrStatus[0] = 'advert';
        $arrStatus[2] = 'admin';

        for ($i = 1; $i <= $count; $i++) {
            self::add(array(
                'name' => $faker->name,
                'login' => $faker->userName,
                'skype' => $faker->userName,
                'email' => $faker->email,
                'address' => $faker->streetAddress,
                'zip' => $faker->postcode,
                'phone' => $faker->phoneNumber,
                'adminComment' => '',
                'advertiserAddress' => $faker->address,
                'badcall' => rand(0, 1),
                'paySettings' => $faker->numerify('R############'),
                'group' => rand(1, 6),
                'topName' => $faker->userName,
                'ref' => rand(0, rand(0, $count)),
                'status' => $arrStatus[rand(0, rand(0, rand(0, 2)))],
                'password' => $faker->sha1
            ));
        }
    }
}
