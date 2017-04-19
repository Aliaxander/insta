<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 15.08.15
 * Time: 15:16
 */

namespace Acme\Console\Command;

use OxApp\helpers\FreenomReg;
use OxApp\helpers\IgApi;
use OxApp\models\Domains;
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
        $status = true;
        while ($status = true) {
            $users = Users::orderBy(["id" => 'desc'])->limit([0 => 1])->find([
                'ban' => 0,
                'userTask' => 2,
                'login' => 0
            ]);
            if ($users->count == 1) {
                $user = $users->rows[0];
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
                //unlink($photo);
                
                $profiles = ProfileGenerate::limit([0 => 1])->find(['status' => 0])->rows[0];//groupBy('description')->
                ProfileGenerate::where(['id' => $profiles->id])->update(['status' => 1]);
                //            $word = [$user->userName, $user->firstName, mt_rand(10000, 99999)];
                //            $word = $word[mt_rand(0, 2)];
                //            $word = str_replace([' ', '.'], '', $word);
                $biography = @$profiles->description;
                //            $url = mb_strtolower(str_replace('%username%', $word, $profiles->url));
                //            //
                //            //
                //                $result = FreenomReg::freedomReg($profiles->url);
                //                $p = xml_parser_create();
                //                xml_parse_into_struct($p, $result[1], $vals, $index);
                //                xml_parser_free($p);
                //                $domain = mb_strtolower($vals[2]['value']);
                //                if (rand(0, 1) == 1) {
                //                    $domain = "http://" . $domain;
                //                }
                $domain = Domains::limit([0 => 1])->find(['status' => 0]);
                if ($domain->count == 1) {
                    Domains::where(['id' => $domain->rows[0]->id])->update(['status' => 1]);
                    $profileResult = '';
                    $i = 0;
                    while ($profileResult === '') {
                        //$biography
                        $profile = $api->edit($biography, $domain->rows[0]->domain, $user->phoneId, $user->firstName,
                            $user->email);
                        $profileResult = $profile[1];
                        if ($i == 3) {
                            $profileResult = false;
                        }
                        $i++;
                    }
                    
                    print_r($profile);
                    
                    //            $dir = scandir('/home/photos2');
                    //            unset($dir[array_search('.', $dir)]);
                    //            unset($dir[array_search('..', $dir)]);
                    //            $dir = array_values($dir);
                    //            $photo = '/home/photos2/' . $dir[rand(0, count($dir) - 1)];
                    //
                    //            $api->uploadPhoto($photo);
                    //            unlink($photo);
                    //            sleep(rand(3, 10));
                    
                    Users::where(['id' => $user->id])->update([
                        'login' => 1,
                        'biography' => $biography,
                        'url' => $domain->rows[0]->domain,
                        'photo' => $profile[1]['user']['profile_pic_url']
                    ]);
                } else {
                    die('no domains');
                }
            } else {
                die('no tasks');
            }
        }
        
        //EditProfile
        return $output->writeln("Complite");
    }
}
