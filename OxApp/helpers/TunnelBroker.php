<?php
/**
 * Created by PhpStorm.
 * User: irina
 * Date: 15.05.17
 * Time: 3:44
 */

namespace OxApp\helpers;

/**
 * Class TunnelBroker
 *
 * @package OxApp\helpers
 */
class TunnelBroker
{
    public $username = '';
    protected $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36';
    public $ips = [
        '74.82.46.6',
        '216.66.84.46',
        '216.66.86.114',
        '216.66.87.14',
        '216.66.80.30',
        '216.66.87.102',
        '216.66.80.26',
        '216.66.88.98',
        '216.66.84.42',
        '216.66.86.122',
        '216.66.80.90',
        '216.66.80.162',
        '216.66.80.98',
        '216.66.22.2',
        '184.105.253.14',
        '184.105.253.10',
        '184.105.250.46',
        '72.52.104.74',
        '64.62.134.130',
        '64.71.156.86',
        '216.66.77.230',
        '66.220.18.42',
        '209.51.161.58',
        '209.51.161.14',
        '66.220.7.82',
        '216.218.226.238',
        '216.66.38.58',
        '184.105.255.26',
        '216.66.87.134'
    ];
    public $proxy = '46.105.121.37:21099';
    //
    //    public function __construct()
    //    {
    //        $proxys = [
    //            '151.80.93.34:1080',
    //            '151.80.93.45:1080',
    //            '174.103.149.169:15310',
    //            '14.141.77.10:1080',
    //            '91.195.103.172:11086',
    //            '8.2.214.8:52520',
    //            '82.209.138.37:45554',
    //            '65.35.38.244:26263',
    //            '83.209.187.218:45554',
    //            '46.105.121.37:21099',
    //            '46.105.121.37:33365',
    //            '47.21.134.154:43407',
    //            '46.105.107.107:10520',
    //            '35.185.253.171:1080',
    //            '193.253.104.157:33191',
    //            '187.241.4.187:45554',
    //            '177.38.97.194:3389',
    //            '164.132.20.94:28946',
    //            '142.0.102.231:10200',
    //            '110.142.208.171:45809',
    //            '47.208.209.111:60829',
    //            '217.23.9.74:30759',
    //            '151.80.93.46:1080',
    //            '67.197.250.83:14222',
    //            '213.64.155.223:45554',
    //            '144.76.109.247:3128',
    //            '5.51.249.68:47702',
    //            '151.80.93.40:1080',
    //            '216.47.217.15:45554',
    //            '124.155.212.69:48124',
    //            '198.91.156.125:41216',
    //            '87.100.129.248:45554',
    //            '91.155.198.45:45554',
    //            '91.158.228.190:45554',
    //            '151.80.93.40:1080',
    //            '46.105.121.37:15310',
    //            '46.105.121.37:21099',
    //            '46.105.121.37:23784',
    //            '46.105.121.37:33365',
    //            '46.105.121.37:35049',
    //            '46.105.121.37:41742',
    //            '46.105.121.37:56991',
    //            '46.105.121.37:57796',
    //            '5.135.214.238:24632',
    //            '51.255.201.110:24632',
    //            '51.255.201.76:24632',
    //            '178.32.56.110:24632',
    //            '37.191.217.128:46690',
    //            '84.202.245.152:45554',
    //            '84.202.60.118:45554',
    //            '5.15.184.13:17956',
    //            '83.169.208.87:45554',
    //            '95.215.97.196:45554',
    //            '213.113.32.34:45554',
    //            '213.114.104.69:45554',
    //            '31.192.207.40:45554',
    //            '31.208.51.86:45554',
    //            '46.162.125.10:45554',
    //            '77.105.196.132:45554',
    //            '77.53.144.118:45554',
    //            '82.209.173.7:45554',
    //            '84.217.12.73:45554',
    //            '85.229.247.21:45554',
    //            '85.230.141.37:45554',
    //            '89.233.201.176:45554',
    //            '94.255.214.250:45554',
    //            '178.150.96.58:45554',
    //            '104.231.94.131:52546',
    //            '165.166.235.13:45554',
    //            '170.250.107.235:45554',
    //            '174.69.3.119:22036',
    //            '204.116.193.200:45554',
    //            '208.95.182.199:45554',
    //            '208.99.107.132:45554',
    //            '209.124.216.163:45554',
    //            '216.212.240.194:45554',
    //            '216.212.241.247:45554',
    //            '216.212.251.28:45554',
    //            '24.106.91.30:47186',
    //            '24.236.126.66:45554',
    //        ];
    //        foreach ($proxys as $proxy) {
    //            $proxyTst = explode(':', $proxy);
    //            $host = $proxyTst[0];
    //            $port = $proxyTst[1];
    //            if ($fp = @fsockopen($host, $port, $errCode, $errStr, 5)) {
    //                $this->proxy = $proxy;
    //                exit();
    //            }
    //        }
    //    }
    
    /**
     * @param $serverIp
     *
     * @return array
     */
    public function createNewTunnel($serverIp)
    {
        $ips = $this->getNewIps();
        $ip = $ips[rand(0, count($ips) - 1)];
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = $this->ips[rand(0, count($this->ips) - 1)];
        }
        $html = $this->createTunnel($ip, $serverIp);
        print_r($html);
        preg_match_all('/<span class=\"fr\">(.*?)<\/span>/s', $html[1], $estimates);
        $result = $estimates[1];
        
        $tunnelId = preg_replace('/\D/', '', $result[0]);
        $remoteIp = $result[3];
        $v6route = strip_tags($result[5]);
        $result = $this->create48($tunnelId);
        print_r($result);
        $sub48 = str_replace('::/48', '', $result[1]);
        
        return ['tunnelId' => $tunnelId, 'remoteIp' => $remoteIp, 'v6route' => $v6route, '48sub' => $sub48];
    }
    
    /**
     * @param $login
     * @param $password
     *
     * @return array
     */
    public function login($login, $password)
    {
        $this->username = $login;
        @unlink("/home/insta/cookies/" . $this->username . '-tunel.dat');
        
        return $this->request('https://tunnelbroker.net/login.php',
            ['f_user' => $login, 'f_pass' => $password, 'redir' => '', 'Login' => 'Login']);
    }
    
    /**
     * @return array
     */
    public function getNewIps()
    {
        $getServers = $this->request('https://tunnelbroker.net/new_tunnel.php');
        $dom = new \DOMDocument();
        $dom->loadHTML($getServers[1]);
        $xpath = new \DOMXPath($dom);
        $result_rows = $xpath->query('//span');
        $count = 5;
        $ips = [];
        while ($count < 34) {
            $ips[] = @$result_rows->item($count)->textContent;
            $count++;
        }
        
        return $ips;
    }
    
    /**
     * @param $tunnelId
     *
     * @return array
     */
    public function deleteTunnel($tunnelId)
    {
        
        return $this->request('https://tunnelbroker.net/tunnel_detail.php?tid=' . $tunnelId,
            ['delete' => 'Delete+Tunnel']);
    }
    
    /**
     * @param $setIp
     * @param $serverIp
     *
     * @return array
     */
    public function createTunnel($setIp, $serverIp)
    {
        return $this->request('https://tunnelbroker.net/new_tunnel.php',
            ['ipv4z' => $serverIp, 'tserv' => $setIp, 'normaltunnel' => 'Create+Tunnel']);
    }
    
    /**
     * @param $tunnelId
     *
     * @return array
     */
    public function create48($tunnelId)
    {
        return $this->request('https://tunnelbroker.net/tunnel_detail.php?tid=' . $tunnelId . '&ajax=true',
            ['assign48' => 'true']);
    }
    
    /**
     * @param      $url
     * @param null $post
     * @param null $headers
     * @param bool $first
     *
     * @return array
     */
    public function request($url, $post = null, $headers = null, $first = true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        if (!is_null($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/home/insta/cookies/" . $this->username . '-tunel.dat');
        curl_setopt($ch, CURLOPT_COOKIEJAR, "/home/insta/cookies/" . $this->username . '-tunel.dat');
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, count($post));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        
        curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        curl_setopt($ch, CURLOPT_PROXYTYPE, 7);
        
        $resp = curl_exec($ch);
        $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($resp, 0, $header_len);
        $body = substr($resp, $header_len);
        curl_close($ch);
        
        $result = [$header, $body, $post, $url];
        //        echo "\n--------------Result--------------:\n";
        //        print_r($result);
        //
        return $result;
    }
}