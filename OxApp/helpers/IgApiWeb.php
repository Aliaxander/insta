<?php
/**
 * Created by PhpStorm.
 * User: irina
 * Date: 03.04.17
 * Time: 19:42
 */

namespace OxApp\helpers;

use Faker\Factory;
use InstagramAPI\Checkpoint;
use OxApp\models\SystemSettings;
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
        
        $tokenResult = '';
        $i = 0;
        while ($tokenResult === '') {
            $sync = $this->syncRegister();
            print_r($sync);
            
            if (preg_match('#Set-Cookie: csrftoken=([^;]+)#', $sync[0], $token)) {
                $tokenResult = $token[1];
            }
            if ($i == 10) {
                $tokenResult = false;
            }
            $i++;
        }
        if ($tokenResult == false || $tokenResult == '') {
            die('empty token');
        }
        $this->csrftoken = $tokenResult;
        
        sleep(rand(15, 25));
        $checkEmail = $this->checkEmail($email, $qe_id, $waterfall_id);
        
        print_r($checkEmail);
        if (isset($checkEmail[1]['message']) && $checkEmail[1]['message'] == 'Sorry, an error occured') {
            die('Error. Ip ban?');
        }
        sleep(rand(15, 20));
        $singTokenResult = '';
        $i = 0;
        while ($singTokenResult === '') {
            $token = $this->fetchHeadersSingUp();
            print_r($token);
            
            if (preg_match('#Set-Cookie: csrftoken=([^;]+)#', $token[0], $token)) {
                $singTokenResult = $token[1];
            }
            if ($i == 10) {
                $singTokenResult = false;
            }
            $i++;
        }
        if ($singTokenResult == false || $singTokenResult == '') {
            die('empty sigKey token');
        }
        $this->csrftoken = $singTokenResult;
        
        $this->request('feed/timeline/?is_prefetch=0&seen_posts=&phone_id=' . $this->phone_id . '&battery_level=' . mt_rand(90,
                100) . '&timezone_offset=3600&is_pull_to_refresh=0&unseen_posts=&is_charging=' . mt_rand(0,
                1));
        
        
        if (rand(0, 1) == 1) {
            sleep(rand(15, 20));
            print_r($this->usernameSuggestions($usernameTmp4, $email, $waterfall_id));
        }
        sleep(rand(16, 24));
        $singTokenResult = '';
        $i = 0;
        while ($singTokenResult === '') {
            $token = $this->fetchHeadersSingUp();
            print_r($token);
            
            if (preg_match('#Set-Cookie: csrftoken=([^;]+)#', $token[0], $token)) {
                $singTokenResult = $token[1];
            }
            if ($i == 10) {
                $singTokenResult = false;
            }
            $i++;
        }
        if ($singTokenResult == false || $singTokenResult == '') {
            die('empty sigKey token');
        }
        $this->csrftoken = $singTokenResult;
        
        sleep(rand(5, 6));
        print_r($this->usernameSuggestions($usernameTmp3, $email, $waterfall_id));
        
        sleep(rand(4, 8));
        print_r($this->usernameSuggestions($usernameTmp2, $email, $waterfall_id));
        
        //                if (rand(0, 1) == 1) {
        //                    sleep(rand(3, 8));
        //                    print_r($this->usernameSuggestions($usernameTmp1, $email, $waterfall_id));
        //                }
        //        sleep(rand(4, 9));
        $finalName = $this->usernameSuggestions($this->username, $email, $waterfall_id);
        print_r($finalName);
        //$this->username = $finalName[1]['suggestions'][mt_rand(0, 11)];
        // $finalName = $this->usernameSuggestions($this->username, $email, $waterfall_id);
        print_r($finalName);
        echo "SET name: " . $this->username . "\n";
        
        sleep(rand(5, 11));
        //register:
        $createResult = '';
        $i = 0;
        while ($createResult === '') {
            $create = $this->createAccount($email, $waterfall_id);
            $createResult = $create[1];
            if ($i === 5) {
                $createResult = false;
            }
            $i++;
        }
        
        print_r($create);
        //        if (empty($create[1])) {
        //  $create = $this->createAccount($email, $waterfall_id);
        //        } elseif (isset($create[1]['errors']['username'])) {
        //            $this->username = $this->username . rand(0, 999999);
        //            $finalName = $this->usernameSuggestions($this->username, $email, $waterfall_id);
        //            print_r($finalName);
        //            $create = $this->createAccount($email, $waterfall_id);
        //        }
        //
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
    public function request($url, $data = null)
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
        
        $proxy = explode(";", $this->proxy);
        curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
        if (!empty($proxy[1])) {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[1]);
        }
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        curl_setopt($ch, CURLOPT_PROXYTYPE, 7);
        
        curl_setopt($ch, CURLOPT_URL, $url);
        
        //            $headers = [
        //                'Connection: keep-alive',
        //                "X-IG-Connection-Type: WIFI",
        //                "X-IG-Capabilities: " . Constants::xIgCapabilities,
        //                'Accept-Encoding: gzip, deflate',
        //                'Accept-Language: en-US',
        //            ];
        //            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //            curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        
        
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
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
        
        return [$header, json_decode($body, true)];
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