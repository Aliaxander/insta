<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 15.08.15
 * Time: 15:16
 */

namespace Acme\Console\Command;

use InstagramAPI\Checkpoint;
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
        $api = new IgApi();
        $users = Users::orderBy(["id" => 'desc'])->limit([0 => 1])->find(['login' => 1, 'ban' => 0, 'requests' => 0]);
        if ($users->rows == 0) {
            die('no job');
        }
        foreach ($users->rows as $user) {
            print_r($user);
            $requestCou = $user->requests;
            $likeCou = $user->likes;
            $hour = $user->hour + 1;
            $day = $user->day + 1;
            $followCou = $user->follows;
            Users::where(['id' => $user->id])->update(['requests' => round($requestCou + 1)]);
            $api->proxy = $user->proxy;
            $api->username = $user->userName;
            $api->accountId = $user->accountId;
            $api->guid = $user->guid;
            $api->csrftoken = $user->csrftoken;
            //$api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
            
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
            
            $status = true;
            while ($status = true) {
                $accRow = InstBase::limit([0 => 1])->find(['status' => 0]);
                $acc = @preg_replace("/[^0-9]/", '', $accRow->rows[0]->account);
                if (!empty($acc)) {
                    InstBase::where(['id' => $accRow->rows[0]->id])->update(['status' => 1]);
                    
                    echo "Set acc $acc:\n";
                    if (rand(0, 50) == 10) {
                        $api->getRecentActivityAll();
                    }
                    $result = $api->getFeed($acc);
                    if (isset($result['1']['message']) && $result['1']['message'] === 'login_required') {
                        $api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
                    } elseif (isset($result['1']['message']) && $result['1']['message'] === 'checkpoint_required') {
                        
                        $api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
                        
                        $checkPoint = new Checkpoint($user->userName);
//                        $checkPoint->proxy = $user->proxy;
//                        $token = $checkPoint->doCheckpoint();
//                        echo "\n\nCode you have received via mail: ";
//                        //$code = trim(fgets(STDIN));
//                       // $checkPoint->checkpointThird($code, $token);
//                        echo "\n\nDone";
//                         Users::where(['id' => $user->id])->update(['ban' => 1]);
                         die();
                    } elseif (isset($result['1']['message']) && $result['1']['message'] === 'Not authorized to view user') {
                        //                        sleep(rand(10, 20));
                        //                        print_r($api->follow($acc));
                        //                        InstBase::where(['id' => $accRow->rows[0]->id])->update(['follow' => round($accRow->rows[0]->follow + 1)]);
                        //
                        //                        $followCou++;
                        //                        $requestCou += 2;
                    } elseif (isset($result[1]['items'])) {
                        sleep(rand(0, 1));
                        $rows = $result[1]['items'];
                        $like1 = @$result[1]['items'][mt_rand(0, count($rows) - 1)]['id'];
                        if ($like1) {
                            InstBase::where(['id' => $accRow->rows[0]->id])->update(['likes' => round($accRow->rows[0]->likes + 1)]);
                            print_r($api->like($like1));
                            sleep(rand(0, 1));
                            $feed = $api->getFeed($acc);
                            if (@$feed[1]['message'] === 'checkpoint_required') {
                                Users::where(['id' => $user->id])->update(['ban' => 1]);
                                die("Account banned");
                            }
                            $likeCou++;
                            $requestCou += 4;
                        }
                    }
                    if (rand(0, 50) == 10) {
                        $api->getRecentActivityAll();
                    }
                    Users::where(['id' => $user->id])->update([
                       // 'requests' => $requestCou,
                        'follows' => $followCou,
                        'likes' => $likeCou
                    ]);
                    echo "Requests: $requestCou | Likes: $likeCou | Follows: $followCou\n";
                    $folLikSum = round($likeCou + $followCou);
                    
                    $resultLikesForTimeout = $folLikSum / $hour;
                    if ($resultLikesForTimeout > rand(400, 500) && $resultLikesForTimeout < 600) {
                        $hour += 1;
                        Users::where(['id' => $user->id])->update(['hour' => $hour]);
                        echo "Sleep";
                        sleep(rand(500, 1500));
                    }
                    if ($hour >= 4 && $likeCou > 2100) {
                        sleep(rand(70000, 87000));
                    }
                    sleep(rand(1, 5));
                }
            }
        }
        
        return $output->writeln("Complite");
    }
}