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
use OxApp\models\SystemSettings;
use OxApp\models\Users;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ThreadsControl
 *
 * @package Acme\Console\Command
 */
class ThreadsControl extends Command
{
    /**
     * configure
     */
    protected function configure()
    {
        $this->setName('start:threads')->setDescription('Cron jobs');
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
        for ($i = 0; $i < 5; $i++) {
            system('nohup /usr/bin/php /insta/console.php test:likes > /dev/null &');
            system('nohup /usr/bin/php /insta/console.php test:edit > /dev/null &');
            sleep(mt_rand(5, 14));
        }
        
        return $output->writeln("Complite");
    }
    
}