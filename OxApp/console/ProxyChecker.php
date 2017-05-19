<?php
/**
 * Created by PhpStorm.
 * User: kinky
 * Date: 20.04.17
 * Time: 19:32
 */

namespace Acme\Console\Command;

use OxApp\models\Proxy;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProxyChecker extends Command
{
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('proxy:check')
            ->setDescription('file');
    }
    
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = 0;
        $deleted = 0;
        $proxy = Proxy::find(['status' => 0]);
        foreach ($proxy->rows as $row) {
            $status = $this->request($row->proxy)['http_code'];
            if ($status == '200' || $status == '301') {
                echo 'Ok - ' . $row->proxy . "\n";
                $count++;
            } else {
                Proxy::data(['id' => $row->id]);
                echo "DEL " . $row->proxy . "\n";
                $deleted++;
            }
        }
        
        return $output->writeln("Complite {$count} Deleted: {$deleted}");
    }
    
    protected function request($proxy)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        
        //    $proxy = explode(";", $this->proxy);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        
        //    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        //    curl_setopt($ch, CURLOPT_PROXYTYPE, 7);
        
        curl_setopt($ch, CURLOPT_URL, 'https://ya.ru/');
        
        $resp = curl_exec($ch);
        $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($resp, 0, $header_len);
        $body = substr($resp, $header_len);
        $info = curl_getinfo($ch);
        // curl_close($ch);
        curl_close($ch);
        
        return $info;
    }
    
}
