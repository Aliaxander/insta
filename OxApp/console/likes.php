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
        $users = Users::limit([0 => 1])->find(['login' => 1, 'ban' => 0]);
        foreach ($users->rows as $user) {
            $api->proxy = $user->proxy;
            $api->username = $user->userName;
            $api->accountId = $user->accountId;
            $api->guid = $user->guid;
            $api->csrftoken = $user->csrftoken;
            if (empty($user->csrftoken)) {
                $tokenResult = '';
                $i = 0;
                while ($tokenResult === '') {
                    $sync = $api->sync();
                    print_r($sync);
            
                    if (preg_match('#Set-Cookie: csrftoken=([^;]+)#', $sync[0], $token)) {
                        $tokenResult = $token[1];
                    }
                    if ($i == 10) {
                        $tokenResult = false;
                    }
                    if ($sync[1]['message'] === 'checkpoint_required') {
                        Users::where(['id' => $user->id])->update(['ban' => 1]);
                        die("Account banned");
                    }
            
                    $i++;
                }
                if ($tokenResult == false || $tokenResult == '') {
                    die('empty token');
                }
                $api->csrftoken = $tokenResult;
                Users::where(['id' => $user->id])->update(['csrftoken' => $tokenResult]);
            }
            //$api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
    
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
        }
        return $output->writeln("Complite");
    }
}
