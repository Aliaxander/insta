<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 20.04.17
 * Time: 19:32
 */

namespace Acme\Console\Command;

use Ox\DataBase\DbConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BaseUpload extends Command
{
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('base:uploads')
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
        $start = microtime(true);
        $text = "";
        try {
            $dsn = DbConfig::$dbDriver . ':dbname=' . DbConfig::$dbname . ';host=' . DbConfig::$dbhost;
            $db = new \PDO($dsn, DbConfig::$dbuser, DbConfig::$dbuserpass, [
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ]
            );
        } catch (\PDOException $e) {
            throw new \PDOException($e);
        }
        $file = $input->getArgument('file');
        $file = file($file);
        $file = array_chunk($file, 1000000);
        foreach ($file as $value) {
            $text = '';
            foreach ($value as $item) {
                $acc = @preg_replace("/[^0-9]/", '', $item);
                $text .= "('$acc'),";
            }
            $text = mb_substr($text, 0, -1);
            $count = $db->exec("INSERT INTO instBase (`account`) VALUE $text");
        }
        
        $end = microtime(true);
        $time = round($end - $start, 4);
        
        return $output->writeln("Complite {$count} time: {$time}s");
    }
}
