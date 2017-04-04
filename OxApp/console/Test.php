<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 15.08.15
 * Time: 15:16
 */

namespace Acme\Console\Command;

use DocBlockReader\Reader;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;
use Ox\Hash;
use OxApp\controllers\api\AccessTokenController;
use OxApp\helpers\IgApi;
use OxApp\helpers\Login;
use OxApp\models\CollectTables;
use OxApp\models\Proxy;
use OxApp\models\Users;
use OxApp\modules\integration\Leadvertex;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Code\Annotation\Parser\DoctrineAnnotationParser;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Scanner\DocBlockScanner;

/**
 * Class CronCommands
 *
 * @package Acme\Console\Command
 */
class Test extends Command
{
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('test:test')
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
        //5 600 000
        require(__DIR__ . "/../../config.php");
        $proxy = Proxy::limit([0 => 1])->find(['status' => 0]);
    
        $api = new IgApi();
    
        if ($proxy->count > 0) {
            foreach ($proxy->rows as $row) {
                Proxy::where(['id' => $row->id])->update(['status' => 1]);
                $api->proxy = $row->proxy;
                $api->create();
            }
        }
    
        return $output->writeln("Complite");
    }
}
