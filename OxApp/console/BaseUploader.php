<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 15.08.15
 * Time: 15:16
 */

namespace Acme\Console\Command;

use Faker\Factory;
use OxApp\helpers\IgApi;

use OxApp\models\Domains;
use OxApp\models\InstBase;
use OxApp\models\Proxy;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BaseUploader
 *
 * @package Acme\Console\Command
 */
class BaseUploader extends Command
{
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('base:upload')
            ->setDescription('file')->addArgument(
                'file',
                InputArgument::OPTIONAL,
                'filename'
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
        $file = $input->getArgument('file');
        $file = file($file);
        foreach ($file as $row) {
            $acc = @preg_replace("/[^0-9]/", '', $row);
            InstBase::add(['account' => $acc]);
        }
        
        return $output->writeln("Complite");
    }
}