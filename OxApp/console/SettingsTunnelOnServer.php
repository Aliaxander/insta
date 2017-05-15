<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 15.08.15
 * Time: 15:16
 */

namespace Acme\Console\Command;

use OxApp\models\Proxy;
use OxApp\models\Servers;
use OxApp\models\Tunnels;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SettingsTunnelOnServer
 *
 * @package Acme\Console\Command
 */
class SettingsTunnelOnServer extends Command
{
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('setting:server')
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
        $tunnels = Tunnels::find(['status' => 2]);
        if ($tunnels->count > 0) {
            $tunnel = $tunnels->rows[0];
            Tunnels::where(['id' => $tunnel->id])->update([
                'status' => 3,
            ]);
            if ($tunnel->v6route !== '[1500]' && $tunnel->remoteIp !== '[1500]' && $tunnel->remoteIp !== '' && $tunnel->v6route !== '') {
                $server = Servers::find(['ip' => $tunnel->serverIp])->rows[0];
                print_r($server);
                $connection = ssh2_connect($server->ip, 22);
                var_dump(ssh2_auth_password($connection, 'root', $server->password));
                
                /**
                 *
                 * ulimit -n 600000
                 * ulimit -u 600000
                 * pkill 3proxy
                 * #service network restart
                 * ip -6 route del default
                 * modprobe ipv6
                 * ip tunnel add he-ipv6 mode sit remote $1 local 91.211.116.248 ttl 255
                 * ip link set he-ipv6 up
                 * ip addr add $2 dev he-ipv6
                 * ip route add ::/0 dev he-ipv6
                 * ip -f inet6 addr
                 */
                //ssh2_exec($connection, 'sh 1.sh '. $tunnel->remoteIp.' '. $tunnel->v6route);
                
                ssh2_exec($connection, 'ulimit -n 600000');
                ssh2_exec($connection, 'ulimit -u 600000');
                ssh2_exec($connection, 'pkill 3proxy');
                ssh2_exec($connection, 'ip link delete he-ipv6');
                ssh2_exec($connection, 'ifconfig he-ipv6 down');
                ssh2_exec($connection, 'nohup service network restart &');
                sleep(7);
                $connection = ssh2_connect($server->ip, 22);
                var_dump(ssh2_auth_password($connection, 'root', $server->password));
                ssh2_exec($connection, 'ip -6 route del default');
                ssh2_exec($connection, 'modprobe ipv6');
                
                echo "\n>" . 'ip tunnel add he-ipv6 mode sit remote ' . $tunnel->remoteIp . ' local ' . $tunnel->serverIp . ' ttl 255' . "<\n";
                ssh2_exec($connection,
                    'ip tunnel add he-ipv6 mode sit remote ' . $tunnel->remoteIp . ' local ' . $tunnel->serverIp . ' ttl 255');
                
                ssh2_exec($connection, 'ip link set he-ipv6 up');
                
                echo "\n>" . 'ip addr add ' . $tunnel->v6route . ' dev he-ipv6' . "<\n";
                ssh2_exec($connection, 'ip addr add ' . $tunnel->v6route . ' dev he-ipv6');
                
                ssh2_exec($connection, 'ip route add ::/0 dev he-ipv6');
                
                $name = '48sub';
                $exc = './fastProxy.sh ' . $tunnel->$name;
                ssh2_exec($connection, $exc);
                
                $stream = ssh2_exec($connection, 'ifconfig');
                stream_set_blocking($stream, true);
                $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
                echo stream_get_contents($stream_out);
                
                Tunnels::where(['id' => $tunnel->id])->update([
                    'status' => 4,
                ]);
                Proxy::delete(['proxy/like' => $tunnel->serverIp . ':%']);
                for ($i = 30000; $i < 30200; $i++) {
                    $proxy[] = Proxy::add([
                        'proxy' => $tunnel->serverIp . ":" . $i . ";",
                        'rand' => rand(0, 1000)
                    ]);
                }
            }
        }
        
        return $output->writeln("Complite");
    }
}
