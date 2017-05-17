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
use OxApp\models\Proxy;
use OxApp\models\Servers;
use OxApp\models\SystemSettings;
use OxApp\models\TechAccount;
use OxApp\models\Tunnels;
use OxApp\models\Users;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RestartTunelControl
 *
 * @package Acme\Console\Command
 */
class RestartTunnelControl extends Command
{
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('tunnel:control')
            ->setDescription('Cron jobs');
    }
    
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        require(__DIR__ . "/../../config.php");
        $servers = Servers::find();
        foreach ($servers->rows as $row) {
            $tunnels = Tunnels::find(['serverIp' => $row->ip, 'status' => 4]);
            $users = Users::find([
                'ban' => 0,
                'userTask/!=' => 8,
                'userGroup/!=' => 2,
                'proxy/like' => $row->ip . ':%'
            ]);
            $proxy = Proxy::find(['status' => 0, 'proxy/like' => $row->ip . ':%']);
            if ($users->count <= 0 && $proxy->count === 0 && $tunnels->count === 1) {
                $tunnel = Tunnels::find(['serverIp' => $row->ip]);
                if ($tunnel->count > 0) {
                    $tunnel = $tunnel->rows[0];
                    Users::delete(['proxy/like' => $tunnel->serverIp . ':%', 'userGroup' => 1, 'ban' => 0]);
                    $tunnelData = TechAccount::find(['id' => $tunnel->tunnelAccountId])->rows[0];
                    $tunel = new TunnelBroker();
                    $tunel->login($tunnelData->name, $tunnelData->password);
                    $tunel->deleteTunnel($tunnel->tunnelId);
                    Tunnels::where(['serverIp' => $row->ip])->update(['status' => 0]);
                }
            }
        }
        
        return $output->writeln("Complite");
    }
}
