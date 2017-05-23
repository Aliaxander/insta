<?php
/**
 * Created by PhpStorm.
 * User: irina
 * Date: 03.04.17
 * Time: 19:42
 */

namespace OxApp\helpers;

use Faker\Factory;
use OxApp\models\Users;

/**
 * Class IgApiWeb
 *
 * @package OxApp\helpers
 */
class IgApiWeb
{
    
    public $username;
    public $userAgent;
    public $proxy;
    protected $phone_id;
    protected $name;
    public $guid;
    public $accountId;
    public $rank_token;
    protected $password;
    protected $device_id;
    public $csrftoken;
    protected $curl = false;
    
    /**
     * IgApi constructor.
     */
    public function __construct()
    {
        $languages = array(
            'fr_MC',
            'fr_LU',
            'de_CH',
            'es_PR',
            'eu_ES',
            'en_US',
            'fr_CH',
            'uk_UA',
        );
        $lang = $languages[mt_rand(0, count($languages) - 1)];
        $device = new Device(Constants::igVersion, $lang);
        $this->userAgent = UserAgent::buildUserAgent(Constants::igVersion, $lang, $device);
        //        $device = new Device('10.15.0', 'en_US');
        //        $this->userAgent = UserAgent::buildUserAgent('10.15.0', 'en_US', $device);
    }
    
    public function start(){
        
        //$api->proxy()
        $this->username = 'sdfsdfsf12321';
        $this->userAgent = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.4 (KHTML, like Gecko) Chrome/22.0.1229.94 Safari/537.4';
        $result = $this->request('https://www.instagram.com/');
        print_r($result);
        preg_match('/{"csrf_token": "(.*?)", "viewer": null}/mis',
            $result[1], $results);
    
        $token = $results[1];
        $email = 'sdfsd4fsaaadf34@gmail.com';
        $password = 'sdfsdfsdf34sdfsd';
        $uname = 'sdfgkfjgo433';
        $firstName = 'sdfsdfsdf34sdfsd';
        $password = 'dfdg54fref';
        
        $email = 'sdfsdfsaaadf34@gmail.com';
        $password = 'sdfsdfsdf34sdfsd';
        $uname = 'sdfsdfsdf34sdfsd';
        $firstName= 'sdfsdfsdf34sdfsd';
        $password='dfdg54fref';
        //sleep(3);
        //$result = $api->request('https://www.instagram.com/accounts/web_create_ajax/', [
        //    'email' => $email,
        //    'first_name' => $firstName,
        //    'password' => $password,
        //    'name' => $uname
        //],1);
        //$result = $api->request('https://www.instagram.com/accounts/web_create_ajax/attempt/',
        //    ['email' => $email, 'first_name' => '', 'password' => '', 'username' => '']);
        //print_r($result);
        //
        //
        ////sleep(3);
        //$result = $api->request('https://www.instagram.com/accounts/web_create_ajax/attempt/',
        //    ['email' => $email, 'first_name' => '', 'password' => '', 'username' => '']);
        //print_r($result);
        //
        //sleep(3);
        //$result = $api->request('https://www.instagram.com/accounts/web_create_ajax/attempt/',
        //    ['email' => $email, 'first_name' => $firstName, 'password' => '', 'username' => $uname]);
        //print_r($result);
        //
        //sleep(3);
        //$result = $api->request('https://www.instagram.com/accounts/web_create_ajax/attempt/',
        //    ['email' => $email, 'first_name' => $firstName, 'password' => $password, 'username' => $uname]);
        //print_r($result);
        //
        //
        $result = $api->request('https://www.instagram.com/accounts/web_create_ajax/', [
            'email' => $email,
            'first_name' => $firstName,
            'password' => $password,
            'name' => $uname
        ],1);
        print_r($result);
    
    }
    
    /**
     * @return bool
     */
    public function create()
    {
        $domainMail = [
            'mail.com',
            'gmail.com',
            'hotmail.com',
            'icloud.com',
            'yahoo.com'
        ];
        $faker = Factory::create();
        if (mt_rand(0, 4) == 1) {
            $uname = $faker->userName . mt_rand(0, 2017);
        } elseif (mt_rand(0, 3) == 0) {
            $uname = $faker->firstNameFemale . range('a', 'z')[mt_rand(0, 25)] . range('a', 'z')[mt_rand(0,
                    25)] . $faker->lastName . range('a',
                    'z')[mt_rand(0, 25)] . range('a', 'z')[mt_rand(0, 25)];
        } elseif (mt_rand(0, 2) == 0) {
            $uname = $faker->firstNameFemale . range('a',
                    'z')[mt_rand(0, 25)] . range('a', 'z')[mt_rand(0, 25)] . $faker->lastName;
        } elseif (mt_rand(0, 1) == 0) {
            $uname = $faker->firstNameFemale . $faker->lastName . range('a',
                    'z')[mt_rand(0, 25)] . range('a', 'z')[mt_rand(0, 25)];
        } else {
            $uname = $faker->lastName . range('a',
                    'z')[mt_rand(0, 25)] . $faker->firstNameFemale . range('a',
                    'z')[mt_rand(0, 25)] . mt_rand(0, 2017);
        }
        $uname = mb_strtolower($uname);
        if (rand(0, 1) == 1) {
            $this->username = str_replace(".", "", $uname);
        } else {
            $this->username = $uname;
        }
        if (rand(0, 1) == 1) {
            $this->username .= mt_rand(0, 2017);
        }
        $this->password = strtolower(substr(md5(number_format(microtime(true), 7, '', '')), mt_rand(15, 20)));
        $this->name = $faker->firstNameFemale . range('a', 'z')[mt_rand(0, 25)] . range('a', 'z')[mt_rand(0,
                25)];// . " " . $faker->lastName;
        if (rand(0, 1) == 1) {
            $this->name .= " " . $faker->lastName . range('a', 'z')[mt_rand(0, 25)] . range('a', 'z')[mt_rand(0, 25)];
        }
        
        //$email = $faker->email;
        if (mt_rand(0, 2) == 0) {
            $email = str_replace(" ", ".", $this->name) . range('a', 'z')[mt_rand(0, 25)] . mt_rand(0,
                    999) . "@gmail.com";
        } elseif (mt_rand(0, 1) == 0) {
            $email = str_replace(" ", ".", $this->username) . range('a', 'z')[mt_rand(0,
                    25)] . mt_rand(0, 999) . "@" . $domainMail[mt_rand(0,
                    count($domainMail) - 1)];
        } elseif (mt_rand(0, 1) == 0) {
            $email = str_replace(" ", ".", $this->name) . range('a', 'z')[mt_rand(0,
                    25)] . mt_rand(0, 999) . "@" . $domainMail[mt_rand(0,
                    count($domainMail) - 1)];
        } elseif (mt_rand(0, 1) == 0) {
            $email = str_replace(" ", ".", $this->name) . mt_rand(0, 9999) . "@" . $domainMail[mt_rand(0,
                    count($domainMail) - 1)];
        } else {
            $email = $uname . range('a', 'z')[mt_rand(0, 25)] . mt_rand(0, 999) . "@gmail.com";
        }
        if (mt_rand(0, 1) === 1) {
            $email = mb_strtolower($email);
        }
        $usernameTmp1 = substr($this->username, 0, -round(1, mb_strlen($this->username) - 3));
        $usernameTmp2 = substr($usernameTmp1, 0, -round(1, mb_strlen($usernameTmp1) - 3));
        $usernameTmp3 = substr($usernameTmp2, 0, -round(1, mb_strlen($usernameTmp2) - 3));
        $usernameTmp4 = substr($usernameTmp3, 0, -round(1, mb_strlen($usernameTmp3) - 3));
        
        $megaRandomHash = md5(number_format(microtime(true), 7, '', ''));
        $this->device_id = 'android-' . strtolower(substr($megaRandomHash, 16));
        $this->phone_id = strtolower($this->genUuid());
        $waterfall_id = strtolower($this->genUuid());
        $this->guid = strtolower($this->genUuid());
        $qe_id = strtolower($this->genUuid());
        
        echo "Generate DATA:
        uName: {$this->username}
        name: {$this->name}
        email: {$email}
        pass: {$this->password}

        deviceId: {$this->device_id}
        phoneId: {$this->phone_id}
        waterfall_id: {$waterfall_id}
        guid: $this->guid
        qeId: $qe_id
        userAgent: {$this->userAgent}
        proxy: {$this->proxy}
        Start...
        ";
       
        if (isset($create[1]['created_user']['pk'])) {
            Users::add([
                'userName' => $this->username,
                'firstName' => $this->name,
                'email' => $email,
                'password' => $this->password,
                'deviceId' => $this->device_id,
                'phoneId' => $this->phone_id,
                'waterfall_id' => $waterfall_id,
                'guid' => $this->guid,
                'qeId' => $qe_id,
                'logIn' => 0,
                'gender' => 0,
                'accountId' => $create[1]['created_user']['pk'],
                'photo' => '',
                'biography' => '',
                'proxy' => $this->proxy,
                'userAgent' => $this->userAgent,
                'dateCreate' => '//now()//'
            ]);
        } elseif (empty($create[1])) {
            Users::add([
                'userName' => $this->username,
                'firstName' => $this->name,
                'email' => $email,
                'password' => $this->password,
                'deviceId' => $this->device_id,
                'phoneId' => $this->phone_id,
                'waterfall_id' => $waterfall_id,
                'guid' => $this->guid,
                'qeId' => $qe_id,
                'logIn' => 0,
                'gender' => 0,
                'accountId' => 0,
                'photo' => '',
                'biography' => '',
                'proxy' => $this->proxy,
                'userAgent' => $this->userAgent,
                'dateCreate' => '//now()//'
            ]);
        }
        
        return true;
    }
    
    
    /**
     * @param $username
     * @param $email
     * @param $waterfall_id
     *
     * @return array
     */
    protected function usernameSuggestions($username, $email, $waterfall_id)
    {
        $data = json_encode([
            '_csrftoken' => $this->csrftoken,
            'name' => $username,
            'email' => $email,
            'waterfall_id' => $waterfall_id,
        ]);
        
        return $this->request('accounts/username_suggestions/', $data);
    }
    
    
    /**
     * @param $email
     * @param $uuid
     * @param $waterfall_id
     *
     * @return array
     */
    protected function checkEmail($email, $uuid, $waterfall_id)
    {
        $data = json_encode([
            //'_csrftoken' => $this->csrftoken,
            'email' => $email,
            'qe_id' => $uuid,
            'waterfall_id' => $waterfall_id,
        ]);
        
        return $this->request('users/check_email/', $data);
    }
    
    
    /**
     * @param      $url
     * @param null $data
     *
     * @return array
     */
    public function request($url, $data = null, $create = null)
    {
        echo "Request: \n";
        echo $url . "\n";
        print_r($data);
        echo "\n\nResult: \n";
        $ch = curl_init();
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/home/insta/cookies/" . $this->username . "-webcookies.dat");
        curl_setopt($ch, CURLOPT_COOKIEJAR, "/home/insta/cookies/" . $this->username . "-webcookies.dat");
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        //
        //        $proxy = explode(";", $this->proxy);
        //        curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
        //        if (!empty($proxy[1])) {
        //            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[1]);
        //        }
        //        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        //        curl_setopt($ch, CURLOPT_PROXYTYPE, 7);
    
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, ' https://www.instagram.com/');
    
        if ($create) {
            $headers = [
                'X-CSRFToken: ' . $create,
                'X-Instagram-AJAX: 1',
                'X-Requested-With: XMLHttpRequest',
                'x-insight: activate'
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            //            curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        }
    
        if ($data) {
            //            if ($json) {
            //                $data = json_encode($data);
            //                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            //                        'Content-Type: application/json',
            //                        'Content-Length: ' . strlen($data)
            //                    )
            //                );
            //            }
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
        }
        $resp = curl_exec($ch);
        $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($resp, 0, $header_len);
        $body = substr($resp, $header_len);
        $info = curl_getinfo($ch);
        // curl_close($ch);
        echo "\n\nHeaders:\n";
        print_r($info);
        echo "\n\nBody:";
        print_r($body);
    
        return [$header, $body];
    }
    
    
    /**
     * @return array
     */
    protected function genUuid()
    {
        $uuid = array(
            'time_low' => 0,
            'time_mid' => 0,
            'time_hi' => 0,
            'clock_seq_hi' => 0,
            'clock_seq_low' => 0,
            'node' => array()
        );
        
        $uuid['time_low'] = mt_rand(0, 0xffff) + (mt_rand(0, 0xffff) << 16);
        $uuid['time_mid'] = mt_rand(0, 0xffff);
        $uuid['time_hi'] = (4 << 12) | (mt_rand(0, 0x1000));
        $uuid['clock_seq_hi'] = (1 << 7) | (mt_rand(0, 128));
        $uuid['clock_seq_low'] = mt_rand(0, 255);
        
        for ($i = 0; $i < 6; $i++) {
            $uuid['node'][$i] = mt_rand(0, 255);
        }
        
        $uuid = sprintf(
            '%08x-%04x-%04x-%02x%02x-%02x%02x%02x%02x%02x%02x',
            $uuid['time_low'],
            $uuid['time_mid'],
            $uuid['time_hi'],
            $uuid['clock_seq_hi'],
            $uuid['clock_seq_low'],
            $uuid['node'][0],
            $uuid['node'][1],
            $uuid['node'][2],
            $uuid['node'][3],
            $uuid['node'][4],
            $uuid['node'][5]
        );
        
        return $uuid;
    }
    
}