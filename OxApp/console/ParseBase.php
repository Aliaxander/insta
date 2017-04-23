<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 20.04.17
 * Time: 19:32
 */

namespace Acme\Console\Command;

use InstagramAPI\Checkpoint;
use Ox\DataBase\DbConfig;
use OxApp\helpers\IgApi;
use OxApp\models\InstBase;
use OxApp\models\Users;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ParseBase
 *
 * @package Acme\Console\Command
 */
class ParseBase extends Command
{
    /**
     * @var IgApi
     */
    public $api;
    
    /**
     * configure
     */
    protected function configure()
    {
        /**
         *
         */
        $this->api = new IgApi();
        $this
            ->setName('test:parse')
            ->setDescription('parse')->addArgument(
                'account',
                InputArgument::OPTIONAL,
                'account'
            );
    }
    
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = $this->api;
        $users = Users::orderBy(["id" => 'desc'])->limit([0 => 1])->find([
            'ban' => 0,
            'requests' => 0,
            'login' => 0,
            'userTask' => 6,
        ]);
        if ($users->count == 0) {
            die('no users');
        } else {
            $user = $users->rows[0];
            print_r($user);
            $requestCou = $user->requests;
            Users::where(['id' => $user->id])->update(['requests' => round($requestCou + 1)]);
            $api->proxy = $user->proxy;
            $api->username = $user->userName;
            $api->accountId = $user->accountId;
            $api->guid = $user->guid;
            $api->csrftoken = $user->csrftoken;
           
            if (!file_exists($user->userName . "-cookies.dat") || $user->logIn === 2) {
                echo "login account:";
                $api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
            }
            
            while ($status = true) {
                $userTest = Users::find(['id' => $user->id, 'ban' => 0]);
                if ($userTest->count === 0) {
                    Users::where(['id' => $user->id])->update(['login' => 0]);
                    die("ban user manual");
                }
                $accRow = \OxApp\models\ParseBase::limit([0 => 1])->find(['status' => 0]);
                if ($accRow->count > 0) {
                    $acc = @preg_replace("/[^0-9]/", '', $accRow->rows[0]->account);
                    if (!empty($acc)) {
                        \OxApp\models\ParseBase::where(['id' => $accRow->rows[0]->id])->update(['status' => 1]);
                        
                        $result = $api->getFeed($acc);
                        if (isset($result['1']['message']) && $result['1']['message'] == 'login_required') {
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
                            unlink($user->userName . "-cookies.dat");
                            $login = $api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
                            $checkPoint = new Checkpoint($user->userName);
                            if (isset($login[1]['checkpoint_url'])) {
                                $result = $checkPoint->request($login[1]['checkpoint_url']);
                                if (preg_match("/Your phone number will be added\b/i", $result[1])) {
                                    Users::where(['id' => $user->id])->update(['ban' => 1]);
                                    die("SMS BAN!");
                                }
                            }
                        }
                        if (!empty($result[1]['items'])) {
                            $this->addToDb($result[1]['items']);
                            
                            if (isset($result[1]['next_max_id'])) {
                                $result2 = $api->getFeed($acc, $result[1]['next_max_id']);
                                $this->addToDb($result[1]['items']);
                                
                                if (isset($result2[1]['next_max_id'])) {
                                    $result3 = $api->getFeed($acc, $result2[1]['next_max_id']);
                                    $this->addToDb($result3[1]['items']);
                                    if (isset($result3[1]['next_max_id'])) {
                                        $result4 = $api->getFeed($acc, $result3[1]['next_max_id']);
                                        $this->addToDb($result4[1]['items']);
                                    }
                                }
                                
                            }
                            
                        }
                    }
                }else{
                    Users::where(['id' => $user->id])->update(['login' => 0]);
                    die("no jobs");
                }
            }
            
        }
        
        return $output->writeln("Complite");
    }
    
    protected function addToDb($rows)
    {
        foreach ($rows as $row) {
            //Parse all comments:
            if ($row['comment_count'] > 0) {
                $this->findComment($row['id']);
            }
        }
    }
    
    protected function findComment($mediaId, $maxId = '')
    {
        $api = $this->api;
        if (!empty($maxId)) {
            $maxId = '?max_id=' . $maxId;
        }
        $comments = $api->request("media/{$mediaId}/comments/" . $maxId);
        foreach ($comments[1]['comments'] as $comment) {
            if (InstBase::find(['account' => $comment['user_id']])->count == 0) {
                InstBase::add(['account' => $comment['user_id']]);
            }
        }
        if (isset($comments[1]['next_max_id'])) {
            $this->findComment($mediaId, $comments[1]['next_max_id']);
        }
    }
}
