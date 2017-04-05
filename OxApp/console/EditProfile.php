<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 15.08.15
 * Time: 15:16
 */

namespace Acme\Console\Command;

use OxApp\helpers\IgApi;
use OxApp\models\ProfileGenerate;
use OxApp\models\Users;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronCommands
 *
 * @package Acme\Console\Command
 */
class EditProfile extends Command
{
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('test:edit')
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
        
        $users = Users::limit([0 => 25])->find(['login' => 0, 'ban' => 0]);
        foreach ($users->rows as $user) {
            $dir = scandir('/home/photos');
            unset($dir[array_search('.', $dir)]);
            unset($dir[array_search('..', $dir)]);
            $dir = array_values($dir);
            $photo = '/home/photos/' . $dir[rand(0, count($dir) - 1)];
            
            $api = new IgApi();
            $api->proxy = $user->proxy;
            $api->username = $user->userName;
            $api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
            
            //SetPhoto:
            $api->changeProfilePicture($photo);
            unlink($photo);
            sleep(rand(3, 10));
            
            $profiles = ProfileGenerate::limit([0 => 1])->find(['status' => 0])->rows[0];
            ProfileGenerate::where(['id' => $profiles->id])->update(['status' => 1]);
            $word = [$user->userName, $user->firstName, mt_rand(10000, 99999)];
            $word = $word[mt_rand(0, 2)];
            $word = str_replace([' ', '.'], '', $word);
            $biography = $profiles->description;
            $url = mb_strtolower(str_replace('%username%', $word, $profiles->url));
            $profile = $api->edit($biography, $url, $user->phoneId, $user->firstName, $user->email);
            print_r($profile);
            
            Users::where(['id' => $user->id])->update([
                'login' => 1,
                'biography' => $biography,
                'url' => $url,
                'photo' => $profile[1]['user']['profile_pic_url']
            ]);
    
            sleep(rand(10, 20));
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
        
        //EditProfile
        return $output->writeln("Complite");
    }
}
