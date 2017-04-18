<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 15.08.15
 * Time: 15:16
 */

namespace Acme\Console\Command;

use Faker\Factory;

use OxApp\models\Domains;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FreenomReger
 *
 * @package Acme\Console\Command
 */
class FreenomReger extends Command
{
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('freenom:reg')
            ->setDescription('count')->addArgument(
                'count',
                InputArgument::OPTIONAL,
                'count domains'
            )->setDescription('IP')->addArgument(
                'ip',
                InputArgument::OPTIONAL,
                'ip'
            );
    }
    
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        print_r($input);
        $ip = $input->getArgument('ip');
        $cmd = 'curl -X POST https://api.dnspod.com/Auth -d \'login_email=maste.craft@gmail.com&login_password=LAuuEo9Seevwgjx8&format=json\'';
        exec($cmd, $result);
        $result = json_decode($result[0]);
        $token = $result->user_token;
        
        $faker = Factory::create();
        $domains = ['.tk', '.ml', '.ga', '.cf', '.gq'];
        $email = "domainreg480@gmail.com";
        $password = "m9Rgx7UpoTiJjM8k";
        $params['account_email'] = $email;
        $params['account_password'] = $password;
        //freenom_SaveContactDetails($params);
        $params['ns1'] = "a.dnspod.com";
        $params['ns2'] = "b.dnspod.com";
        $params['ns3'] = "c.dnspod.com";
        
        require(__DIR__ . "/../../config.php");
        for ($i = 0; $i < $input->getArgument('count'); $i++) {
            $uname = $faker->userName . rand(1950, 2017);
            $uname = str_replace([".", "-", ")"], "", $uname);
            $params['domain'] = $uname . $domains[rand(0, 4)];
            echo "Test: " . $params['domain'] . "\n";
            $result = $this->freenom_RegisterFreeDomain($params);
            print_r($result);
            if (!isset($result->error) && $result->domain->status !== "NOT AVAILABLE") {
                $cmd = 'curl -X POST https://api.dnspod.com/Domain.Create -d \'user_token=' . $token . '&domain=' . $params['domain'] . '&format=json\'';
                exec($cmd, $result);
                $result = json_decode($result[0]);
                print_r($result);
                $id = @$result->domain->id;
                echo $id;
                $cmd = 'curl -X POST https://api.dnspod.com/Record.Create -d \'user_token=' . $token . '&format=json&domain_id=' . $id . '&sub_domain=@&record_type=A&record_line=default&value=' . $ip . '\'';
                exec($cmd, $result);
                Domains::add(['domain' => $params['domain']]);
            }
        }
        
        return $output->writeln("Complite");
    }
    
    public function freenom_RegisterFreeDomain($params)
    {      // {{{
        $domainname = $params['domain'];
        $params['function'] = 'domain/register';
        
        $qstring = array(
            "function" => 'domain/register',
            "email" => $params['account_email'],
            "password" => $params['account_password'],
            "domainname" => $domainname,
            "idshield" => "enabled",
            "domaintype" => "FREE",
            "ipaddress" => "217.182.242.108",
            "period" => '12M',
            "method" => "POST",
        );
        
        if (isset($params['ns1'])) {
            $qstring["nameserver1"] = $params['ns1'];
        }
        if (isset($params['ns2'])) {
            $qstring["nameserver2"] = $params['ns2'];
        }
        if (isset($params['ns3'])) {
            $qstring["nameserver3"] = $params['ns3'];
        }
        if (isset($params['ns4'])) {
            $qstring["nameserver4"] = $params['ns4'];
        }
        if (isset($params['ns5'])) {
            $qstring["nameserver5"] = $params['ns5'];
        }
        $query = $this->freenom_buildquery($qstring);
        
        return $this->freenom_put($query, "POST", $params);
    }
    
    public function freenom_put($xml, $callmethod, $params)
    {  // {{{
        $xurl = "http://api.freenom.com/v2/" . $params["function"] . ".json";
        $headers = array(
            "Accept: application/x-www-form-urlencoded",
            "Content-Type: application/x-www-form-urlencoded"
        );
        $session = curl_init();
        curl_setopt($session, CURLOPT_URL, $xurl);
        curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($session, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($session, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($session);
        print_r($response);
        if (curl_errno($session)) {
            return array(
                'error' => 'curl error: ' . curl_errno($session) . " - " . curl_error($session),
                'status' => 'error'
            );
            curl_close($session);
            
            return $data;
        }
        curl_close($session);
        sleep(1);
        
        return json_decode($response);
    }
    
    public function freenom_buildquery($formdata)
    {            // {{{
        $query = "";
        foreach ($formdata as $k => $v) {
            if (substr($k, 0, 10) == "nameserver") {
                $k = "nameserver";
            }
            $query .= "" . $k . "=" . urlencode($v) . "&";
        }
        
        return $query;
    }
}