<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 18.05.15
 * Time: 10:34
 */

function gen_uuid()
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
    
    $uuid = sprintf('%08x-%04x-%04x-%02x%02x-%02x%02x%02x%02x%02x%02x',
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

$username = 'doorg' . rand(0, 999999);
$name = 'doorg doorgd';
$email = 'doorg' . rand(0, 999999) . '@gmail.com';
$password = "f3546h7rtj6rhgrfeds";

$megaRandomHash = md5(number_format(microtime(true), 7, '', ''));
$device_id = 'android-' . strtolower(substr($megaRandomHash, 16));

$phone_id = strtolower(gen_uuid());
$waterfall_id = strtolower(gen_uuid());
$guid = strtolower(gen_uuid());

$uuid = strtolower(gen_uuid());

$igKey = '2f6dcdf76deb0d3fd008886d032162a79b88052b5f50538c1ee93c4fe7d02e60';
$igV = '4';


//sync:
$syncData = json_encode([
    "id" => $guid,
    "experiments" => 'ig_android_prefill_phone_email_login_m_devices,ig_android_username_hint_copy,ig_android_ci_opt_in_at_reg,ig_android_one_click_in_old_flow,ig_androi
d_merge_fb_and_ci_friends_page,ig_android_reg_back_dialog,ig_android_profile_photo_nux,ig_android_remove_fb_nux_if_no_fb_installed,ig_android_non_
fb_sso,ig_android_mandatory_full_name,ig_android_iconless_reg,ig_android_analytics_data_loss,ig_android_prefill_phone_email_login,ig_fbns_blocked,
ig_android_contact_point_triage,ig_android_remove_ci_option_for_fb_reg,ig_android_auto_submit_verification_code,ig_android_prefill_phone_number,ig
_android_show_fb_social_context_in_nux,ig_fbns_push,ig_android_background_phone_confirmation,ig_android_phoneid_sync_interval,ig_android_login_lan
guage_picker'
]);
$tokenResult = "";
//Sync:
$hash = hash_hmac('sha256', $syncData, $igKey);
$sync = requestGet('qe/sync/', 'ig_sig_key_version=' . $igV . '&signed_body=' . $hash . '.' . urlencode($syncData),
    $username);
print_r($sync);

if (preg_match('#Set-Cookie: csrftoken=([^;]+)#', $sync[0], $token)) {
    $tokenResult = $token[1];
}
echo "Result: " . $tokenResult;
print_r($tokenResult);


//Check e-mail:
sleep(rand(5, 10));

$data = json_encode([
    '_csrftoken' => $tokenResult,
    'email' => $email,
    'qe_id' => $uuid,
    'waterfall_id' => $waterfall_id,
]);

$hash = hash_hmac('sha256', $data, $igKey);
$hash = 'signed_body=' . $hash . '.' . urlencode($data) . '&ig_sig_key_version=' . $igV;
$result = requestGet('users/check_email/', $hash, $username);
print_r($result);

sleep(rand(2, 4));

$token = requestGet("si/fetch_headers/?guid=" . mb_strtolower(str_replace("-", "", $guid)) . "&challenge_type=singup",
    null,
    $username);
echo "1:\n";
print_r($token[0]);
echo "2:\n";
print_r($token[1]);
$tokenResult = "";

if (preg_match('#Set-Cookie: csrftoken=([^;]+)#', $token[0], $token)) {
    $tokenResult = $token[1];
}
echo "Result: " . $tokenResult;

sleep(rand(5, 10));
$usernameTmp = substr($username, 0, -round(mb_strlen($username) - 5, mb_strlen($username) - 1));
//Check username:
$data = json_encode([
    '_csrftoken' => $tokenResult,
    'name' => $usernameTmp,
    'email' => $email,
    'waterfall_id' => $waterfall_id,

]);
$hash = hash_hmac('sha256', $data, $igKey);
$hash = 'signed_body=' . $hash . '.' . urlencode($data) . '&ig_sig_key_version=' . $igV;


$result = requestGet('accounts/username_suggestions/', $hash, $username);
print_r($result);

sleep(rand(1, 2));

$token = requestGet("si/fetch_headers/?guid=" . mb_strtolower(str_replace("-", "", $guid)) . "&challenge_type=singup",
    null,
    $username);
echo "1:\n";
print_r($token[0]);
echo "2:\n";
print_r($token[1]);
$tokenResult = "";

if (preg_match('#Set-Cookie: csrftoken=([^;]+)#', $token[0], $token)) {
    $tokenResult = $token[1];
}
echo "Result: " . $tokenResult;

sleep(rand(2, 4));

$usernameTmp = substr($username, 0, -round(1, mb_strlen($username) - 3));
//Check username:
$data = json_encode([
    '_csrftoken' => $tokenResult,
    'name' => $usernameTmp,
    'email' => $email,
    'waterfall_id' => $waterfall_id,

]);
$hash = hash_hmac('sha256', $data, $igKey);
$hash = 'signed_body=' . $hash . '.' . urlencode($data) . '&ig_sig_key_version=' . $igV;

$result = requestGet('accounts/username_suggestions/', $hash, $username);
print_r($result);

sleep(rand(1, 2));

//Check username:
$data = json_encode([
    '_csrftoken' => $tokenResult,
    'name' => $username,
    'email' => $email,
    'waterfall_id' => $waterfall_id,
]);
$hash = hash_hmac('sha256', $data, $igKey);
$hash = 'signed_body=' . $hash . '.' . urlencode($data) . '&ig_sig_key_version=' . $igV;

$result = requestGet('accounts/username_suggestions/', $hash, $username);
print_r($result);

sleep(rand(1, 2));
//Register:
$data = [
    'allow_contacts_sync' => true,
    'phone_id' => $phone_id,
    '_csrftoken' => $tokenResult,
    'username' => $username,
    'first_name' => $name,
    'guid' => $guid,
    'device_id' => $device_id,
    'email' => $email,
    'force_sign_up_code' => '',
    'waterfall_id' => $waterfall_id,
    'qs_stamp' => "",
    'password' => $password,
];

$data = json_encode($data);
//17457ca87fa243a07a7c78e03085e42fd36f1e3c2bfe7217ab35b669cdca7cd2.{\"username\": \"newusername\", \"first_name\": \"Ivan\", \"waterfall_id\": \"dcd34a15bf244b9c8274d8031b06aea5\", \"_csrftoken\": \"yYmiLwiEhW8UIHVi1LmxLyS9pd7GRlZq\", \"password\": \"asdasd\", \"email\": \"newusername@gmail.com\", \"device_id\": \"CB8AF1A6-6ED0-4901-AF00-5B5AFD461E45\"}
$hash = hash_hmac('sha256', $data, $igKey);
$hash = 'signed_body=' . $hash . '.' . urlencode($data) . '&ig_sig_key_version=' . $igV;

$result = requestGet('accounts/create/', $hash, $username);
print_r($result);

//if ($response->isAccountCreated()) {
//    $this->username_id = $response->getUsernameId();
//    $this->settings->set('username_id', $this->username_id);
//    preg_match('#Set-Cookie: csrftoken=([^;]+)#', $header, $match);
//    $token = $match[1];
//    $this->settings->set('token', $token);
//}

function requestGet($endpoint, $post = null, $username)
{
    $userAgent = 'Instagram 9.7.0 Android (17/4.2.2; 240dpi; 480x800; samsung; GT-S7270; logan; hawaii_ss_logan; ru_RU)';
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, "https://i.instagram.com/api/v1/" . $endpoint);
    //        $headers = [
    //            "X-IG-Connection-Type: WiFi\r\n",
    //            "X-IG-Capabilities: 3boBAA==\r\n",
    //        ];
    //
    //        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
//    if ($post != null && !empty($post)) {
//        $headers = [
//            "X-IG-Connection-Type: WIFI",
//            "X-IG-Capabilities: 3Ro=",
//            'Accept-Language: ru-RU, en-US',
//            'Connection: keep-alive',
//            //'Accept-Encoding: gzip, deflate',
//            'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
//        ];
//    } else {
//        $headers = [
//            "X-IG-Connection-Type: WIFI",
//            "X-IG-Capabilities: 3Ro=",
//           // 'Accept-Encoding: gzip, deflate, sdch',
//            'Accept-Language: ru-RU, en-US',
//            'Connection: keep-alive',
//        ];
//    }
  //  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_COOKIEFILE, "$username-cookies.dat");
    curl_setopt($ch, CURLOPT_COOKIEJAR, "$username-cookies.dat");
    
    
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    curl_setopt($ch, CURLOPT_PROXY, "46.105.124.207:5012");
    //curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyAuth);
    //    if ($this->proxy) {
    //        curl_setopt($ch, CURLOPT_PROXY, $this->proxyHost);
    //        if ($this->proxyAuth) {
    //            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyAuth);
    //        }
    //    }
    
    $resp = curl_exec($ch);
    $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($resp, 0, $header_len);
    $body = substr($resp, $header_len);
    
    curl_close($ch);
    
    echo "REQUEST: $endpoint\n";
    if (!is_null($post)) {
        if (!is_array($post)) {
            echo "DATA: $post\n";
        }
    }
    echo "RESPONSE: $body\n\n";
    
    return [$header, json_decode($body, true)];
}