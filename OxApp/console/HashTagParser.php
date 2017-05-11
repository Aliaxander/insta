<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 15.08.15
 * Time: 15:16
 */

namespace Acme\Console\Command;

use Faker\Factory;
use OxApp\helpers\FreenomReg;
use OxApp\helpers\IgApi;
use OxApp\helpers\Resize;
use OxApp\models\Domains;
use OxApp\models\HashTags;
use OxApp\models\ProfileGenerate;
use OxApp\models\SystemSettings;
use OxApp\models\Users;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class HashTagParser
 *
 * @package Acme\Console\Command
 */
class HashTagParser extends Command
{
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('parse:hashtag')
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
        $api = new IgApi();
        $user = Users::find(['id' => 14159])->rows[0];
        $api->proxy = $user->proxy;
        $api->username = $user->userName;
        $api->accountId = $user->accountId;
        $api->guid = $user->guid;
        $api->csrftoken = $user->csrftoken;
        if (!file_exists("/home/insta/cookies/" . $user->userName . "-cookies.dat") || $user->logIn === 2) {
            echo "login account:";
            $api->login($user->guid, $user->phoneId, $user->deviceId, $user->password);
        }
        
        $accRow = HashTags::find();
        if ($accRow->count > 0) {
            
            foreach ($accRow->rows as $tag) {
                $results = $api->request('tags/search?q=' . $tag->tag);
                foreach ($results[1]['results'] as $result) {
                    print_r($result);
                    if (HashTags::find(['tag' => $result['name']])->count === 0) {
                        HashTags::add(['tag' => $result['name']]);
                        echo 'Added ' . $result['name'] . "\n";
                    }
                }
            }
        }
        
        //EditProfile
        return $output->writeln("Complite");
    }
}
