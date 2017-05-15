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
            $ips[] = $result_rows->item($count)->textContent;
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
        //        echo "\n------------Request-----------------:\n $url";
        //        print_r($post);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        if (!is_null($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        //curl_setopt($ch, CURLOPT_VERBOSE, $this->debug);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/home/insta/cookies/" . $this->username . '-tunel.dat');
        curl_setopt($ch, CURLOPT_COOKIEJAR, "/home/insta/cookies/" . $this->username . '-tunel.dat');
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, count($post));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        
        $resp = curl_exec($ch);
        $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($resp, 0, $header_len);
        $body = substr($resp, $header_len);
        curl_close($ch);
        
        $result = [$header, $body];
        //        echo "\n--------------Result--------------:\n";
        //        print_r($result);
        //
        return $result;
    }
}