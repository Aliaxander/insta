<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 15.08.15
 * Time: 15:16
 */

namespace Acme\Console\Command;

use OxApp\helpers\IgApi;
use OxApp\models\InstBase;
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
        $this->setName('test:likes')->setDescription('Cron jobs');
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
        $requestCou = 0;
        $likeCou = 0;
        $followCou = 0;
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
                    $requestCou++;
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
            
            $startMinRand = rand(10, 15);//10
            $startMaxRand = rand(10, 20);//15
            $stopMinRand = rand(20, 30);//20
            $stopMaxRand = rand(40, 60);//40
            while (0 < 1) {
                $accRow = InstBase::find(['status' => 0]);
                $acc = $accRow->rows[0]->account;
                InstBase::where(['id' => $accRow->rows[0]->id])->update(['status' => 1]);
                echo "Set acc $acc:\n";
                if (rand(0, 20) == 10) {
                    $api->getRecentActivityAll();
                }
                $result = $api->getFeed($acc);
                $requestCou += 3;
                if (rand(0, 30) == 10) {
                    $api->getRecentActivityAll();
                }
                if (mt_rand(0, 1) == 1) {
                    if (isset($result[1]['items'])) {
                        $rows = $result[1]['items'];
                        $like1 = @$result[1]['items'][mt_rand(0, count($rows) - 1)]['id'];
                        $like2 = @$result[1]['items'][mt_rand(0, count($rows) - 1)]['id'];
                        sleep(rand($startMinRand, $stopMinRand));
                        if (mt_rand(0, 10) === 9) {
                            print_r($api->follow($acc));
                            $followCou++;
                            $requestCou++;
                        }
                        if (mt_rand(0, 9) == 1) {
                            sleep(mt_rand($startMaxRand, $stopMaxRand));
                            if ($like1) {
                                print_r($api->like($like1));
                                $api->getFeed($acc);
                                $likeCou++;
                                $requestCou++;
                            }
                            sleep(mt_rand($startMaxRand, $stopMaxRand));
                            
                            if (mt_rand(0, 1) == 1 && $like2) {
                                print_r($api->like($like2));
                                $likeCou++;
                                $requestCou++;
                            }
                        }
                    }
                    echo "Requests: $requestCou | Likes: $likeCou | Follows: $followCou\n";
                    sleep(rand($startMinRand, $stopMinRand));
                }
                sleep(rand($startMinRand, $stopMaxRand));
            }
        }
        
        return $output->writeln("Complite");
    }
}
