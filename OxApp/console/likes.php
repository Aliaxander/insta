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
use OxApp\models\SystemSettings;
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
        echo 1;
        $api = new IgApi();
        $users = Users::orderBy(["id" => 'desc'])->limit([0 => 1])->find([
            'login/in' => [1, 2],
            'ban' => 0,
            'requests' => 0,
            'userTask' => 3
        ]);
        echo 2;
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
            echo 3;
            $api->proxy = $user->proxy;
            $api->username = $user->userName;
            $api->accountId = $user->accountId;
            $api->guid = $user->guid;
            $api->csrftoken = $user->csrftoken;
            if (!file_exists("/home/insta/cookies/" . $user->userName . "-cookies.dat") || $user->logIn === 2) {
                echo "login account:";
                $login = $api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
                Checkpoint::checkPoint($login, $user);
            }else{
                $api->request('news/inbox/?limited_activity=true&show_su=true');
            }
            $timeOutMin = SystemSettings::get('timeOutMin');
            $timeOutMax = SystemSettings::get('timeOutMax');
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
                    //                    if ($sync[1]['message'] === 'checkpoint_required') {
                    //                        $checkPoint = new Checkpoint($user->userName);
                    //                        $checkPoint->proxy = $user->proxy;
                    //                        $checkPoint->accountId = $user->accountId;
                    //                        $checkPoint->request($sync[1]['checkpoint_url']);
                    //                        Users::where(['id' => $user->id])->update(['ban' => 1]);
                    //                        die("Account banned");
                    //                    }
                    
                    Checkpoint::checkPoint($sync, $user);
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
            if (SystemSettings::get('followBot') == 1 and rand(0, 1) === 1) {
                $usersFollow = Users::orderBy(["id" => 'desc'])->find([
                    'ban' => 0,
                    'userTask' => 3,
                    'accountId/>' => 0,
                    'id/!=' => $user->id,
                ]);
                if ($usersFollow->count > 0) {
                    $array = $usersFollow->rows;
                    if ($usersFollow->count < 8) {
                        $count = $usersFollow->count - 1;
                    } else {
                        $count = 8;
                    }
                    $randUsers = mt_rand(1, $count);
                    for ($i = 0; $i < $randUsers; $i++) {
                        $rand = mt_rand(0, count($array));
                        $randUser = $array[$rand];
                        sleep(rand(1, 3));
                        $result = $api->getFeed($randUser->accountId);
                        print_r($result);
                        print_r($api->follow($randUser->accountId));
                        $followCou++;
                        $requestCou += 2;
                        unset($array[$rand]);
                        sleep(rand(10, 20));
                    }
                }
            }
            
            $api->request('feed/timeline/?is_prefetch=0&seen_posts=&phone_id=' . $user->phoneId . '&battery_level=' . mt_rand(90,
                    100) . '&timezone_offset=3600&is_pull_to_refresh=0&unseen_posts=&is_charging=' . mt_rand(0,
                    1));
            
            $likesForAccountMin = SystemSettings::get('likesForAccountMin');
            $likesForAccountMax = SystemSettings::get('likesForAccountMax');
            $massFollow = SystemSettings::get('massFollow');
            $badRequest = 0;
            $status = true;
            $allWhile = 0;
            while ($status = true) {
                $allWhile++;
                if (isset($parentId)) {
                    $accRow = InstBase::orderBy(['id' => 'desc'])->limit([0 => 1])->find([
                        'status' => 0,
                        'parentId' => $parentId
                    ]);
                } else {
                    $accRow = InstBase::orderBy(['id' => 'desc'])->limit([0 => 1])->find(['status' => 0]);
                    $parentId = $accRow->rows[0]->parentId;
                    Users::where(['id' => $user->id])->update(['parentAccs' => $user->parentAccs . ',' . $parentId]);
                }
                if ($accRow->count == 0) {
                    Users::where(['id' => $user->id])->update(['requests' => 0]);
                    die('No accounts');
                }
                $acc = @preg_replace("/[^0-9]/", '', $accRow->rows[0]->account);
                if (!empty($acc)) {
                    echo 6;
                    InstBase::where(['id' => $accRow->rows[0]->id])->update(['status' => 1]);
                    echo 7;
                    echo "Set acc $acc:\n";
                    if (rand(0, 30) == 10) {
                        $api->getRecentActivityAll();
                    }
                    $result = $api->getFeed($acc);
                    
                    Checkpoint::checkPoint($result, $user);
                    if (empty($result[1])) {
                        $badRequest++;
                    }
                    if (isset($result['1']['message']) && $result['1']['message'] === 'login_required') {
                        echo "login_required";
                        $login = $api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
                        Checkpoint::checkPoint($login, $user);
                    } elseif (isset($result['1']['message']) && $result['1']['message'] === 'checkpoint_required') {
                        echo "\nLogout user account\n";
                        unlink("/home/insta/cookies/" . $user->userName . "-cookies.dat");
                        $login = $api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
                        Checkpoint::checkPoint($login, $user);
                    } elseif (isset($result['1']['message']) && $result['1']['message'] === 'Not authorized to view user' && mt_rand(0,
                            1) == 1 && $massFollow == 1
                    ) {
                        sleep(rand($timeOutMin, $timeOutMax));
                        print_r($api->follow($acc));
                        InstBase::where(['id' => $accRow->rows[0]->id])->update(['follow' => round($accRow->rows[0]->follow + 1)]);
                        $followCou++;
                        $requestCou += 2;
                    } elseif (!empty($result[1]['items'])) {
                        sleep(rand(0, 1));
                        $randLikes = mt_rand($likesForAccountMin, $likesForAccountMax);
                        for ($i = 0; $i <= $randLikes; $i++) {
                            $rows = $result[1]['items'];
                            $rowMedia = @$result[1]['items'][mt_rand(0, count($rows) - 1)];
                            $like1 = $rowMedia['id'];
                            $userNameLike = $rowMedia['user']['username'];
                            $mediaType = $rowMedia['media_type'];
                            if ($like1) {
                                InstBase::where(['id' => $accRow->rows[0]->id])->update(['likes' => round($accRow->rows[0]->likes + 1)]);
                                
                                $likes = $api->like($like1, $acc, $userNameLike, $mediaType);
                                $likesResult = $likes[0];
                                if (empty($likesResult)) {
                                    $badRequest++;
                                }
                                //sleep(mt_rand(1, 2));
                                //$likes = $api->oldLike($like1);
                                print_r($likes);
                                $likeCou++;
                                $requestCou += 1;
                                sleep(mt_rand($timeOutMin, $timeOutMax));
                            }
                            $feed = $api->getFeed($acc);
                            Checkpoint::checkPoint($feed, $user);
                            $requestCou += 4;
                        }
                    } else {
                        $result = $api->getRecentActivityAll();
                        Checkpoint::checkPoint($result, $user);
                    }
                    if (mt_rand(0, 40) == 10) {
                        $api->getRecentActivityAll();
                    }
                    
                    if ($requestCou > 0 && $allWhile > 10) {
                        $allWhile = 0;
                        Users::where(['id' => $user->id])->update([
                            'requests' => $requestCou,
                            'follows' => $followCou,
                            'likes' => $likeCou
                        ]);
                        $userTest = Users::find(['id' => $user->id, 'ban' => 0]);
                        if ($userTest->count === 0) {
                            die();
                        }
                    }
                    $requestCou++;
                    echo "Requests: $requestCou | Likes: $likeCou | Follows: $followCou\n";
                    $folLikSum = round($likeCou + $followCou);
                    
                    $resultLikesForTimeout = $folLikSum / $hour;
                    
                    if ($resultLikesForTimeout > mt_rand(520, 700)) {
                        $hour += 1;
                        Users::where(['id' => $user->id])->update(['userTask' => 8, 'hour' => $hour]);
                        echo "Sleep";
                        //sleep(mt_rand(30000, 50000));
                        die('Complite');
                    }
                }
                if ($badRequest === 100) {
                    Users::where(['id' => $user->id])->update(['requests' => 0]);
                    die('Many bad requests');
                }
            }
        }
        
        return $output->writeln("Complite");
    }
    
}