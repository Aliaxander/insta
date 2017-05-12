<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 20.04.17
 * Time: 19:32
 */

namespace Acme\Console\Command;

use OxApp\helpers\IgApi;
use OxApp\models\HashTags;
use OxApp\models\InstBase;
use OxApp\models\ParseBase;
use OxApp\models\Users;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ParsePreBase
 *
 * @package Acme\Console\Command
 */
class PreParseBase extends Command
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
            ->setName('test:preparse')
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
            Users::where(['id' => $user->id])->update(['login' => 1]);
                $api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
          
            
            while ($status = true) {
                $userTest = Users::find(['id' => $user->id, 'ban' => 0]);
                if ($userTest->count === 0) {
                    Users::where(['id' => $user->id])->update(['login' => 0]);
                    die("ban user manual");
                }
                
                $accRow = HashTags::limit([0 => 1])->find(['status' => 0]);
                if ($accRow->count > 0) {
                    HashTags::where(['id' => $accRow->rows[0]->id])->update(['status' => 1]);
                    $result = $api->request('tags/search?q=' . $accRow->rows[0]->tag);
                    $api->request('feed/timeline/?is_prefetch=0&seen_posts=&phone_id=' . $user->phoneId . '&battery_level=' . mt_rand(90,
                            100) . '&timezone_offset=3600&is_pull_to_refresh=0&unseen_posts=&is_charging=' . mt_rand(0,
                            1));
                    if (!empty($result[1]['results'])) {
                        foreach ($result[1]['results'] as $tag) {
                            $result = $api->request('feed/tag/' . $tag['name']);
                            if (!empty($result[1]['ranked_items'])) {
                                foreach ($result[1]['ranked_items'] as $row) {
                                    if ($row['like_count'] >= 50) {
                                        if (ParseBase::find(['account' => $row['user']['pk']])->count === 0) {
                                            ParseBase::add(['account' => $row['user']['pk']]);
                                        }
                                    }
                                }
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
