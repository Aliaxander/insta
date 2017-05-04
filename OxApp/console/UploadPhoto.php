<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 15.08.15
 * Time: 15:16
 */

namespace Acme\Console\Command;

use OxApp\helpers\Resize;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronCommands
 *
 * @package Acme\Console\Command
 */
class UploadPhoto extends Command
{
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('upload:photo')
            ->setDescription('Cron jobs');
    }
    
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        require(__DIR__ . "/../../config.php");
        $status = true;
        
//        $users = Users::find(['id' => 9894]);
//        $user = @$users->rows[0];
//        $proxy = explode(":", @$user->proxy);
//        if (empty($user)) {
//            die('No users');
//        }
//        $api = new IgApi();
//        $api->proxy = $user->proxy;
//        $api->username = $user->userName;
//        $api->guid = $user->guid;
//        $api->csrftoken = $user->csrftoken;
//        $loginResult = '';
//        $i = 0;
//        while ($loginResult === '') {
//            $login = $api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
//            $loginResult = @$login[1];
//            if ($i === 5) {
//                $loginResult = false;
//            }
//            $i++;
//        }
//        //
//
        
     
        
        //EditProfile
        return $output->writeln("Complite");
    }
    
   public static function randomColor()
    {
        $result = array('rgb' => '', 'hex' => '');
        foreach (array('r', 'b', 'g') as $col) {
            $rand = mt_rand(0, 255);
            $result['rgb'][$col] = $rand;
            $dechex = dechex($rand);
            if (strlen($dechex) < 2) {
                $dechex = '0' . $dechex;
            }
            $result['hex'] .= $dechex;
        }
        
        return $result;
    }
}
