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
use OxApp\models\FreenomAccounts;
use OxApp\models\FreenomSessions;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FreenomWebReg
 *
 * @package Acme\Console\Command
 */
class FreenomWebReg extends Command
{
    protected $username;
    protected $email;
    protected $password;
    protected $accountId;
    protected $ip;
    protected $domain;
    protected $domains;
    protected $userAgent;
    protected $proxy = '';
    protected $addDomainData = [];
    protected $debug = true;
    protected $countRequests = 0;
    
    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('freenom:webreg')
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
        $this->ip = '217.182.242.108';
        $faker = Factory::create();
        $domainsSub = ['.tk', '.ml', '.ga', '.cf', '.gq'];
        $accounts = FreenomAccounts::find(['isWork' => 0]);
        $resultDomains = [];
        if ($accounts->count > 0) {
            foreach ($accounts->rows as $account) {
                print_r($account);
                $count = $account->countStarts;
                $this->accountId = $account->id;
                if ($count == 0) {
                    $count = 1;
                }
                $date1 = new \DateTime($account->dateCreate);
                $date2 = new \DateTime($account->dateUpdate);
                $diff = $date2->diff($date1);
                $seconds = ($diff->y * 365 * 24 * 60 * 60) +
                    ($diff->m * 30 * 24 * 60 * 60) +
                    ($diff->d * 24 * 60 * 60) +
                    ($diff->h * 60 * 60) +
                    ($diff->i * 60) +
                    $diff->s;
                $hour = round($seconds / 60 / 60);
                $work = $hour / $count;
                if ($work >= 24) {
                    //Login:
                    //                    FreenomAccounts::where(['id' => $account->id])->update([
                    //                        'isWork' => 1,
                    //                        'countStarts' => $count + 1
                    //                    ]);
                    if ($account->userAgent === '') {
                        $userAgents = [
                            "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36",
                            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36",
                            "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36",
                            "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36",
                            "Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36",
                            "Mozilla/5.0 (Windows NT 6.4; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36",
                            "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2224.3 Safari/537.36",
                            "Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36",
                            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.124 Safari/537.36",
                            "Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36",
                            "Mozilla/5.0 (Windows NT 4.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36",
                            "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.67 Safari/537.36",
                            "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.67 Safari/537.36"
                        ];
                        $this->userAgent = $userAgents[mt_rand(0, count($userAgents) - 1)];
                        FreenomAccounts::where(['id' => $account->id])->update([
                            'userAgent' => $this->userAgent
                        ]);
                    } else {
                        $this->userAgent = $account->userAgent;
                    }
                    echo "\nSet userAgent: {$this->userAgent}\n";
                    $this->email = $account->email;
                    $this->password = $account->password;
                    $this->proxy = $account->proxy;
                    $this->username = str_replace(['@', '.'], '', $this->email);
                    //                    unlink("/home/insta/cookies/freenom/" . $this->username . '-cookies.dat');
                    //                    $token = '';
                    //                    $i = 0;
                    //                    while ($token == '') {
                    //                        $result = $this->request('https://my.freenom.com/clientarea.php');
                    //                        preg_match('/<input type="hidden" name="token" value="(.*?)" \/>/mis',
                    //                            $result[1], $results);
                    //                        $token = @$results[1];
                    //                        $i++;
                    //                        if ($i > 10) {
                    //                            echo "No isset token";
                    //                            FreenomAccounts::where(['id' => $this->accountId])->update([
                    //                                'isWork' => 0
                    //                            ]);
                    //                            die('No login');
                    //                        }
                    //                    }
                    //                    $loginData = [
                    //                        'password' => $this->password,
                    //                        'rememberme' => 'on',
                    //                        'token' => $token,
                    //                        'username' => $this->email
                    //                    ];
                    //                    $result = $this->request('https://my.freenom.com/dologin.php', $loginData);
                    $this->request('https://my.freenom.com/clientarea.php');
                    
//                    $randDomains = mt_rand(20, 23);
//                    //$this->request('https://my.freenom.com/domains.php');
//                    $result = $this->request('https://my.freenom.com/includes/domains/fn-available.php',
//                        [
//                            'domain' => "sdfsdfsf354345",
//                            'tld' => ''
//                        ], [
//                            'Accept: */*'
//                        ], 'https://my.freenom.com/domains.php');
//                    sleep(3);
//                    for ($i = 0; $i < $randDomains; $i++) {
//                        if (mt_rand(0, 4) == 1) {
//                            $uname = $faker->userName . rand(0, 2017);
//                        } elseif (mt_rand(0, 1) == 0) {
//                            $uname = $faker->firstNameFemale . rand(0, 2017);
//                        } else {
//                            $uname = $faker->lastName . rand(0, 2017);
//                        }
//                        $uname = str_replace([".", "-", ")", "'", "`"], "", $uname);
//                        $this->domain = mb_strtolower($uname . $domainsSub[rand(0, 4)]);
//                        $resultDomains[] = $this->domain;
//                        $this->domains = explode(".", $this->domain);
//                        sleep(3);
//                        $result = $this->request('https://my.freenom.com/includes/domains/fn-additional.php', [
//                            'domain' => str_replace([" ", "\n", "\r", "\t"], "", $this->domains[0]),
//                            'tld' => str_replace([" ", "\n", "\r", "\t"], "", $this->domains[1]),
//                        ], [
//                            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
//                        ], 'https://my.freenom.com/domains.php');
//                        //Add to cart:
//                        $result = json_decode($result[1]);
//                        print_r($result);
//                        if ($result->available === 1) {
//                            echo "\nOK!\n";
//                            $this->addDomainData['domains'][] = $this->domain;
//                        }
//                    }
                    $result = $this->request('https://my.freenom.com/cart.php?a=view');
                    
                    $this->request('https://my.freenom.com/includes/domains/confdomain-pricing.php',
                        $this->addDomainData);
                    
                    $result = $this->request('https://my.freenom.com/cart.php?a=view');
                    if (preg_match("/https:\/\/my.freenom.com\/clientarea.php\?setcheckout=true/i", $result[0])) {
                        $this->request('https://my.freenom.com/clientarea.php?setcheckout=true');
                        $result = $this->request('https://my.freenom.com/clientarea.php');
                        preg_match('/<input type="hidden" name="token" value="(.*?)" \/>/mis',
                            $result[1], $results);
                        $token = @$results[1];
                        
                        if (empty($token)) {
                            $loginData = [
                                'password' => $this->password,
                                'rememberme' => 'on',
                                'token' => $token,
                                'username' => $this->email
                            ];
                            $this->request('https://my.freenom.com/dologin.php', $loginData);
                        }
                        
                        $result = $this->request('https://my.freenom.com/cart.php?a=view');
                    }
                    
                    
                      echo "Cart view:\n";
                      preg_match('/<input type="hidden" name="token" value="(.*?)" \/>/mis',
                          $result[1], $results);
                      $token = $results[1];
                      
                      echo "setToken:{$token}\n";
                      $fpGetBlackbox = $this->request('https://my.freenom.com/templates/freenom/js/static_wdp.js');
                      $ioGetBlackbox = $this->request('https://mpsnare.iesnare.com/snare.js');
                      $hren = $this->request('https://my.freenom.com/iojs/4.1.1/dyn_wdp.js');
                      $session = rand(0, 99999) . time();
                      
                      $tmpHtmlContent = "
          <html>
    <head>
      <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js\"></script>
    </head>
   <body>
      
      <div id=\"container\"></div>
      <script type=\"text/javascript\">
      var io_install_flash = false;
      var io_install_stm = false;
      var io_bbout_element_id = 'iobb';
      var fp_bbout_element_id = 'fpbb';
  </script>
      <script>
      {$hren[1]}
      {$fpGetBlackbox[1]}
      {$ioGetBlackbox[1]}
      </script>
      <script type=\"text/javascript\">
  
    function ioio() {
        var \$v = ioGetBlackbox();
        // not entirely done? retry
        if (!\$v.finished) {
            setTimeout(function(){ ioio(); }, 100);
        }
    }
    function fpio() {
        var \$v = fpGetBlackbox();
        // not entirely done? retry
        if (!\$v.finished) {
            setTimeout(function(){ fpio(); }, 100);
        }
    }
  
    $(function() {
      setTimeout(function(){ fpio(); }, 200);
      setTimeout(function(){ ioio(); }, 200);
    });
   setTimeout(function(){
   console.log($(\"#fpbb\").val());
   console.log($(\"#iobb\").val());
       $(\"#fpbbResult\").html($(\"#fpbb\").val());
       $(\"#iobbResult\").html($(\"#iobb\").val());
       $.get('http://insta.oxgroup.media/webhook?session=$session&iobb='+$(\"#iobb\").val()+'&fpbb='+$(\"#fpbb\").val());
   }, 3000);
  
  </script>
        <input type=\"text\" value=\"\" name=\"fpbb\" id=\"fpbb\" />
  <input type=\"text\" value=\"\" name=\"iobb\" id=\"iobb\" />
        <div id='fpbbResult'></div>
        <div id='iobResult'></div>
    </body>
  </html>
  "; // $.get('https://bot.oxgroup.media/request?session=$session&iobb='+$(\"#iobb\").val()+'&fpbb='+$(\"#fpbb\").val());
                      
                      FreenomSessions::add(['sessid' => $session]);
                      file_put_contents("/insta/public/public/{$session}.html", $tmpHtmlContent);
                      //file_get_contents('http://mini.s-shot.ru/1024x768/JPEG/1024/Z100/D5/?insta.oxgroup.media%2Fpublic%2F' . $session . '.html');
                      $this->debug = false;
                      $this->request('http://mini.s-shot.ru/1024x768/JPEG/1024/Z100/D5/?insta.oxgroup.media%2Fpublic%2F' . $session . '.html');
                      $this->request('https://snapito.com/screenshots/insta.oxgroup.media.html?size=800x0&screen=1024x768&cache=2592000&delay=5&url=http%3A%2F%2Finsta.oxgroup.media%2Fpublic%2F' . $session . '.html');
                      $this->debug = true;
                      echo "\nManual test: http://insta.oxgroup.media/public/$session.html\n";
                      $iobb = '';
                      $fpbb = '';
                      while ($iobb == '') {
                          sleep(5);
                          print_r(['sessid' => $session]);
                          $find = FreenomSessions::find(['sessid' => $session]);
                          print_r($find);
                          if ($find->rows[0]->iobb != '') {
                              $iobb = $find->rows[0]->iobb;
                              $fpbb = $find->rows[0]->fpbb;
                          }
                      }
                      echo "\nIOBB SET! - " . $iobb;
                      echo "\n---------------------\nIOBB SET! - " . $fpbb;
                      //
                      
                      $postDataCart = [
                          'accepttos' => 'on',
                          'address1' => '',
                          'allidprot' => 'true',
                          'amount' => '0.00',
                          'city' => '',
                          'companyname' => '',
                          'country' => 'RU',
                          'custtype' => 'existing',
                          'firstname' => 'Name',
                          'fpbb' => $fpbb,
                          'iobb' => $iobb,
                          'lastname' => '',
                          'paymentmethod' => 'credit',
                          'phonenumber' => '',
                          'postcode' => '',
                          'state' => '',
                          'submit' => 'true',
                          'token' => $token
                      ];
                      
                      $result = $this->request('https://my.freenom.com/cart.php?a=checkout', $postDataCart);
                      echo "CheckOut:\n";
                      // print_r($result);
                      preg_match_all('/^Location:(.*)$/mi', $result[0], $matches);
                      print_r($matches);
                      $locationUrl = str_replace([' ', "\n", "\t", "\r"], '', @$matches[1][0]);
                      echo "Location Url: >" . $locationUrl . "<";
                      if ($locationUrl == 'https://my.freenom.com/failed_registration.php?e=flag' || $locationUrl == 'https://my.freenom.com/failed_registration.php') {
                          $status = false;
                      } else {
                          $status = true;
                      }
                      var_dump($status);
                      $this->request('https://my.freenom.com/cart.php?a=complete');
                      $this->request('https://my.freenom.com/cart.php');
                      
                      
                      if ($status === false) {
                          FreenomAccounts::where(['id' => $account->id])->update([
                              'isWork' => 2
                          ]);
                          exit();
                      } else {
                          foreach ($resultDomains as $domain) {
                              echo "$domain\n";
                              Domains::add(['domain' => $domain]);
                          }
                      }
                      
                    print_r($resultDomains);
                    FreenomAccounts::where(['id' => $account->id])->update([
                        'isWork' => 0
                    ]);
                    exit();
                }
            }
        }
        
        return $output->writeln("Complite");
    }
    
    protected function logic()
    {
        //Search:
        $searchDomainData = [
            'domain' => $this->domains[0],
            'tld' => ''
        ];
        $result = $this->request('https://my.freenom.com/includes/domains/fn-available.php', $searchDomainData);
        
        $result = $this->request('https://my.freenom.com/includes/domains/fn-additional.php', [
            'domain' => $this->domains[0],
            'tld' => $this->domains[1]
        ]);
        //Add to cart:
        $result = json_decode($result[1]);
        print_r($result);
        if ($result->status === 'OK') {
            echo "\nOK!\n";
            $this->addDomainData['domains'][] = $this->domain;
        }
        
        /*
        $result = $this->request('https://my.freenom.com/cart.php?a=confdomains');
        preg_match('/<input type="hidden" name="token" value="(.*?)" \/>/mis',
            $result[1], $results);
        $token = $results[1];
        echo "setToken:{$token}\n";
        $this->request('https://my.freenom.com/includes/domains/confdomain-update.php',
            ['domain' => $this->domain, 'period' => '12M']);
        echo "\nUpdate domain period:";
        // print_r($result);
        
       $this->request('https://my.freenom.com/includes/domains/domainconfigure.php',
            [
                'data' => json_encode([
                    $this->domain =>
                        [
                            'hn1' => $this->domain,
                            'hi1' => $this->ip,
                            'hn2' => 'www.' . $this->domain,
                            'hi2' => $this->ip
                        ]
                ])
            ]);
        echo "\nConfiguration domain:";
        
        $this->request('https://my.freenom.com/cart.php?a=confdomains', [
            $this->domains[0] . '_' . $this->domains[1] . '_period' => '12M',
            'domainns1' => 'ns01.freenom.com',
            'domainns2' => 'ns02.freenom.com',
            'domainns3' => 'ns03.freenom.com',
            'domainns4' => 'ns04.freenom.com',
            'domainns5' => '',
            'idprotection' => ['on'],
            'token' => $token,
            'update' => 'true'
        ]);
        */
        
        return true;
    }
    
    protected function request($url, $post = null, $headers = null, $referer = null)
    {
        $i = 0;
        $result = [];
        $result[] = '';
        while ($result[0] == '') {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            if (!is_null($headers)) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }
            if (!is_null($referer)) {
                curl_setopt($ch, CURLOPT_REFERER, $referer);
            }
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            //curl_setopt($ch, CURLOPT_COOKIEFILE, "/home/insta/cookies/freenom/" . $this->username . '-cookies.dat');
            //curl_setopt($ch, CURLOPT_COOKIEJAR, "/home/insta/cookies/freenom/" . $this->username . '-cookies.dat');
            curl_setopt($ch, CURLOPT_COOKIE,
                'AWSELB=BB755F330E44FE27E970EAECFCC78F629EB1F82E68A2EB4800BB8C05440CD44F87164DBFE8ADFF3E70BD458086728EC2CBAF4FA010B644897794A9E75D3F58371A29D2A8A2; WHMCSZH5eHTGhfvzP=3cv68mmig4o9qs1bfum2mr7952');
            if ($post) {
                curl_setopt($ch, CURLOPT_POST, count($post));
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
            }
            if (!empty($this->proxy)) {
                curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
            }
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            $resp = curl_exec($ch);
            $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($resp, 0, $header_len);
            $body = substr($resp, $header_len);
            $information = curl_getinfo($ch);
            curl_close($ch);
            
            $result = [$header, $body];
            echo "\nREQUEST: $url\n";
            print_r($post);
            print_r($information);
            if ($this->debug) {
                echo "\n--------------Result--------------:\n";
                print_r($result);
            }
            $i++;
            if ($i > 10) {
//                FreenomAccounts::where(['email' => $this->email])->update([
//                    'isWork' => 2
//                ]);
                die('Proxy don\'t work');
            }
        }
        $this->countRequests++;
        
        return $result;
    }
}