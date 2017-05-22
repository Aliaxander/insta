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
class ParseBaseIg extends Command
{
    /**
     * @var IgApi
     */
    public $api;
    protected $parentId;
    protected $db;
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
        try {
            $dsn = DbConfig::$dbDriver . ':dbname=' . DbConfig::$dbname . ';host=' . DbConfig::$dbhost;
            $db = new \PDO($dsn, DbConfig::$dbuser, DbConfig::$dbuserpass, [
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ]
            );
        } catch (\PDOException $e) {
            throw new \PDOException($e);
        }
        $this->db = $db;
        
        
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
            Users::where(['id' => $user->id])->update(['login' => 1]);
            if (!file_exists("/home/insta/cookies/" . $user->userName . "-cookies.dat") || $user->logIn === 2) {
                echo "login account:";
                $api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
            }
            
            while ($status = true) {
                $accRow = \OxApp\models\ParseBase::limit([0 => 1])->find(['status' => 0]);
                if ($accRow->count > 0) {
                    $acc = @preg_replace("/[^0-9]/", '', $accRow->rows[0]->account);
                    if (!empty($acc)) {
                        $this->parentId = $acc;
                        \OxApp\models\ParseBase::where(['id' => $accRow->rows[0]->id])->update(['status' => 1]);
                        
                        $result = $api->getFollows($acc);
                        //$result = $api->getFeed($acc);
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
                        }
                        if (!empty($result[1]['users'])) {
                            $this->addToDb($result[1]['users']);
                            if (isset($result[1]['next_max_id'])) {
                                $this->findFlows($acc, $result[1]['next_max_id']);
                            }
                        }
                    }
                } else {
                    Users::where(['id' => $user->id])->update(['login' => 0, 'requests' => 0]);
                    die("no jobs");
                }
            }
        }
        
        return $output->writeln("Complite");
    }
    
    protected function addToDb($rows)
    {
        //  $api = $this->api;
        //        foreach ($rows as $row) {
            //Parse all comments:
            // if ($row['comment_count'] > 0) {
            //    $this->findComment($row['id']);
            //}
        //            if (InstBase::find(['account' => $row['pk']])->count == 0) {
        //                $tst = $api->request("feed/user/" . $row['pk']);
        //                if (@$tst['1']['num_results'] >= 4) {
        //                    $resultTst = $api->request("users/" . $row['pk'] . "/info/");
        //                    $biography = $resultTst[1]['biography'];
        //                    if (empty($resultTst[1]['external_url']) && !preg_match("/(http(s)?:\/\/)?([\\w-]+\\.)+[\\w-]+(\/[\\w- ;,.\/?%&=]*)?/",
        //                            $biography)
        //                    ) {
        //                        InstBase::add(['account' => $row['pk'],'parentId'=> $this->parentId]);
        //                    }
        //                }
        //            }
        //        }
        if(count($rows)> 100000) {
            $file = array_chunk($rows, 100000);
        }else{
            $file[]= $rows;
        }
        foreach ($file as $value) {
            $text = '';
            foreach ($value as $row) {
                $acc = @preg_replace("/[^0-9]/", '', $row['pk']);
                if (!empty($acc)) {
                    $text .= "('$acc','" . $this->parentId . "'),";
                }
            }
            $text = mb_substr($text, 0, -1);
            print_r($this->db->exec("INSERT INTO instBase (`account`,`parentId`) VALUE $text"));
        }
    }
    
    protected function findFlows($mediaId, $maxId = '')
    {
        $api = $this->api;
 
        $follows = $api->getFollows($mediaId, $maxId);
        print_r($follows);
        $this->addToDb($follows[1]['users']);
        
        if (isset($follows[1]['next_max_id'])) {
            $this->findFlows($mediaId, $follows[1]['next_max_id']);
        }
        sleep(rand(1,2));
    }
    
    //    protected function findComment($mediaId, $maxId = '')
    //    {
    //        $api = $this->api;
    //        if (!empty($maxId)) {
    //            $maxId = '?max_id=' . $maxId;
    //        }
    //        $comments = $api->request("media/{$mediaId}/comments/" . $maxId);
    //        foreach ($comments[1]['comments'] as $comment) {
    //            if (InstBase::find(['account' => $comment['user_id']])->count == 0) {
    //                InstBase::add(['account' => $comment['user_id']]);
    //            }
    //        }
    //        if (isset($comments[1]['next_max_id'])) {
    //            $this->findComment($mediaId, $comments[1]['next_max_id']);
    //        }
    //    }
}
