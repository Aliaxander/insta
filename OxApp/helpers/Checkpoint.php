<?php
namespace InstagramAPI;

use OxApp\models\Users;

class Checkpoint
{
    public $username;
    protected $settingsPath;
    public $accountId;
    public $proxy;
    protected $userAgent;
    protected $debug = true;
    
    public function __construct($username, $debug = false)
    {
        $this->username = $username;
        $this->debug = $debug;
        $this->userAgent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_3_3 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Mobile/13G34 Instagram 9.7.0 (iPhone5,2; iPhone OS 9_3_3; en_US; en-US; scale=2.00; 640x1136)';
    }
    
    
    public static function checkPoint($result, $user)
    {
        echo 'Checkpoint Test:';
        print_r($result);
        if (isset($result[1]['message'])) {
            $result = $result[1];
        }
        if (@$result['message'] === 'checkpoint_required') {
            echo "\n!!!!!!!!!!!!!!!!!>>>>>>>>>>>>Checkpoint detected:\n";
            if (isset($user->username)) {
                $user->userName = $user->username;
            }
            $checkPoint = new Checkpoint($user->userName);
            $checkPoint->proxy = $user->proxy;
            $checkPoint->accountId = $user->accountId;
            if (isset($result['checkpoint_url'])) {
                $result = $checkPoint->request($result['checkpoint_url']);
                print_r($result);
                if (preg_match("/Your phone number will be added\b/i", $result[1])) {
                    
                    //                    preg_match('/<input type="hidden" name="csrfmiddlewaretoken" value="(.*?)"\/>/mis',
                    //                        $result[1], $results);
                    //                    $token = $results[1];
                    //                    preg_match('/<form action="(.*?)" method="POST" accept-charset="utf-8" class="adjacent bordered">/mis',
                    //                        $result[1], $results);
                    //                    $post = $results[1];
                    //                    echo "sand post on $post and token $token";
                    //
                    //                    $result = $checkPoint->request('https://i.instagram.com' . $post, null,
                    //                        ['csrfmiddlewaretoken' => $token, 'phone_number' => '+79773230210']);
                    //                    print_r($result);
                    Users::where(['accountId' => $user->accountId])->update(['ban' => 3]);
                    die("SMS BAN!");
                }
            }
        }
    }
    
    public function doCheckpoint()
    {
        $token = $this->checkpointFirstStep();
        $this->checkpointSecondStep($token);
        
        return $token;
    }
    
    public function checkpointFirstStep()
    {
        $response = $this->request('https://i.instagram.com/integrity/checkpoint/checkpoint_logged_out_main/' . $this->accountId . '/?next=instagram%3A%2F%2Fcheckpoint%2Fdismiss');
        print_r($response);
        
        preg_match('#Set-Cookie: csrftoken=([^;]+)#', $response[0], $token);
        
        return $token;
    }
    
    public function checkpointSecondStep($token)
    {
        $post = [
            'csrfmiddlewaretoken' => $token[1],
            'email' => 'Verificar por correo electrónico',
        ];
        $headers = [
            'Origin: https://i.instagram.com',
            'Connection: keep-alive',
            'Proxy-Connection: keep-alive',
            'Accept-Language: es-es',
            'Referer: https://i.instagram.com/integrity/checkpoint/checkpoint_logged_out_main/' . $this->accountId . '/?next=instagram%3A%2F%2Fcheckpoint%2Fdismiss',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        ];
        $this->request('https://i.instagram.com/integrity/checkpoint/checkpoint_logged_out_main/' . $this->accountId . '/?next=instagram%3A%2F%2Fcheckpoint%2Fdismiss',
            $headers, $post);
        
        return $token;
    }
    
    public function checkpointThird($code, $token)
    {
        $post = [
            'csrfmiddlewaretoken' => $token,
            'response_code' => $code,
        ];
        $headers = [
            'Origin: https://i.instagram.com',
            'Connection: keep-alive',
            'Proxy-Connection: keep-alive',
            'Accept-Language: es-es',
            'Referer: https://i.instagram.com/integrity/checkpoint/checkpoint_logged_out_main/' . $this->accountId . '/?next=instagram%3A%2F%2Fcheckpoint%2Fdismiss',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        ];
        $this->request('https://i.instagram.com/integrity/checkpoint/checkpoint_logged_out_main/' . $this->accountId . '/?next=instagram%3A%2F%2Fcheckpoint%2Fdismiss',
            $headers, $post);
    }
    
    public function request($endpoint, $headers = null, $post = null, $first = true)
    {
        echo "\n------------Request-----------------:\n $endpoint";
        print_r($post);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        if (!is_null($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_VERBOSE, $this->debug);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/home/insta/cookies/" . $this->username . '-cookies.dat');
        curl_setopt($ch, CURLOPT_COOKIEJAR, "/home/insta/cookies/" . $this->username . '-cookies.dat');
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, count($post));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        $pos = strripos($this->proxy, ';');
        if ($pos === false) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        } else {
            $proxy = explode(";", $this->proxy);
            curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
            if (!empty($proxy[1])) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[1]);
            }
        }
        
        $resp = curl_exec($ch);
        $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($resp, 0, $header_len);
        $body = substr($resp, $header_len);
        curl_close($ch);
        if ($this->debug) {
            echo "REQUEST: $endpoint\n";
            if (!is_null($post)) {
                if (!is_array($post)) {
                    echo 'DATA: ' . urldecode($post) . "\n";
                }
            }
            echo "RESPONSE: $body\n\n";
        }
        $result = [$header, $body];
        echo "\n--------------Result--------------:\n";
        print_r($result);
        
        return $result;
    }
}