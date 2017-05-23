<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 20.04.17
 * Time: 19:32
 */

namespace Acme\Console\Command;

use OxApp\helpers\IgApi;
use OxApp\models\InstBase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ParseBase
 *
 * @package Acme\Console\Command
 */
class FilterBaseIg extends Command
{
    /**
     * @var IgApi
     */
    public $api;
    protected $parentId;
    
    /**
     * configure
     */
    protected function configure()
    {
        /**
         *
         */
        $this->api = new IgApi();
        $this
            ->setName('base:filter')
            ->setDescription('filter')->addArgument(
                'thread',
                InputArgument::OPTIONAL,
                'thread'
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
        $page = $file = $input->getArgument('thread');
        $start = 0;
        $stop = 10000;
        if (!empty($page)) {
            $start = $page * $stop + 1;
            $stop = $start + $stop;
        }
        $base = InstBase::limit([$start => $stop])->find(['status' => 0]);
        foreach ($base->rows as $row) {
//            print_r($row);
            $account = $row->account;
            $tst = @json_decode(file_get_contents('https://i.instagram.com/api/v1/users/' . $account . '/info/'));
            print_r($tst);
            $tst= @$tst->user;
            if (!empty(@$tst->external_url) || preg_match("/(http(s)?:\/\/)?([\\w-]+\\.)+[\\w-]+(\/[\\w- ;,.\/?%&=]*)?/",
                    @$tst->biography)) {//(isset($tst->media_count) && $tst->media_count <= 3) ||
                    InstBase::delete(['account' => $account]);
                    echo "Delete $account ".$tst->external_url." ".$tst->biography."\n";
            }
             
            }
        
        return $output->writeln("Complite");
    }
}
