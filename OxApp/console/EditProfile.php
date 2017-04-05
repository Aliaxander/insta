<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 15.08.15
 * Time: 15:16
 */

namespace Acme\Console\Command;

use OxApp\helpers\IgApi;
use OxApp\models\Users;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronCommands
 *
 * @package Acme\Console\Command
 */
class EditProfile extends Command
{
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('test:edit')
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
        $api = new IgApi();
        $user = Users::find(['id' => 25])->rows[0];
        $api->proxy = $user->proxy;
        $api->username = $user->userName;
        $api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
        $biography = "";
        $url = "";
        $api->edit($biography, $url, $user->phoneId, $user->firstName, $user->email);
        
        //EditProfile
        return $output->writeln("Complite");
    }
}
