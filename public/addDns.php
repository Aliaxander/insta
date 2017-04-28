<?php
/**
 * Created by OxGroup.
 * User: Aliaxander
 * Date: 28.04.17
 * Time: 11:24
 *
 * @author    Aliaxander
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://oxgroup.media/
 */
$ip = '217.182.242.108';
$email = 'maste.craft@gmail.com';
$password = 'LAuuEo9Seevwgjx8';

$cmd = 'curl -X POST https://api.dnspod.com/Auth -d \'login_email=' . $email . '&login_password=' . $password . '&format=json\'';
exec($cmd, $result);
$result = json_decode($result[0]);
$token = $result->user_token;

$files = file(__DIR__ . '/domains.txt');
foreach ($files as $domain) {
    $domain = str_replace(["\n", ' '], '', $domain);
    echo 'Domain: ' . $domain . "\n";
    $cmd = 'curl -X POST https://api.dnspod.com/Domain.Create -d \'user_token=' . $token . '&domain=' . $domain . '&format=json\'';
    echo $cmd . "\n";
    exec($cmd, $result);
    $result = json_decode($result[0]);
    print_r($result);
    $id = @$result->domain->id;
    if (empty($id)) {
        $id = @$result->record->id;
    }
    echo "set ID:" . $id;
    if (isset($id)) {
        echo "\n\n";
        $cmd = 'curl -X POST https://api.dnspod.com/Record.Create -d \'user_token=' . $token . '&format=json&domain_id=' . $id . '&sub_domain=@&record_type=A&record_line=default&value=' . $ip . '\'';
        exec($cmd, $result);
        print_r($result);
        $cmd = 'curl -X POST https://api.dnspod.com/Record.Create -d \'user_token=' . $token . '&format=json&domain_id=' . $id . '&sub_domain=www&record_type=A&record_line=default&value=' . $ip . '\'';
        exec($cmd, $result);
        print_r($result);
        $cmd = 'curl -X POST https://api.dnspod.com/Record.Create -d \'user_token=' . $token . '&format=json&domain_id=' . $id . '&sub_domain=*&record_type=A&record_line=default&value=' . $ip . '\'';
        exec($cmd, $result);
        print_r($result);
    }
    echo "\n\n------------------END-------------------\n\n";
}