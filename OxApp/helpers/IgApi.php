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

class IgApi
{
    public $username;
    public $userAgent = 'Instagram 9.7.0 Android (17/4.2.2; 240dpi; 480x800; samsung; GT-S7270; logan; hawaii_ss_logan; ru_RU)';
    public $proxy = '46.105.124.207:5016';
    protected $phone_id;
    protected $name;
    protected $guid;
    protected $accountId;
    protected $password;
    protected $device_id;
    protected $igKey = '2f6dcdf76deb0d3fd008886d032162a79b88052b5f50538c1ee93c4fe7d02e60';
    protected $igVersion = '4';
    protected $csrftoken;
    
    public function __construct()
    {
        $device = new Device('9.7.0', 'fr_FR');
        $this->userAgent = UserAgent::buildUserAgent('9.7.0', 'fr_FR', $device);
    }
    
    public function login()
    {
        $this->guid = '466dafce-f3e3-492b-f7d9-245ca0d3115c';
        $phoneId = '485591b1-9ca8-4ed6-a1ff-289980b7fa37';
        $sync = $this->sync();
        print_r($sync);
        if (preg_match('#Set-Cookie: csrftoken=([^;]+)#', $sync[0], $token)) {
            $tokenResult = $token[1];
        }
        if (empty($tokenResult)) {
            die("no token");
        }
        $this->csrftoken = $tokenResult;
        $this->username = 'vickyleuschke';
        $this->fetchHeadersSingUp();
        
        $data = [
            'phone_id' => $phoneId,
            '_csrftoken' => $this->csrftoken,
            'username' => $this->username,
            'guid' => $this->guid,
            'device_id' => 'android-dfbe4a9c13d9e6d1',
            'password' => 't76PFAgSOt',
            'login_attempt_count' => 0
        ];
        
        $data = json_encode($data);
        $resultLogin = $this->request('accounts/login/', $data);
        print_r($resultLogin);
        $this->accountId = $resultLogin[1]['logged_in_user']['pk'];
    }
    
    public function editProfile()
    {
        print_r($this->request('accounts/current_user/'));
        sleep(rand(1, 4));
        $data = [
            '_uid' => $this->accountId,
            '_uuid' => $this->guid,
            '_csrftoken' => $this->csrftoken,
            'external_url' => '',
            'phone_number' => '',
            'username' => $this->username,
            'first_name' => 'Susan Zulauf',
            'email' => 'glover.jayden46938@klocko.com',
            'biography' => '!! Yeee. Power by OxGroup !!',
            'gender' => 2,
            'is_private' => true
        ];
        $data = json_encode($data);
        $resultEdit = $this->request('accounts/edit_profile/', $data);
        print_r($resultEdit);
    }
    
    public function changeProfilePicture($photo)
    {
        if (!empty($photo)) {
            $resultEdit = $this->request('accounts/change_profile_picture/', null,
                $photo);
            print_r($resultEdit);
        }
    }
    
    
    public function edit()
    {
        /*   uName: vickyleuschke
        name: Susan Zulauf
        email: glover.jayden46938@klocko.com
        pass: t76PFAgSOt
        
        deviceId: android-dfbe4a9c13d9e6d1
        phoneId: 485591b1-9ca8-4ed6-a1ff-289980b7fa37
        waterfall_id: 70e133ff-581d-456e-ffa1-3ee82aa0dd1d
        guid: 466dafce-f3e3-492b-f7d9-245ca0d3115c
        qeId: 01ac2b57-761c-48d9-8180-55792a6e736d
*/
        /*
         *         return $this->request('accounts/edit_profile/')
        ->addPost('_uuid', $this->uuid)
        ->addPost('_uid', $this->account_id)
        ->addPost('_csrftoken', $this->token)
        ->addPost('external_url', $url)
        ->addPost('phone_number', $phone)
        ->addPost('username', $this->username)
        ->addPost('first_name', $firstName)
        ->addPost('biography', $biography)
        ->addPost('email', $email)
        ->addPost('gender', $gender)
         */
        $this->username = 'vickyleuschke';
        $this->guid = '466dafce-f3e3-492b-f7d9-245ca0d3115c';
        $sync = $this->sync();
        print_r($sync);
        if (preg_match('#Set-Cookie: csrftoken=([^;]+)#', $sync[0], $token)) {
            $tokenResult = $token[1];
        }
        if (empty($tokenResult)) {
            die("no token");
        }
        $this->csrftoken = $tokenResult;
        
        $data = [
            'phone_id' => '485591b1-9ca8-4ed6-a1ff-289980b7fa37',
            '_csrftoken' => $this->csrftoken,
            'username' => $this->username,
            '_uid' => '4983565950',
            'first_name' => 'Susan Zulauf',
            'email' => 'glover.jayden46938@klocko.com',
            'biography' => 'New text for OX'
        ];
        
        $data = json_encode($data);
        print_r($this->request('accounts/edit_profile/', $data));
    }
    
    public function create()
    {
        $faker = Factory::create();
        
        $this->username = str_replace(".", "", $faker->userName);
        $usernameTmp1 = substr($this->username, 0, -round(1, mb_strlen($this->username) - 3));
        $usernameTmp2 = substr($usernameTmp1, 0, -round(1, mb_strlen($usernameTmp1) - 3));
        $usernameTmp3 = substr($usernameTmp2, 0, -round(1, mb_strlen($usernameTmp2) - 3));
        $usernameTmp4 = substr($usernameTmp3, 0, -round(1, mb_strlen($usernameTmp3) - 3));
        
        $this->name = $faker->firstNameFemale;// . " " . $faker->lastName;
        //$email = $faker->email;
        $email = explode("@", $faker->email);
        $email = implode(rand(1000, 9999) . "@", $email);
        $this->password = strtolower(substr(md5(number_format(microtime(true), 7, '', '')), mt_rand(15, 24)));
        
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
        $sync = $this->sync();
        if (preg_match('#Set-Cookie: csrftoken=([^;]+)#', $sync[0], $token)) {
            $tokenResult = $token[1];
        }
        if (empty($tokenResult)) {
            die("Empty token");
        }
        $this->csrftoken = $tokenResult;
        print_r($sync);
        sleep(rand(4, 10));
        
        print_r($this->checkEmail($email, $qe_id, $waterfall_id));
        
        sleep(rand(4, 10));
        $token = $this->fetchHeadersSingUp();
        
        if (preg_match('#Set-Cookie: csrftoken=([^;]+)#', $token[0], $token)) {
            $singTokenResult = $token[1];
        }
        if (empty($singTokenResult)) {
            die();
        }
        $this->csrftoken = $singTokenResult;
        
        sleep(rand(3, 10));
        print_r($this->usernameSuggestions($usernameTmp4, $email, $waterfall_id));
        
        sleep(rand(3, 9));
        $token = $this->fetchHeadersSingUp();
        if (preg_match('#Set-Cookie: csrftoken=([^;]+)#', $token[0], $token)) {
            $singTokenResult = $token[1];
        }
        if (empty($singTokenResult)) {
            die();
        }
        $this->csrftoken = $singTokenResult;
        
        sleep(rand(3, 7));
        print_r($this->usernameSuggestions($usernameTmp3, $email, $waterfall_id));
        
        sleep(rand(3, 7));
        print_r($this->usernameSuggestions($usernameTmp2, $email, $waterfall_id));
        
        if (rand(0, 1) == 1) {
            sleep(rand(3, 7));
            print_r($this->usernameSuggestions($usernameTmp1, $email, $waterfall_id));
        }
        sleep(rand(3, 7));
        $finalName = $this->usernameSuggestions($this->username, $email, $waterfall_id);
        print_r($finalName);
        //$this->username = $finalName[1]['suggestions'][rand(0, 11)];
        // $finalName = $this->usernameSuggestions($this->username, $email, $waterfall_id);
        print_r($finalName);
        echo "SET name: " . $this->username . "\n";
        
        sleep(rand(1, 2));
        //register:
        $create = $this->createAccount($email, $waterfall_id);
        if (isset($create[1]['errors']['username'])) {
            $this->username = $this->username . rand(0, 999999);
            $create = $this->createAccount($email, $waterfall_id);
        }
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
                'userAgent' => $this->userAgent
            ]);
        }
        print_r($create);
        
        return true;
    }
    
    /**
     * @param $email
     * @param $waterfall_id
     *
     * @return array
     */
    protected function createAccount($email, $waterfall_id)
    {
        $data = [
            'allow_contacts_sync' => 'true',
            'phone_id' => $this->phone_id,
            '_csrftoken' => $this->csrftoken,
            'username' => $this->username,
            'first_name' => $this->name,
            'guid' => $this->guid,
            'device_id' => $this->device_id,
            'email' => $email,
            'waterfall_id' => $waterfall_id,
            'qs_stamp' => "",
            'password' => $this->password,
            'force_sign_up_code' => '',
        ];
        
        $data = json_encode($data);
        
        return $this->request('accounts/create/', $data);
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
     * @return array
     */
    protected function fetchHeadersSingUp()
    {
        return $this->request("si/fetch_headers/?guid=" . mb_strtolower(str_replace("-", "",
                $this->guid)) . "&challenge_type=singup");
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
            '_csrftoken' => $this->csrftoken,
            'email' => $email,
            'qe_id' => $uuid,
            'waterfall_id' => $waterfall_id,
        ]);
        
        return $this->request('users/check_email/', $data);
    }
    
    /**
     * @return array
     */
    protected function sync()
    {
        $syncData = json_encode([
            "id" => $this->guid,
            "experiments" => 'ig_android_prefill_phone_email_login_m_devices,ig_android_username_hint_copy,ig_android_ci_opt_in_at_reg,ig_android_one_click_in_old_flow,ig_androi
d_merge_fb_and_ci_friends_page,ig_android_reg_back_dialog,ig_android_profile_photo_nux,ig_android_remove_fb_nux_if_no_fb_installed,ig_android_non_
fb_sso,ig_android_mandatory_full_name,ig_android_iconless_reg,ig_android_analytics_data_loss,ig_android_prefill_phone_email_login,ig_fbns_blocked,
ig_android_contact_point_triage,ig_android_remove_ci_option_for_fb_reg,ig_android_auto_submit_verification_code,ig_android_prefill_phone_number,ig
_android_show_fb_social_context_in_nux,ig_fbns_push,ig_android_background_phone_confirmation,ig_android_phoneid_sync_interval,ig_android_login_lan
guage_picker'
        ]);
        
        return $this->request('qe/sync/', $syncData);
    }
    
    /**
     * @param      $method
     * @param null $data
     *
     * @return array
     */
    protected function request($method, $data = null, $file = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://i.instagram.com/api/v1/" . $method);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if ($file) {
            $uData = json_encode([
                '_csrftoken' => $this->csrftoken,
                '_uuid' => $this->guid,
                '_uid' => $this->accountId,
            ]);
            $boundary = $this->guid;
            $bodies = [
                [
                    'type' => 'form-data',
                    'name' => 'ig_sig_key_version',
                    'data' => $this->igVersion,
                ],
                [
                    'type' => 'form-data',
                    'name' => 'signed_body',
                    'data' => hash_hmac('sha256', $uData, $this->igKey) . $uData,
                ],
                [
                    'type' => 'form-data',
                    'name' => 'profile_pic',
                    'data' => file_get_contents($file),
                    'filename' => 'profile_pic',
                    'headers' => [
                        'Content-Type: application/octet-stream',
                        'Content-Transfer-Encoding: binary',
                    ],
                ],
            ];
            $postData = $this->buildBody($bodies, $boundary);
            $headers = [
                'Proxy-Connection: keep-alive',
                'Connection: keep-alive',
                'Accept: */*',
                'Content-Type: multipart/form-data; boundary=' . $boundary,
                'Accept-Language: en-en',
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        //                    $headers=[
        //                        "X-IG-Connection-Type: WIFI",
        //                        "X-IG-Capabilities: 3Ro=",
        //                        'Accept-Encoding: gzip, deflate'
        //                    ];
        //                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->username . "-cookies.dat");
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->username . "-cookies.dat");
        if ($file) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        } elseif ($data) {
            $hash = hash_hmac('sha256', $data, $this->igKey);
            $postData = 'signed_body=' . $hash . '.' . urlencode($data) . '&ig_sig_key_version=' . $this->igVersion;
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        //curl_setopt($ch, CURLOPT_PROXYUSERPWD, "HmddbF:h0WAKS");
        $resp = curl_exec($ch);
        $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($resp, 0, $header_len);
        $body = substr($resp, $header_len);
        curl_close($ch);
        
        return [$header, json_decode($body, true)];
    }
    
    /**
     * @param $bodies
     * @param $boundary
     *
     * @return string
     */
    protected function buildBody($bodies, $boundary)
    {
        $body = '';
        foreach ($bodies as $b) {
            $body .= '--' . $boundary . "\r\n";
            $body .= 'Content-Disposition: ' . $b['type'] . '; name="' . $b['name'] . '"';
            if (isset($b['filename'])) {
                $ext = pathinfo($b['filename'], PATHINFO_EXTENSION);
                $body .= '; filename="' . 'pending_media_' . number_format(round(microtime(true) * 1000), 0, '',
                        '') . '.' . $ext . '"';
            }
            if (isset($b['headers']) && is_array($b['headers'])) {
                foreach ($b['headers'] as $header) {
                    $body .= "\r\n" . $header;
                }
            }
            $body .= "\r\n\r\n" . $b['data'] . "\r\n";
        }
        $body .= '--' . $boundary . '--';
        
        return $body;
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