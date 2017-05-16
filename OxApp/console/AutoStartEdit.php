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
 * Class AutoStartEdit
 *
 * @package Acme\Console\Command
 */
class AutoStartEdit extends Command
{
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('edit:autostart')
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
        
        Users::where([
            'ban' => 0,
            'userTask' => 1,
            'userGroup/!=' => 2,
            'dateCreate/<=' => '//now()-interval ' . mt_rand(40, 70) . ' MINUTE//'
        ])->update(['userTask' => 2]);
        
        
        return $output->writeln("Complite");
    }
}
