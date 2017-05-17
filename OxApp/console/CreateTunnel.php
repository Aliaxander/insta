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
use OxApp\helpers\TunnelBroker;
use OxApp\models\Domains;
use OxApp\models\HashTags;
use OxApp\models\ProfileGenerate;
use OxApp\models\SystemSettings;
use OxApp\models\TechAccount;
use OxApp\models\Tunnels;
use OxApp\models\Users;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class HashTagParser
 *
 * @package Acme\Console\Command
 */
class CreateTunnel extends Command
{
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('create:tunnel')
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
        $tunnels = Tunnels::find(['status' => 0]);
        if ($tunnels->count > 0) {
            $tunnel = $tunnels->rows[0];
            Tunnels::where(['id' => $tunnel->id])->update(['status' => 1]);
            
            $tunelLogin = TechAccount::find(['count/<' => 3]);
            if ($tunelLogin->count === 0) {
                $tunelLogin = TechAccount::find(['dateUpdate/<=' => '//now()-interval 1 day//']);
                if ($tunelLogin->count === 0) {
                    die('account limit');
                } else {
                    TechAccount::where(['id' => $tunelLogin->rows[0]->id])->update(['count' => 0]);
                    $count = 0;
                }
            }
            
            $tunelLogin = $tunelLogin->rows[0];
            if (!isset($count)) {
                $count = $tunelLogin->count;
            }
            $tunnelClass = new TunnelBroker();
            print_r($tunnelClass->login($tunelLogin->name, $tunelLogin->password));
            $result = $tunnelClass->createNewTunnel($tunnel->serverIp);
            if ($result['status'] === false) {
                Tunnels::where(['id' => $tunnel->id])->update([
                    'status' => 0
                ]);
                TechAccount::where(['id' => $tunelLogin->id])->update(['count' => 3]);
            } elseif ($result['v6route'] !== '[1500]' && $result['v6route'] !== '') {
                Tunnels::where(['id' => $tunnel->id])->update([
                    'status' => 2,
                    'remoteIp' => $result['remoteIp'],
                    'v6route' => $result['v6route'],
                    '48sub' => $result['48sub'],
                    'tunnelId' => $result['tunnelId'],
                    'tunnelAccountId' => $tunelLogin->id
                ]);
                
                TechAccount::where(['id' => $tunelLogin->id])->update(['count' => $count + 1]);
            }
        }
        
        return $output->writeln("Complite");
    }
}
