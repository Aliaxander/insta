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

class IgApi
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
    protected $igKey = 'f6bcd0639ea3d279c10d4ffe49bd59f1f5045cf73c62fc33c9e45766f6cf6c63';
    protected $xIgCapabilities = '3ToAAA==';
    //protected $igKey = 'b03e0daaf2ab17cda2a569cace938d639d1288a1197f9ecf97efd0a4ec0874d7';
    protected $igVersion = '4';
    public $csrftoken;
    
    public function __construct()
    {
        $device = new Device('10.1.0', 'en_US');
        $this->userAgent = UserAgent::buildUserAgent('10.1.0', 'en_US', $device);
        //        $device = new Device('10.15.0', 'en_US');
        //        $this->userAgent = UserAgent::buildUserAgent('10.15.0', 'en_US', $device);
    }
    
    public function getFeed($feedId, $maxId = '')
    {
        
        $tst = $this->request("friendships/show/" . $feedId . "/");
        if (@$tst['1']['is_private'] == 1) {
            $result = [];
            $result['1']['message'] = 'Not authorized to view user';
        } elseif (@$tst['1']['message'] === "checkpoint_required") {
            $result = [];
            $result['1']['message'] = 'checkpoint_required';
        } else {
            if (!empty($maxId)) {
                $maxId = '?max_id=' . $maxId;
            }
            if (mt_rand(0, 1) == 1) {
                $result = $this->request("feed/user/" . $feedId . "/" . $maxId);
                $result2 = $this->request("feed/user/" . $feedId . "/story/");
            } else {
                $result2 = $this->request("feed/user/" . $feedId . "/story/");
                $result = $this->request("feed/user/" . $feedId . "/" . $maxId);
            }
        }
        $this->request("users/" . $feedId . "/info/");
        if (empty($result) && !empty($result2)) {
            $result = $result2;
        }
        
        return $result;
    }
    
    
    public function oldLike($mediaId)
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
    
    public function like($mediaId, $userId, $userName, $moduleId)
    {
        $moduleName = 'photo_view_profile';
        switch ($moduleId) {
            case (1):
                $moduleName = 'photo_view_profile';
                break;
            case (2):
                $moduleName = 'video_view_profile';
                break;
        }
        $data = [
            'module_name' => $moduleName,
            'media_id' => $mediaId,
            '_csrftoken' => $this->csrftoken,
            'username' => $userName,
            'user_id' => $userId,
            '_uid' => $this->accountId,
            '_uuid' => $this->guid,
        
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
        if (file_exists("/home/insta/cookies/" . $this->username . "-cookies.dat")) {
            unlink("/home/insta/cookies/" . $this->username . "-cookies.dat");
        }
        $this->guid = $guid;
        // $this->guid = '466dafce-f3e3-492b-f7d9-245ca0d3115c';
        // $phoneId = '485591b1-9ca8-4ed6-a1ff-289980b7fa37';
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
            if (@$sync[1]['message'] === 'checkpoint_required') {
                //                echo "\nLimit fixer----------------------------------------\n";
                //                $checkPoint = new Checkpoint($this->username);
                //                $checkPoint->proxy = $this->proxy;
                //                $checkPoint->accountId = $this->accountId;
                //                $checkPoint->request($sync[1]['checkpoint_url']);
                //                $checkPoint->request('https://www.instagram.com/challenge/');
                //
                //
                //                echo "\nEND Limit fixer----------------------------------------\n";
                if ($sync[1]['error_type'] === 'inactive user') {
                    Users::where([
                        'guid' => $guid,
                        'phoneId' => $phoneId,
                        'deviceId' => $device_id
                    ])->update(['ban' => 4]);
                } else {
                    Users::where([
                        'guid' => $guid,
                        'phoneId' => $phoneId,
                        'deviceId' => $device_id
                    ])->update(['ban' => 1]);
                }
                die("Account banned");
            }
            
            $i++;
        }
        if ($tokenResult == false || $tokenResult == '') {
            Users::where(['guid' => $guid, 'phoneId' => $phoneId, 'deviceId' => $device_id])->update(['ban' => 2]);
            exit('empty token');
        }
        
        $this->csrftoken = $tokenResult;
        $this->fetchHeadersSingUp();
        
        $data = [
            'phone_id' => $phoneId,
            //'_csrftoken' => $this->csrftoken,
            'username' => $this->username,
            'guid' => $this->guid,
            'device_id' => $device_id,
            'password' => $password,
            'login_attempt_count' => 0
        ];
        
        $data = json_encode($data);
        $resultLogin = '';
        $i = 0;
        while ($resultLogin == '') {
            $login = $this->request('accounts/login/', $data);
            $resultLogin = $login[1];
            if (empty($resultLogin)) {
                $resultLogin = '';
            }
            if ($i === 5) {
                $resultLogin = false;
            }
            $i++;
        }
        $this->request('feed/timeline/?is_prefetch=0&seen_posts=&phone_id=' . $this->phone_id . '&battery_level=' . mt_rand(23,
                100) . '&timezone_offset=3600&is_pull_to_refresh=0&unseen_posts=&is_charging=' . mt_rand(0, 1));
        if (@$resultLogin['error_type'] === "inactive user" || @$resultLogin['error_type'] === 'invalid_user') {
            Users::where(['guid' => $guid, 'phoneId' => $phoneId, 'deviceId' => $device_id])->update(['ban' => 4]);
            die("Account banned");
        }
        print_r($resultLogin);
        $this->accountId = @$resultLogin['logged_in_user']['pk'];
        $this->rank_token = $this->accountId . '_' . $this->guid;
        Users::where([
            'guid' => $guid,
            'phoneId' => $phoneId,
            'deviceId' => $device_id
        ])->update(['csrftoken' => $tokenResult, 'accountId' => @$resultLogin['logged_in_user']['pk']]);
        $newsInbox = $this->request('news/inbox/?activity_module=all');
        if (@$newsInbox[1]['message'] === 'checkpoint_required') {
            Checkpoint::checkPoint($newsInbox, $this);
        } elseif (@$newsInbox[1]['message'] === 'login_required') {
            Users::where(['guid' => $guid, 'phoneId' => $phoneId, 'deviceId' => $device_id])->update(['ban' => 1]);
        }
        
        return $resultLogin;
    }
    
    
    public function changeProfilePicture($photo)
    {
        $resultEdit = true;
        if (!empty($photo)) {
            $resultEdit = $this->request('accounts/change_profile_picture/', null,
                $photo);
            print_r($resultEdit);
        }
        
        return $resultEdit;
    }
    
    //    public function uploadPhoto($photo)
    //    {
    //        if (!empty($photo)) {
    //            $resultEdit = $this->request('upload/photo/', null, null, $photo);
    //            print_r($resultEdit);
    //        }
    //    }
    
    public function uploadPhoto(
        $photo,
        $caption = null,
        $customPreview = null,
        $location = null,
        $filter = null,
        $reel_flag = false
    ) {
        $endpoint = 'upload/photo/';
        $boundary = $this->guid;
        $upload_id = number_format(round(microtime(true) * 1000), 0, '', '');
        $fileToUpload = file_get_contents($photo);
        
        $bodies = [
            [
                'type' => 'form-data',
                'name' => 'upload_id',
                'data' => $upload_id,
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
                'data' => $fileToUpload,
                'filename' => 'pending_media_' . number_format(round(microtime(true) * 1000), 0, '', '') . '.jpg',
                'headers' => [
                    'Content-Transfer-Encoding: binary',
                    'Content-Type: application/octet-stream',
                ],
            ],
        ];
        $data = $this->buildBody($bodies, $boundary);
        $headers = [
            'X-IG-Capabilities: ' . $this->xIgCapabilities,
            'X-IG-Connection-Type: WIFI',
            'Content-Type: multipart/form-data; boundary=' . $boundary,
            'Content-Length: ' . strlen($data),
            'Accept-Language: en-US',
            'Accept-Encoding: gzip, deflate',
            'Connection: close',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://i.instagram.com/api/v1/' . $endpoint);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/home/insta/cookies/" . $this->username . "-cookies.dat");
        curl_setopt($ch, CURLOPT_COOKIEJAR, "/home/insta/cookies/" . $this->username . "-cookies.dat");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $proxy = explode(";", $this->proxy);
        curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
        if (!empty($proxy[1])) {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[1]);
        }
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        curl_setopt($ch, CURLOPT_PROXYTYPE, 7);
        $resp = curl_exec($ch);
        $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($resp, 0, $header_len);
        $body = substr($resp, $header_len);
        curl_close($ch);
        print_r($body);

        //print_r(json_decode($body, true));

        return [$header, json_decode($body, true)];
    }


    public function oldEdit($biography, $url, $phoneId, $firstName, $email)
    {
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
            die('no token');
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
        ];
        if (SystemSettings::get('isPrivate') === 1) {
            $data['is_private'] = true;
        }
        $data = json_encode($data);

        return $this->request('accounts/edit_profile/', $data);
    }

    public function edit($biography, $url, $phoneId, $firstName, $email)
    {
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
            die('no token');
        }

        $this->csrftoken = $tokenResult;
        sleep(rand(0, 2));

        print_r($this->request('accounts/current_user/?edit=true'));
        sleep(rand(1, 4));
        /*
         * {"external_url":"https://price.yt/1f972c0d","gender":"3","phone_number":"","_csrfto
ken":"2pTCvhlokIZR8fOZ16nRK2MJKAL2rMii","username":"bagirus11","first_name":"abgymnic","_uid":"5374297804","biography":"Уникальный пояс для тренировки
мышц","_uuid":"419fcce5-b663-4b31-80c0-5586609f730f","email":"bagirus11@gmail.com"}
         */
        $data = [
            'external_url' => $url,
            'gender' => 2,
            'phone_number' => '',
            '_csrftoken' => $this->csrftoken,
            'username' => $this->username,
            'first_name' => $firstName,
            '_uid' => $this->accountId,
            '_uuid' => $this->guid,
            'biography' => $biography,
            'email' => $email
        ];
        if (SystemSettings::get('isPrivate') === 1) {
            $data['is_private'] = true;
        }
        $data = json_encode($data);

        return $this->request('accounts/edit_profile/', $data);
    }

    public function create()
    {
        $domainMail = [
            'mail.com',
            'gmail.com',
            'hotmail.com',
            'icloud.com',
            'yahoo.com',
            'free.fr',
            'mail707.com',
            'doanart.com'
        ];
        $faker = Factory::create();
        if (mt_rand(0, 4) == 1) {
            $uname = $faker->userName . range('a', 'z')[rand(0, 26)] . range('a', 'z')[rand(0, 26)];
        } elseif (mt_rand(0, 1) == 0) {
            $uname = $faker->firstNameFemale . range('a', 'z')[rand(0, 26)] . range('a', 'z')[rand(0,
                    26)] . $faker->lastName . range('a',
                    'z')[rand(0, 26)] . range('a', 'z')[rand(0, 26)];
        } elseif (mt_rand(0, 1) == 0) {
            $uname = $faker->firstNameFemale . range('a',
                    'z')[rand(0, 26)] . range('a', 'z')[rand(0, 26)] . $faker->lastName;
        } elseif (mt_rand(0, 1) == 0) {
            $uname = $faker->firstNameFemale . $faker->lastName . range('a',
                    'z')[rand(0, 26)] . range('a', 'z')[rand(0, 26)];
        } else {
            $uname = $faker->lastName . range('a',
                    'z')[rand(0, 26)] . $faker->firstNameFemale . rand(1100, 2017);
        }
        $uname = mb_strtolower($uname);
        if (rand(0, 1) == 1) {
            $this->username = str_replace(".", "", $uname);
        } else {
            $this->username = $uname;
        }
        $this->password = strtolower(substr(md5(number_format(microtime(true), 7, '', '')), mt_rand(15, 20)));
        $this->name = $faker->firstNameFemale . range('a', 'z')[rand(0, 26)];// . " " . $faker->lastName;
        if (rand(0, 1) == 1) {
            $this->name .= " " . $faker->lastName . range('a', 'z')[rand(0, 26)];
        }

        //$email = $faker->email;
        if (mt_rand(0, 2) == 0) {
            $email = explode("@", $faker->email);
            $email = implode(range('a', 'z')[rand(0, 26)] . mt_rand(0, 999) . "@", $email);
        } elseif (mt_rand(0, 2) == 0) {
            $email = str_replace(" ", ".", $this->name) . range('a', 'z')[rand(0, 26)] . mt_rand(0, 999) . "@gmail.com";
        } elseif (mt_rand(0, 1) == 0) {
            $email = str_replace(" ", ".", $this->username) . range('a', 'z')[rand(0,
                    26)] . mt_rand(0, 999) . "@" . $domainMail[mt_rand(0,
                    count($domainMail) - 1)];
        } elseif (mt_rand(0, 1) == 0) {
            $email = str_replace(" ", ".", $this->name) . range('a', 'z')[rand(0,
                    26)] . mt_rand(0, 999) . "@" . $domainMail[mt_rand(0,
                    count($domainMail) - 1)];
        } elseif (mt_rand(0, 1) == 0) {
            $email = str_replace(" ", ".", $this->name) . mt_rand(0, 9999) . "@" . $domainMail[mt_rand(0,
                    count($domainMail) - 1)];
        } else {
            $email = $uname . range('a', 'z')[rand(0, 26)] . mt_rand(0, 999) . "@gmail.com";
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

        sleep(rand(10, 20));
        $checkEmail = $this->checkEmail($email, $qe_id, $waterfall_id);

        print_r($checkEmail);
        if (isset($checkEmail[1]['message']) && $checkEmail[1]['message'] == 'Sorry, an error occured') {
            die('Error. Ip ban?');
        }
        sleep(rand(10, 15));
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
            sleep(rand(10, 15));
            print_r($this->usernameSuggestions($usernameTmp4, $email, $waterfall_id));
        }
        sleep(rand(11, 19));
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

        //        sleep(rand(3, 7));
        //        print_r($this->usernameSuggestions($usernameTmp2, $email, $waterfall_id));
        //
//        if (rand(0, 1) == 1) {
//            sleep(rand(3, 8));
//            print_r($this->usernameSuggestions($usernameTmp1, $email, $waterfall_id));
//        }
//        sleep(rand(4, 9));
        $finalName = $this->usernameSuggestions($this->username, $email, $waterfall_id);
        print_r($finalName);
        //$this->username = $finalName[1]['suggestions'][rand(0, 11)];
        // $finalName = $this->usernameSuggestions($this->username, $email, $waterfall_id);
        print_r($finalName);
        echo "SET name: " . $this->username . "\n";

        sleep(rand(2, 8));
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
                'userAgent' => $this->userAgent
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
            'force_sign_up_code' => '',
            'waterfall_id' => $waterfall_id,
            'qs_stamp' => "",
            'password' => $this->password,
        ];

        $data = json_encode($data);

        return $this->request('accounts/create/', $data);
    }

    /**
     * @param      $latitude
     * @param      $longitude
     * @param null $query
     *
     * @return array
     */
    public function searchLocation(
        $latitude,
        $longitude,
        $query = null
    ) {
        $data = [
            'rank_token' => $this->rank_token,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];
        if (is_null($query)) {
            $data = array_merge($data, ['timestamp' => time()]);
        } else {
            $data = array_merge($data, ['search_query' => $query]);
        }

        return $this->request('location_search/', $data);
    }

    /**
     * @param      $query
     * @param null $count
     *
     * @return array
     */
    public function searchFBLocation(
        $query,
        $count = null
    ) {
        $data = [
            'rank_token' => $this->rank_token,
            'query' => $query,
        ];
        if (!is_null($count)) {
            $data = array_merge($data, ['count' => $count]);
        }

        return $this->request('fbsearch/places/', $data);
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
    public function fetchHeadersSingUp()
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
            //'_csrftoken' => $this->csrftoken,
            'email' => $email,
            'qe_id' => $uuid,
            'waterfall_id' => $waterfall_id,
        ]);

        return $this->request('users/check_email/', $data);
    }

    /**
     * @return array
     */
    public function syncRegister()
    {
        $syncData = json_encode([
            "id" => $this->guid,
            "experiments" => 'ig_android_analytics_data_loss,ig_android_gmail_oauth_in_reg,ig_android_phoneid_sync_interval,ig_android_non_fb_sso,ig_android_auto_submit_verification_code,ig_android_phone_prefill_for_m_in_reg,ig_android_confirmation_code_registration,ig_fbns_push,ig_android_profile_photo_nux_holdout,ig_android_background_phone_confirmation_v2,ig_android_remove_ci_option_for_fb_reg,ig_android_merge_fb_and_ci_friends_page,ig_fbns_blocked,ig_android_contact_point_triage,ig_android_gmail_oauth_in_access,ig_android_prefill_phone_email_login,ig_android_one_click_in_old_flow'
        ]);

        return $this->request('qe/sync/', $syncData);
    }

    public function sync()
    {
        $syncData = json_encode([
            "id" => $this->guid,
            "experiments" => 'ig_android_direct_mutually_exclusive_experiment_universe,ig_android_ads_heatmap_overlay_universe,ig_android_confirmation_code_edit_profile,ig_android_universe_video_production,ig_android_direct_composer,ig_android_capture_hands_free_mode,ig_android_show_your_story_when_empty_universe_2,ig_android_video_playback_bandwidth_threshold,ig_android_ad_drop_cookie_early,ig_android_search_null_state_2,ig_android_live_analytics,ig_android_snippets_feed_tooltip,ig_android_direct_link_preview,ig_android_video_captions_universe,ig_android_follow_request_text_buttons_v2,ig_android_ontact_invite_universe,ig_android_send_direct_typing_indicator,ig_android_business_conversion_value_prop_navigate,ig_android_ad_zero_latency_logging_universe,ig_android_checkbox_instead_of_button_as_follow_affordance_universe,ig_android_share_spinner,ig_android_pbia_normal_weight_universe,ig_android_etag_layer,ig_android_follow_button_redesign,ig_android_insta_video_jump_ahead,android_instagram_prefetch_suggestions_universe,ig_android_follow_search_bar,ig_android_insta_video_universe,ig_android_ad_rn_preload_universe,ig_ranking_following,ig_android_universe_reel_video_production,id_android_http_stack_experiment_2016,ig_android_sfplt,ig_offline_profile,ig_android_stories_weblink_consumption,ig_android_direct_blue_tab,ig_android_dynamic_image_disk_cache_size_mb,ig_android_async_network_tweak_universe,ig_video_copyright_whitelist,ig_android_feed_reshare_button_nux,ig_android_exoplayer_stories,ig_android_swipe_navigation_x_angle_universe,ig_android_offline_mode_holdout,ig_android_non_square_first,ig_android_insta_video_drawing,ig_android_react_native_usertag,ig_android_swipeablefilters_universe,ig_android_video_cache_policy,ig_fbns_preload_default,ig_android_blocked_list,ig_android_react_native_promote,ig_android_drafts_universe,ig_android_family_bridge_discover,ig_android_insta_video_consumption_prefetch,ig_android_profile,ig_android_high_res_upload_2,ig_android_inline_gallery_universe,ig_android_remove_followers_universe,ig_android_mentions_dismiss_rule,ig_android_ad_metadata_behavior_universe,ig_android_immersive_viewer,ig_android_mqtt_skywalker,ig_fbns_push,ig_android_search_client_matching,ig_android_feed_preview_blur_fix,ig_android_react_native_universe,ig_android_direct_drawing_in_quick_cam_universe,ig_android_boomerang_feed_attribution,ig_android_video_loopcount_int,ig_android_rendering_controls,ig_android_os_version_blocking,ig_android_use_software_layer_for_kc_drawing_universe,ig_android_ad_watchbrowse_universe,ig_android_react_native_ota,ig_android_snippets_profile_nux,ig_android_view_count_decouple_likes_universe,ig_android_disk_usage,ig_android_swipeable_filters_blacklist,ig_video_use_sve_universe,ig_android_mute_story,ig_fbns_blocked,ig_android_stories_teach_gallery_location,ig_feed_holdout_universe,ig_android_empty_feed_redesign,ig_android_pending_request_search_bar,ig_android_marauder_update_frequency,ig_android_fb_topsearch_sgp_fork_request,ig_android_exoplayer_http_stack,ig_android_organic_insights_django,ig_android_enable_share_to_messenger,ig_android_preview_capture,ig_android_activity_follow_button,ig_android_direct_raven,ig_android_su_activity_feed,ig_android_direct_send_auto_retry_universe,ig_android_media_favorites,ig_android_channels_home,ig_android_ad_holdout_16h2m1_universe,ig_android_boomerang_entry,ig_android_business_conversion_social_context,ig_android_video_reuse_surface,android_ig_fbns_kill_switch,ig_android_react_native_universe_kill_switch,ig_android_stories_book_universe,ig_android_business_promotion,liger_instagram_android_univ,ig_android_facebook_twitter_profile_photos,ig_android_ad_always_send_ad_attribution_id_universe,ig_android_anrwatchdog,ig_android_full_user_detail_endpoint,ig_android_2fac,ig_explore_v3_android_universe,ig_android_feed_like_social_context,ig_android_offline_likes_v2,ig_android_share_to_whatsapp,ig_fbns_dump_ids,ig_android_direct_typing_indicator,ig_android_search,ig_android_stories_max_video_duration,ig_android_capture_boomerang_mode,ig_android_http_stack_experiment_2016,ig_android_invite_popup_universe,ig_show_promote_button_in_feed,ig_android_ad_new_intent_to_highlight_universe,ig_video_max_duration_qe_preuniverse,ig_android_dv2_realtime_private_share,ig_creation_growth_holdout,ig_request_cache_layer,ig_android_direct_plus_button,ig_android_explore_stories,ig_android_mark_reel_seen_on_Swipe_forward,ig_fbns_shared,ig_android_capture_slowmo_mode,ig_android_chaining_teaser_animation,ig_android_video_single_surface,ig_android_asset_picker,ig_android_ad_new_sponsored_label_universe,ig_android_video_download_logging,ig_android_follows_you_badge,ig_android_last_edits,ig_android_exoplayer_4142,ig_android_feed_pill,ig_android_disable_chroma_subsampling,ig_android_feed_cold_start,ig_android_fix_ise_two_phase,ig_family_bridges_holdout_universe,ig_android_ad_video_autoplay_fix_universe,ig_android_following_follower_social_context,ig_android_ad_carousel_redesign_universe,ig_android_stories_use_gl_drawing,ig_android_profile_photo_as_media,ig_android_direct_emoji_picker,ig_android_promotion_toggle_v2,ig_android_newsfeed_large_avatar,ig_android_business_conversion_value_prop,ig_android_new_media_saver,ig_android_disable_comment_public_test,ig_android_user_detail_endpoint,ig_android_infinite_scrolling_launch,ig_android_insta_video_consumption_abr,ig_android_insta_video_consumption,ig_in_feed_commenting,ig_android_add_to_last_post,ig_android_snippets,ig_android_direct_swipe_to_inbox,ig_android_sidecar,ig_android_exoplayer_holdout,ig_android_stories_weblink_creation,ig_android_progressive_jpeg,ig_android_histogram_reporter,ig_android_contact_button_color,ig_invite_invite_in_nux_universe,ig_android_network_cancellation,ig_android_post_auto_retry_v7_21,ig_android_ad_holdout_16m5_universe,ig_android_memory_improve_universe,ig_android_samsung_app_badging,ig_android_family_bridge_bookmarks,ig_android_disable_comment,ig_android_fetch_reel_tray_on_resume_universe'
        ]);

        return $this->request('qe/sync/', $syncData);
    }

    /**
     * @param      $method
     * @param null $data
     *
     * @return array
     */
    public function request($method, $data = null, $file = null, $profilePhoto = null)
    {
        echo "Request: \n";
        echo $method . "\n";
        print_r($data);
        echo "\n\nResult: \n";
        $ch = curl_init();
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
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
        } else {
            $headers = [
                //  'User-Agent: ' . $this->userAgent,
                'Connection: keep-alive',
                "X-IG-Connection-Type: WIFI",
                "X-IG-Capabilities: " . $this->xIgCapabilities,
                'Accept-Encoding: gzip, deflate',
                'Accept-Language: en-US',
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        }

        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/home/insta/cookies/" . $this->username . "-cookies.dat");
        curl_setopt($ch, CURLOPT_COOKIEJAR, "/home/insta/cookies/" . $this->username . "-cookies.dat");
        if ($file) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        } elseif ($data) {
            $hash = hash_hmac('sha256', $data, $this->igKey);
            $postData = 'signed_body=' . $hash . '.' . urlencode($data) . '&ig_sig_key_version=' . $this->igVersion;
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        $proxy = explode(";", $this->proxy);
        curl_setopt($ch, CURLOPT_PROXY, $proxy[0]);
        if (!empty($proxy[1])) {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy[1]);
        }
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        
        $resp = curl_exec($ch);
        $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($resp, 0, $header_len);
        $body = substr($resp, $header_len);
        $info = curl_getinfo($ch);
        curl_close($ch);
        echo "\n\nHeaders:\n";
        print_r($info);
        echo "\n\nBody:";
        print_r($body);
        
        //print_r(json_decode($body, true));
        
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