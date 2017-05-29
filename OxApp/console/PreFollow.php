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
use OxApp\models\PopularAccounts;
use OxApp\models\SystemSettings;
use OxApp\models\Users;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PreFollow
 *
 * @package Acme\Console\Command
 */
class PreFollow extends Command
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName('prefollow:start')->setDescription('Cron jobs');
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
            'ban' => 0,
            'requests' => 0,
            'userTask' => 10
        ]);
        echo 2;
        if ($users->count == 0) {
            die('no job');
        } else {
            $user = $users->rows[0];
            print_r($user);
            $requestCou = $user->requests;
            Users::where(['id' => $user->id])->update(['requests' => round($requestCou + 1)]);
            echo 3;
            $api->proxy = $user->proxy;
            $api->username = $user->userName;
            $api->accountId = $user->accountId;
            $api->guid = $user->guid;
            $api->csrftoken = $user->csrftoken;
            $login = $api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
            Checkpoint::checkPoint($login, $user);
            if (!file_exists("/home/insta/cookies/" . $user->userName . "-cookies.dat") || $user->logIn === 2) {
                echo "login account:";
                $login = $api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
                Checkpoint::checkPoint($login, $user);
            } else {
                $api->request('news/inbox/?limited_activity=true&show_su=true');
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
            $accounts = PopularAccounts::find();
            
            $array = $accounts->rows;
            
            $randUsers = mt_rand(8, 30);
            for ($i = 0; $i < $randUsers; $i++) {
                $rand = mt_rand(0, count($array));
                $randUser = $array[$rand]->account;
                echo "\nSet account: $randUser";
                $result = $api->getFeed($randUser->accountId);
                print_r($result);
                sleep(rand(60, 500));
                if (rand(0, 1) == 1) {
                    print_r($api->follow($randUser->accountId));
                }
                unset($array[$rand]);
                sleep(rand(20, 40));
            }
            Users::where(['id' => $user->id])->update(['requests' => 0, 'userTask' => 7]);
        }
        
        return $output->writeln("Complite");
    }
    
}