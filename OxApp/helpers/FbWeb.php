<?php
/**
 * Created by PhpStorm.
 * User: irina
 * Date: 03.04.17
 * Time: 19:42
 */

namespace OxApp\helpers;

use Faker\Factory;

/**
 * Class FbWeb
 *
 * @package OxApp\helpers
 */
class FbWeb
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
        $this->userAgentMob = UserAgent::buildUserAgent(Constants::igVersion, $lang, $device);
    }
    
    /**
     * @return bool
     */
    public function createMob()
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
        $this->proxy = '185.156.178.80:30768';
        $userAgent = new RandomUserAgent();
        $this->userAgent = $userAgent->random_uagent();//'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:53.0) Gecko/20100101 Firefox/53.0';//
        $result = $this->request('https://m.facebook.com/');
        
        $result = $this->request('https://m.facebook.com/reg/');
        //        print_r($result);
        
        preg_match_all('/<input(.*?)\\>/mis',
            $result[1], $results);
        print_r($results);
        //
        foreach ($results[0] as $row) {
            //name="lsd" value="AVpceFuu"
            preg_match_all('/name="(.*?)" value="(.*?)"/mis',
                $row, $datas);
            print_r($datas);
            if (!empty($datas[1][0])) {
                $data[$datas[1][0]] = $datas[2][0];
            }
        }
        unset($data['field_names[]']);
//        $data['field_names[]'] = 'firstname';
//        $data['field_names[]'] = 'reg_email__';
//        $data['field_names[]'] = 'sex';
//        $data['field_names[]'] = 'birthday_wrapper';
        $data['field_names[]'] = 'reg_passwd__';
        $data['firstname'] = $faker->firstName;
        $data['lastname'] = $faker->lastName;
        $data['reg_email__'] = $email;
//        $data['reg_email_confirmation__'] = $email;
        $data['reg_passwd__'] = 'fvrt54eergz';
        $data['birthday_day'] = 2;
        $data['birthday_month'] = 2;
        $data['birthday_year'] = 1986;
        $data['sex'] = 1;
        
        print_r($data);
        print_r($this->request('https://m.facebook.com/reg/submit/?multi_step_form=1&amp;shouldForceMTouch=1', $data));
    
        /*
         *
__user=0
__a=1

__dyn=7AzHK4GgN2Hy49UrJxm2q3miWGey8G8rWo466EeVE98nwgUb8aUgxebmbwPG2iuUG4XzEa8uwh9VobohzElwIxWcwJwnoCQu2K4o6m5FE9k3Gu7E8ouwko2BxCqUkAxG5oW6o5-fwByUa8

__af=iw
__req=i
__be=-1
__pc=PHASED%3ADEFAULT
__rev=3041720
         */

//
//        $data=[
//
//        ];
//        $result=$this->request('https://www.facebook.com/ajax/register.php?dpr=1',$data);
//        print_r($result);
//
//        $token = $results[1];
//
//
//        $result = $this->request('https://www.instagram.com/ajax/bz',
//            ['q'], $token);
//        print_r($result);
//
//
//        $uname = $this->username;
//        $firstName = $this->name;
//        $password = $this->password;
//        $result = $this->request('https://www.instagram.com/accounts/web_create_ajax/attempt/',
//            ['email' => $email, 'first_name' => '', 'password' => '', 'username' => ''], $token);
//        print_r($result);
//
//        //
//        sleep(3);
//        $result = $this->request('https://www.instagram.com/accounts/web_create_ajax/attempt/',
//            ['email' => $email, 'first_name' => '', 'password' => '', 'username' => ''], $token);
//        print_r($result);
//
//        sleep(3);
//        $result = $this->request('https://www.instagram.com/accounts/web_create_ajax/attempt/',
//            ['email' => $email, 'first_name' => $firstName, 'password' => '', 'username' => $uname], $token);
//        print_r($result);
//
//        sleep(3);
//        $result = $this->request('https://www.instagram.com/accounts/web_create_ajax/attempt/',
//            ['email' => $email, 'first_name' => $firstName, 'password' => $password, 'username' => $uname], $token);
//        print_r($result);
//
//
//        $result = $this->request('https://www.instagram.com/accounts/web_create_ajax/', [
//            'email' => $email,
//            'first_name' => $firstName,
//            'password' => $password,
//            'username' => $uname
//        ], $token);
//        print_r($result);
//        $result = @json_decode($result[1]);//{"account_created": true, "status": "ok"}
//        if (isset($result->account_created) && $result->account_created === true) {
//            Users::add([
//                'userName' => $this->username,
//                'firstName' => $this->name,
//                'email' => $email,
//                'password' => $this->password,
//                'deviceId' => $this->device_id,
//                'phoneId' => $this->phone_id,
//                'waterfall_id' => $waterfall_id,
//                'guid' => $this->guid,
//                'qeId' => $qe_id,
//                'logIn' => 0,
//                'gender' => 0,
//                'photo' => '',
//                'biography' => '',
//                'proxy' => $this->proxy,
//                'userAgent' => $this->userAgentMob,
//                'dateCreate' => '//now()//'
//            ]);
//        }
        
        return true;
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
        $this->proxy = '185.156.178.80:30468';
        $userAgent = new RandomUserAgent();
        $this->userAgent = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:53.0) Gecko/20100101 Firefox/53.0';//$userAgent->random_uagent();//
        //$userAgent = new RandomUserAgent();
//        $this->userAgent = $userAgent->random_uagent();//'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:53.0) Gecko/20100101 Firefox/53.0';//
        $result = $this->request('https://www.facebook.com/');
        print_r($result);
                preg_match_all('/<form method="post" id="reg" (.*?)<\/form/mis',
                    $result[1], $results);

        preg_match_all('/<input(.*?)\\>/mis',
            $results[1][0], $results);
        print_r($results);
        //
        foreach ($results[0] as $row) {
            //name="lsd" value="AVpceFuu"
            preg_match_all('/name="(.*?)"(.*?)(value="(.*?))?"/mis',
                $row, $datas);
            print_r($datas);
            if (!empty($datas[1][0])) {
                $data[$datas[1][0]] = $datas[4][0];
            }
        }
        $data['firstname'] = $faker->firstName;
        $data['lastname'] = $faker->lastName;
        $data['reg_email__'] = $email;
        $data['reg_email_confirmation__'] = $email;
        $data['reg_passwd__'] = 'fvrt54eergz';
        $data['birthday_day'] = 2;
        $data['birthday_month'] = 2;
        $data['birthday_year'] = 1986;
        $data['sex'] = 1;
        print_r($this->request('https://www.facebook.com/ajax/registration/validation/contactpoint_invalid/?contactpoint=' . $email . '&dpr=1&__user=0&__a=1&__dyn=&__af=iw&__req=3&__be=-1&__pc=PHASED:DEFAULT&__rev=3041838'));
    
        print_r($this->request('https://www.facebook.com/ajax/registration/validation/contactpoint_invalid/?contactpoint=' . $email . '&dpr=1&__user=0&__a=1&__dyn=&__af=iw&__req=3&__be=-1&__pc=PHASED:DEFAULT&__rev=3041838'));
        print_r($data);
    
        $result = $this->request('https://www.facebook.com/ajax/register.php?dpr=1', $data);
        print_r($result);
    
        $result = $this->request('https://www.facebook.com/');
        print_r($result);
       /*
        lsd=AVrRnjgR
firstname=sdf
lastname=dfgdfg
reg_email__=dfgdfgdfg@gmail.com
reg_email_confirmation__=dfgdfgdfg@gmail.com
reg_second_contactpoint__
reg_passwd__=sdfsafsdfsdf
birthday_day=3
birthday_month=2
birthday_year=2013
sex=1
referrer
asked_to_login=0
terms=on
contactpoint_label=email_or_phone
ignore=reg_second_contactpoint__|captcha
locale=ru_RU
reg_instance=iLolWdkElRUVJ8obj_9WKYZA
captcha_persist_data=AZlNKX5oTWDnp6wjRwBowtEcIEAxoYTeBwGJ-S0simd7V6LCb0UPG8TPv-JFgcvIKgNgwCB94T5dDxAOayHMsf9psdiVgFu9gSYSJm7S1u7tGDcqyzI1rX8uTq-O95YOHMFX1rk8oMjpxF8m7LD7GzkH1p99_d-9iGIMIrzRskJWjoyoVj_0XimbvhR8wz2YQ12yqXQIjtFECyBZqA-NM1iXWw1h-hdknb-dAB2-i8DM5H0kFh2TzJksb1SzfV87fKRAssj1t-O_sUwCgGl4nfAOJpDQ8nLMeYzOT_XnFx4hF2rnDce8ZcccUL8UIuxGQj8xhdHIt0WfxyBEEZ2lovzSOVnVnDuP09UYx409BUSRPg
captcha_session=06_7DaJ7iwut_Objp77e1w
extra_challenge_params=authp=nonce.tt.time.new_audio_default&psig=TubgAux4DGDo1cze8_y4gfUXiQ0&nonce=06_7DaJ7iwut_Objp77e1w&tt=VJy9RbIg33EEbhd6VRs7snxvCws&time=1495644808&new_audio_default=1
recaptcha_type=password
captcha_response
__user=0
__a=1
__dyn=7AzHK4GgN2Hy49UrJxm2q3miWGey8G8rWo466EeVE98nwgUb8aUgxebmbwPG2iuUG4XzEa8uwh9VobohzElwIxWcwJwnoCQu2K4o6m5FE9k3Gu7E8ouwko2BxCqUkAxG5oW6o5-fwByUa8
__af=iw
__req=c
__be=-1
__pc=PHASED:DEFAULT
__rev=3041838
        */
        return true;
    }
    
    /**
     * @param      $url
     * @param null $data
     *
     * @return array
     */
    public function request($url, $data = null, $token = null)
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
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/home/insta/cookies/" . $this->username . "-Fbwebcookies.dat");
        curl_setopt($ch, CURLOPT_COOKIEJAR, "/home/insta/cookies/" . $this->username . "-Fbwebcookies.dat");
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        
        $proxy = explode(";", $this->proxy);
        curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
        if (!empty($proxy[1])) {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[1]);
        }
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        curl_setopt($ch, CURLOPT_PROXYTYPE, 7);
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, 'https://www.facebook.com/');
        
        if ($token) {
            $headers = [
                'X-CSRFToken: ' . $token,
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
        curl_close($ch);
        
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