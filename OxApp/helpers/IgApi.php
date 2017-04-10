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
    public $guid;
    public $accountId;
    protected $password;
    protected $device_id;
    protected $igKey = '2f6dcdf76deb0d3fd008886d032162a79b88052b5f50538c1ee93c4fe7d02e60';
    //protected $igKey = 'b03e0daaf2ab17cda2a569cace938d639d1288a1197f9ecf97efd0a4ec0874d7';
    protected $igVersion = '4';
    public $csrftoken;
    
    public function __construct()
    {
        $device = new Device('9.7.0', 'en_US');
        $this->userAgent = UserAgent::buildUserAgent('9.7.0', 'en_US', $device);
        //        $device = new Device('10.15.0', 'en_US');
        //        $this->userAgent = UserAgent::buildUserAgent('10.15.0', 'en_US', $device);
    }
    
    public function getFeed($feedId)
    {
        
        $tst = $this->request("friendships/show/" . $feedId . "/");
        if ($tst['1']['is_private'] == 1) {
            $result = [];
            $result['1']['message'] = 'Not authorized to view user';
        } else {
            if (mt_rand(0, 1) == 1) {
                $result = $this->request("feed/user/" . $feedId . "/");
                print_r($this->request("feed/user/" . $feedId . "/story/"));
                print_r($this->request("feed/user/" . $feedId . "/info/"));
            } elseif (mt_rand(0, 1) == 0) {
                print_r($this->request("feed/user/" . $feedId . "/story/"));
                print_r($this->request("feed/user/" . $feedId . "/info/"));
                $result = $this->request("feed/user/" . $feedId . "/");
            } elseif (mt_rand(1, 2) == 2) {
                print_r($this->request("feed/user/" . $feedId . "/story/"));
                print_r($this->request("feed/user/" . $feedId . "/info/"));
                $result = $this->request("feed/user/" . $feedId . "/");
            } else {
                print_r($this->request("feed/user/" . $feedId . "/story/"));
                $result = $this->request("feed/user/" . $feedId . "/");
                print_r($this->request("feed/user/" . $feedId . "/info/"));
            }
        }
        
        print_r($result);
        
        // print_r($this->request('feed/user/' . $feedId . '/story/'));
        return $result;
    }
    
    public function like($mediaId)
    {
        $data = [
            '_uid' => $this->accountId,
            '_uuid' => $this->guid,
            '_csrftoken' => $this->csrftoken,
            'media_id' => $mediaId
        ];
        $data = json_encode($data);
        
        return $this->request('media/' . $mediaId . '/like/', $data);
    }
    
    public function follow($followUserId)
    {
        $data = [
            '_uid' => $this->accountId,
            '_uuid' => $this->guid,
            '_csrftoken' => $this->csrftoken,
            'user_id' => $followUserId
        ];
        $data = json_encode($data);
        $result = $this->request('friendships/create/' . $followUserId . '/', $data);
        $this->request('feed/user/' . $followUserId . '/');
        
        return $result;
    }
    
    public function getRecentActivityAll()
    {
        return $this->request('news/inbox/?limited_activity=true&show_su=true');
    }
    
    public function login($guid, $phoneId, $device_id, $password)
    {
        $this->guid = $guid;
        // $this->guid = '466dafce-f3e3-492b-f7d9-245ca0d3115c';
        // $phoneId = '485591b1-9ca8-4ed6-a1ff-289980b7fa37';
        $tokenResult = '';
        $i = 0;
        while ($tokenResult === '') {
            $sync = $this->sync();
            print_r($sync);
            
            if (preg_match('#Set-Cookie: csrftoken=([^;]+)#', $sync[0], $token)) {
                $tokenResult = $token[1];
            }
            if ($i == 10) {
                $tokenResult = false;
            }
            if ($sync[1]['message'] === 'checkpoint_required') {
                Users::where(['guid' => $guid, 'phoneId' => $phoneId, 'deviceId' => $device_id])->update(['ban' => 1]);
                exit("Account banned");
            }
            
            $i++;
        }
        if ($tokenResult == false || $tokenResult == '') {
            exit('empty token');
        }
        Users::where([
            'guid' => $guid,
            'phoneId' => $phoneId,
            'deviceId' => $device_id
        ])->update(['csrftoken' => $tokenResult]);
        $this->csrftoken = $tokenResult;
        $this->fetchHeadersSingUp();
        
        $data = [
            'phone_id' => $phoneId,
            '_csrftoken' => $this->csrftoken,
            'username' => $this->username,
            'guid' => $this->guid,
            'device_id' => $device_id,
            'password' => $password,
            'login_attempt_count' => 0
        ];
        
        $data = json_encode($data);
        $resultLogin = $this->request('accounts/login/', $data);
        print_r($resultLogin);
        $this->accountId = $resultLogin[1]['logged_in_user']['pk'];
        print_r($this->request('news/inbox/?activity_module=all'));
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
            // 'is_private' => true
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
    
    public function uploadPhoto($photo)
    {
        if (!empty($photo)) {
            $resultEdit = $this->request('upload/photo/', null, null, $photo);
            print_r($resultEdit);
        }
    }
    
    public function edit($biography, $url, $phoneId, $firstName, $email)
    {
        
        $sync = $this->sync();
        print_r($sync);
        if (preg_match('#Set-Cookie: csrftoken=([^;]+)#', $sync[0], $token)) {
            $tokenResult = $token[1];
        }
        if (empty($tokenResult)) {
            $sync = $this->sync();
            print_r($sync);
            if (preg_match('#Set-Cookie: csrftoken=([^;]+)#', $sync[0], $token)) {
                $tokenResult = $token[1];
            }
            if (empty($tokenResult)) {
                exit("no token");
            }
        }
        $this->csrftoken = $tokenResult;
        sleep(rand(0, 2));
        $data = [
            'phone_id' => $phoneId,
            '_csrftoken' => $this->csrftoken,
            'username' => $this->username,
            '_uid' => $this->accountId,
            'first_name' => $firstName,
            'email' => $email,
            'biography' => $biography,
            'gender' => 2,
            'external_url' => $url,
            'is_private' => true
        ];
        
        $data = json_encode($data);
        
        return $this->request('accounts/edit_profile/', $data);
    }
    
    public function create()
    {
        
        $faker = Factory::create();
        if (mt_rand(0, 4) == 1) {
            $uname = $faker->userName . rand(1950, 2017);
        } elseif (mt_rand(0, 1) == 0) {
            $uname = $faker->firstNameFemale . $faker->lastName . rand(1950, 2017);
        } else {
            $uname = $faker->lastName . $faker->firstNameFemale . rand(1950, 2017);
        }
        $uname = mb_strtolower($uname);
        if (rand(0, 1) == 1) {
            $this->username = str_replace(".", "", $uname);
        } else {
            $this->username = $uname;
        }
        $usernameTmp1 = substr($this->username, 0, -round(1, mb_strlen($this->username) - 3));
        $usernameTmp2 = substr($usernameTmp1, 0, -round(1, mb_strlen($usernameTmp1) - 3));
        $usernameTmp3 = substr($usernameTmp2, 0, -round(1, mb_strlen($usernameTmp2) - 3));
        $usernameTmp4 = substr($usernameTmp3, 0, -round(1, mb_strlen($usernameTmp3) - 3));
        
        $this->name = $faker->firstNameFemale;// . " " . $faker->lastName;
        if (rand(0, 1) == 1) {
            $this->name .= " " . $faker->lastName;
        }
        $domainMail = [
            'mail.com',
            'gmail.com',
            'hotmail.com',
            'icloud.com',
            'yahoo.com',
            'lycos.com',
            'aol.com',
            'gmx.com'
        ];
        //$email = $faker->email;
        if (mt_rand(0, 2) == 0) {
            $email = explode("@", $faker->email);
            $email = implode(rand(1940, 2017) . "@", $email);
        } elseif (mt_rand(0, 2) == 0) {
            $email = str_replace(" ", ".", $this->name) . mt_rand(0, 1999) . "@gmail.com";
        } elseif (mt_rand(0, 1) == 0) {
            $email = str_replace(" ", ".", $this->username) . mt_rand(0, 1999) . "@" . $domainMail[mt_rand(0,
                    count($domainMail) - 1)];
        } elseif (mt_rand(0, 1) == 0) {
            $email = str_replace(" ", ".", $this->name) . mt_rand(0, 1999) . "@" . $domainMail[mt_rand(0,
                    count($domainMail) - 1)];
        } else {
            $email = $uname . "@gmail.com";
        }
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
        $i = 0;
        while ($tokenResult === '') {
            $sync = $this->sync();
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
        
        sleep(rand(5, 15));
        $checkEmail = $this->checkEmail($email, $qe_id, $waterfall_id);
        
        print_r($checkEmail);
        if (isset($checkEmail[1]['message']) && $checkEmail[1]['message'] == 'Sorry, an error occured') {
            die('Error. Ip ban?');
        }
        sleep(rand(5, 10));
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
        
        if (rand(0, 1) == 1) {
            sleep(rand(5, 10));
            print_r($this->usernameSuggestions($usernameTmp4, $email, $waterfall_id));
        }
        sleep(rand(5, 7));
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
        
        sleep(rand(3, 5));
        print_r($this->usernameSuggestions($usernameTmp3, $email, $waterfall_id));
        
        
        sleep(rand(3, 7));
        print_r($this->usernameSuggestions($usernameTmp2, $email, $waterfall_id));
        
        if (rand(0, 1) == 1) {
            sleep(rand(2, 4));
            print_r($this->usernameSuggestions($usernameTmp1, $email, $waterfall_id));
        }
        sleep(rand(2, 5));
        $finalName = $this->usernameSuggestions($this->username, $email, $waterfall_id);
        print_r($finalName);
        //$this->username = $finalName[1]['suggestions'][rand(0, 11)];
        // $finalName = $this->usernameSuggestions($this->username, $email, $waterfall_id);
        print_r($finalName);
        echo "SET name: " . $this->username . "\n";
        
        sleep(rand(1, 6));
        //register:
        $create = $this->createAccount($email, $waterfall_id);
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
                'userAgent' => $this->userAgent
            ]);
        }
        
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
    public function sync()
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
    protected function request($method, $data = null, $file = null, $profilePhoto = null)
    {
        echo "Request: \n";
        echo $method . "\n";
        print_r($data);
        echo "\n\nResult: \n";
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
        } elseif ($profilePhoto) {
            $boundary = $this->guid;
            $bodies = [
                [
                    'type' => 'form-data',
                    'name' => 'upload_id',
                    'data' => number_format(round(microtime(true) * 1000), 0, '', ''),
                ],
                [
                    'type' => 'form-data',
                    'name' => '_uuid',
                    'data' => $boundary,
                ],
                [
                    'type' => 'form-data',
                    'name' => '_csrftoken',
                    'data' => $this->csrftoken,
                ],
                [
                    'type' => 'form-data',
                    'name' => 'image_compression',
                    'data' => '{"lib_name":"jt","lib_version":"1.3.0","quality":"87"}',
                ],
                [
                    'type' => 'form-data',
                    'name' => 'photo',
                    'data' => file_get_contents($profilePhoto),
                    'filename' => 'pending_media_' . number_format(round(microtime(true) * 1000), 0, '', '') . '.jpg',
                    'headers' => [
                        'Content-Transfer-Encoding: binary',
                        'Content-Type: application/octet-stream',
                    ],
                ],
            ];
            
            $data = $this->buildBody($bodies, $boundary);
            $headers = [
                'Proxy-Connection: keep-alive',
                'Connection: keep-alive',
                'Accept: */*',
                'Content-Type: multipart/form-data; boundary=' . $boundary,
                'Accept-Language: en-US',
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        //                            $headers=[
        //                                "X-IG-Connection-Type: WIFI",
        //                                "X-IG-Capabilities: 3Ro=",
        //                                'Accept-Encoding: gzip, deflate'
        //                            ];
        //                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
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