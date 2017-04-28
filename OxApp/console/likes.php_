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
        $users = Users::orderBy(["id" => 'desc'])->limit([0 => 1])->find([
            'login/in' => [1, 2],
            'ban' => 0,
            'requests' => 0,
            'userTask' => 3
        ]);
        if ($users->count == 0) {
            die('no job');
        } else {
            $user = $users->rows[0];
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
            if (!file_exists("/home/insta/cookies/" . $user->userName . "-cookies.dat") || $user->logIn === 2) {
                echo "login account:";
                $api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
            }
            
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
                        $checkPoint = new Checkpoint($user->userName);
                        $checkPoint->proxy = $user->proxy;
                        $checkPoint->accountId = $user->accountId;
                        $checkPoint->request($sync[1]['checkpoint_url']);
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
            //Follow my accouns:
            $usersFollow = Users::orderBy(["id" => 'desc'])->find([
                'login/in' => [1, 2],
                'ban' => 0,
                'userTask' => 3,
                'accountId/>' => 0,
                'id/!=' => $user->id,
            ]);
            if ($usersFollow->count > 0) {
                $array = $usersFollow->rows;
                if ($usersFollow->count < 8) {
                    $count = $usersFollow->count;
                } else {
                    $count = 8;
                }
                $randUsers = mt_rand(1, $count);
                for ($i = 0; $i < $randUsers; $i++) {
                    $rand = mt_rand(0, count($array));
                    $randUser = $array[$rand];
                    sleep(rand(1, 3));
                    print_r($api->follow($randUser->accountId));
                    $followCou++;
                    $requestCou += 2;
                    unset($array[$rand]);
                    sleep(rand(10, 20));
                }
            }
            
            $status = true;
            while ($status = true) {
                $userTest = Users::find(['id' => $user->id, 'ban' => 0]);
                if ($userTest->count === 0) {
                    die();
                }
                $accRow = InstBase::limit([0 => 1])->find(['status' => 0]);
                $acc = @preg_replace("/[^0-9]/", '', $accRow->rows[0]->account);
                if (!empty($acc)) {
                    InstBase::where(['id' => $accRow->rows[0]->id])->update(['status' => 1]);
                    
                    echo "Set acc $acc:\n";
                    if (rand(0, 30) == 10) {
                        $api->getRecentActivityAll();
                    }
                    $result = $api->getFeed($acc);
                    if (isset($result['1']['message']) && $result['1']['message'] === 'login_required') {
                        echo "login_required";
                        $login = $api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
                        $checkPoint = new Checkpoint($user->userName);
                        if (isset($login[1]['checkpoint_url'])) {
                            $result = $checkPoint->request($login[1]['checkpoint_url']);
                            if (preg_match("/Your phone number will be added\b/i", $result[1])) {
                                Users::where(['id' => $user->id])->update(['ban' => 3]);
                                die("SMS BAN!");
                            }
                        }
                    } elseif (isset($result['1']['message']) && $result['1']['message'] === 'checkpoint_required') {
                        echo "\nLogout user account\n";
                        unlink("/home/insta/cookies/" . $user->userName . "-cookies.dat");
                        $login = $api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
                        $checkPoint = new Checkpoint($user->userName);
                        if (isset($login[1]['checkpoint_url'])) {
                            $result = $checkPoint->request($login[1]['checkpoint_url']);
                            if (preg_match("/Your phone number will be added\b/i", $result[1])) {
                                Users::where(['id' => $user->id])->update(['ban' => 1]);
                                die("SMS BAN!");
                            }
                        }
                        //                        $checkPoint = new Checkpoint($user->userName);
                        //                        $checkPoint->proxy = $user->proxy;
                        //                        $checkPoint->accountId = $user->accountId;
                        //                        $checkPoint->request('https://i.instagram.com/challenge/?activity_module=all');
                        
                        //                        $checkPoint->request('https://i.instagram.com/checkpoint/dismiss');
                        //                        $api->fetchHeadersSingUp();
                        //
                        //                        $token = $checkPoint->doCheckpoint();
                        //                        echo "\n\nCode you have received via mail: ";
                        //                        //$code = trim(fgets(STDIN));
                        //                       // $checkPoint->checkpointThird($code, $token);
                        //                        echo "\n\nDone";
                        //                         Users::where(['id' => $user->id])->update(['ban' => 1]);
                        die();
                    } elseif (isset($result['1']['message']) && $result['1']['message'] === 'Not authorized to view user') {
                        //                                                sleep(rand(10, 20));
                        //                                                print_r($api->follow($acc));
                        //                                                InstBase::where(['id' => $accRow->rows[0]->id])->update(['follow' => round($accRow->rows[0]->follow + 1)]);
                        //                                                $followCou++;
                        //                                                $requestCou += 2;
                    } elseif (!empty($result[1]['items'])) {
                        sleep(rand(0, 1));
                        $rows = $result[1]['items'];
                        $like1 = @$result[1]['items'][mt_rand(0, count($rows) - 1)]['id'];
                        if ($like1) {
                            InstBase::where(['id' => $accRow->rows[0]->id])->update(['likes' => round($accRow->rows[0]->likes + 1)]);
                            $createResult = '';
                            $i = 0;
                            while ($createResult === '') {
                                $likes = $api->like($like1);
                                $createResult = $likes[1];
                                if ($i === 3) {
                                    $createResult = false;
                                }
                                $i++;
                            }
                            print_r($likes);
                            $feed = $api->getFeed($acc);
                            if (@$feed[1]['message'] === 'checkpoint_required') {
                                Users::where(['id' => $user->id])->update(['ban' => 1]);
                                die("Account banned");
                            }
                            $likeCou++;
                            $requestCou += 4;
                            sleep(rand(10, 25));
                        }
                    } else {
                        $result = $api->getRecentActivityAll();
                        if (@$result[1]['message'] === 'checkpoint_required') {
                            Users::where(['id' => $user->id])->update(['ban' => 1]);
                            die("Account banned");
                        }
                    }
                    if (rand(0, 40) == 10) {
                        $api->getRecentActivityAll();
                    }
                    if ($requestCou !== 0) {
                        Users::where(['id' => $user->id])->update([
                            'requests' => $requestCou,
                            'follows' => $followCou,
                            'likes' => $likeCou
                        ]);
                    }
                    $requestCou++;
                    echo "Requests: $requestCou | Likes: $likeCou | Follows: $followCou\n";
                    $folLikSum = round($likeCou + $followCou);
                    
                    $resultLikesForTimeout = $folLikSum / $hour;
                    if ($resultLikesForTimeout > rand(450, 500) && $resultLikesForTimeout < 600) {
                        $hour += 1;
                        Users::where(['id' => $user->id])->update(['hour' => $hour]);
                        echo "Sleep";
                        sleep(rand(6000, 15000));
                    }
                    if ($hour >= 4 && $likeCou > 700) {
                        sleep(rand(500000, 600000));
                    }
                }
            }
        }
        
        return $output->writeln("Complite");
    }
}