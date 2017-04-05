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
class Likes extends Command
{
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('test:likes')
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
        $user = Users::find(['id' => 12])->rows[0];
        
        $api->proxy = $user->proxy;
        $api->username = $user->userName;
        $api->accountId = $user->accountId;
        $api->guid = $user->guid;
        $api->csrftoken = $user->csrftoken;
        
        $api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
        
        $result = $api->getFeed('3639014581');
        if (isset($result[1]['items'])) {
            $rows = $result[1]['items'];
            $like1 = $result[1]['items'][rand(0, count($rows) - 1)]['id'];
            $like2 = $result[1]['items'][rand(0, count($rows) - 1)]['id'];
            print_r($api->like($like1));
            sleep(rand(10, 20));
            if (rand(0, 1) == 1) {
                print_r($api->like($like2));
            }
        }
        sleep(rand(10, 20));
        print_r($api->follow('3639014581'));
        
        return $output->writeln("Complite");
    }
}
