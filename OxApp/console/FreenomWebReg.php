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
    protected $userAgent;
    protected $debug = true;
    
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
        echo "\nSet userAgent: {$this->userAgent}\n";
        $this->userAgent = 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2226.0 Safari/537.36';
        //asdfghbvc@yahoo.com T3kWEuyVJBSL2ca2
        $email = 'asdfghbvc@yahoo.com';
        $password = 'T3kWEuyVJBSL2ca2';
        $this->username = str_replace(['@', '.'], '', $email);
        $domain = 'sfsdfsadfszadf.tk';
        $domains = explode(".", $domain);
        $ip = '217.182.242.108';
        //Login:
        //        $result = $this->request('https://my.freenom.com/clientarea.php');
        //        preg_match('/<input type="hidden" name="token" value="(.*?)" \/>/mis',
        //            $result[1], $results);
        //        $token = $results[1];
        //        $loginData = [
        //            'password' => $password,
        //            'rememberme' => 'on',
        //            'token' => $token,
        //            'username' => $email
        //        ];
        //        $result = $this->request('https://my.freenom.com/dologin.php', $loginData);
        //        print_r($result);
        //        $this->request('https://my.freenom.com/clientarea.php');
        
        //Search:
        $this->request('https://my.freenom.com/domains.php');
        $searchDomainData = [
            'domain' => $domains[0],
            'tld' => $domains[1]
        ];
        $this->request('https://my.freenom.com/includes/domains/fn-available.php', $searchDomainData);
        
        //Add to cart:
        $addDomainData['domains'][] = $domain;
        $this->request('https://my.freenom.com/includes/domains/confdomain-pricing.php', $searchDomainData);
        
        $result = $this->request('https://my.freenom.com/cart.php?a=confdomains');
        preg_match('/<input type="hidden" name="token" value="(.*?)" \/>/mis',
            $result[1], $results);
        $token = $results[1];
        echo "setToken:{$token}\n";
        
        $result = $this->request('https://my.freenom.com/includes/domains/confdomain-update.php',
            ['domain' => $domain, 'period' => '12M']);
        echo "\nUpdate domain period:";
        print_r($result);
        
        $result = $this->request('https://my.freenom.com/includes/domains/confdomain-update.php',
            ['domain' => $domain, 'period' => '12M']);
        echo "\nUpdate domain period:";
        print_r($result);
        
        $result = $this->request('https://my.freenom.com/includes/domains/domainconfigure.php',
            [
                'data' => json_encode([
                    $domain => [
                        'hn1' => $domain,
                        'hi1' => $ip,
                        'hn2' => 'www.' . $domain,
                        'hi2' => $ip
                    ]
                ])
            ]);
        echo "\nConfiguration domain:";
        print_r($result);
        
        
        $result = $this->request('https://my.freenom.com/cart.php?a=confdomains', [
            $domains[0] . '_' . $domains[1] . '_period' => '12M',
            'domainns1' => 'ns01.freenom.com',
            'domainns2' => 'ns02.freenom.com',
            'domainns3' => 'ns03.freenom.com',
            'domainns4' => 'ns04.freenom.com',
            'domainns5' => '',
            'idprotection' => ['on'],
            'token' => $token,
            'update' => 'true'
        ]);
        
        
        $result = $this->request('https://my.freenom.com/cart.php?a=view');
        echo "Cart view:\n";
        print_r($result);
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
     $.get('http://insta.oxgroup.media/webhook?session='.$session.'&iobb='+$(\"#iobb\").val()+'&fpbb='+$(\"#fpbb\").val());
      
 }, 3000);

</script>
      <input type=\"text\" value=\"\" name=\"fpbb\" id=\"fpbb\" />
<input type=\"text\" value=\"\" name=\"iobb\" id=\"iobb\" />
      <div id='fpbbResult'></div>
      <div id='iobResult'></div>
  </body>
</html>
";
        
        FreenomSessions::add(['sessid' => $session]);
        file_put_contents("/insta/public/public/{$session}.html", $tmpHtmlContent);
        file_get_contents('https://api.thumbalizr.com/?url=http://insta.oxgroup.media/public/' . $session . '.html&width=1&quality=10&output=text&delay=5');
        
        $iobb = '';
        while ($iobb == '') {
            sleep(15);
            $find = FreenomSessions::find(['sessid' => $session]);
            if ($find->rows[0]->iobb != '') {
                $iobb = $find->rows[0]->iobbl;
            }
        }
        echo "IOBB SET! - " . $iobb;
        //
        
        //
        //        $postDataCart = [
        //            'accepttos' => 'on',
        //            'address1' => '',
        //            'allidprot' => 'true',
        //            'amount' => '0.00',
        //            'city' => '',
        //            'companyname' => '',
        //            'country' => 'RU',
        //            'custtype' => 'existing',
        //            'firstname' => 'Name',
        //            'fpbb' => '0400JapG4txqVP4Nf94lis1ztioT9A1DShgATTGvKpCeZlRwmEZ+z1fPZRcJnGLiky0W1+WpmjdizJJlHVB6chrI7oZc+cJURcFiYtBUbpa7l0GrVjpSL
        ///tvtZ2bYY09+WmE2HRR5wgRl26U2N2a/ORKHzYTVKPARS34tCoeN+KAk0KAU1UNOVUCYlOzKIjzY0fFx+6o9j1+mLEsTM/hMBs0vL349852M7BfjNXsgMEghG3fMJhX2p2YpAkH
        //+zgzCnlu0X36ReTqNZEVQA3ciQm23ingkdarUr4UDX8icOWPZcz7cCunQS9MKzlVT8kOFdaE7qymUKbVlbqorEHmj1pZZ66jmh7HDH8Z3TiDrbspxq
        ///8mzNKPwis+lJO1SuKjjw/It0PAOMIkB0gqHxyPWh5igw/qy8utehWRgEqSehE5i27TsSmJARVzQoPjvWMTtkeBqVHq2r3awsrk4Sm95JfOSU310EOWxHkS0xurs1BeyDYXo3BTCX2hhsEKnBjBXXQTTGvKpCeZlRwmEZ
        //+z1fPZRcJnGLiky0WfYy1Zukz1otMgGebJUzGia+yWXOuou6nMQSqs60pLstDdd59r5gHsvtXkZq1g5ksa0Yy5JnHYpPn/zvtFvKHdZX07Oxcz5OxtBIiUDT
        //+wXnmaw339WCGxrgOZLQRI9CNThlVfDOx+sP4KoZyi5Ckp7Sr/aHivJZGxA9x3qRKbjNYX+cfTaApON3ndr+XTA056EuQOlw0fYYM0LX
        ///nSTBMb8i3Z8peI9cv9eOvsyvTBVR12uX7A6i4tXh35GdB6Cc0rdKBra3m4YZeki9r6bxKbcyljLR/8M/2KjEnnx1FqeVT31SgfbGSTmiE0A1szM3YKVvBmottnFrnKVXAzuUK0avykFH13cJGZeFY7fsstiThR3pdL
        ///pMeZpe1KXZSaa7EyASkeG+CQ=',
        //            'iobb' => '0400NkFMpQRHXAMNf94lis1ztioT9A1DShgATTGvKpCeZlRwmEZ+z1fPZRcJnGLiky0WhxsJ3VO4EPgIzzvOlBUh9qRTaDjmUTl26LEtcYEgIDXyxDKJBqBicYLtctOBy5zsuLyMF4
        //+8TBhgO962GbSLFcqAPNKVwml2GkVn5hklpTXQtVCYANQUGR45eVo3JvuIdsxrLQrKON3dOIOtuynGr2hCOHvr5Xx0WPy/tudaZOuBTvCYfZaHNdxnF72KhuoPujoZ8bICN3
        ///xFYjpJeaAv5VPfVKB9sZJOlrwySt1/vVmzHI2bu+7e350EE3SzqcIPY+ZjHFE+SRu+9PtbjuR1NzZbnGgZgkGYYDWpT3oEwgMKXKx2z97
        //+cvQykm6AmkM8cFCb3XpK0J64YAhG7JwvUJbQHdojpjM/H8neKkenzp9XcKQnr3kWmCaRYLawxXCP+g9ErKzWVLfHYTi14x8cCRQ3fV20ZNSdoDUv55HSoiXV
        //+t2kWnHTwQQqAQBl9yd1FA6w+OwNTpYc0yoMxALJZUzACQ5swXPUbcRyxrNT/ny4H/m2pgQaXYgmYbwj+5bpiIn/4g7jAnC1AevYOBPe8UvRkxkPz1xoATgI259
        //+wrAqvQx/6iBZk5svk/NHrqcgniPx9iG3QT4GxAs6buRZtT16TeM5diEHf7WUtiodkRUZMqNeDi7ksz35ywOyseXtSVPrbgATwhU
        //w0i6DueeNaanp6x8og4dVguGdCj5lhdAXKA55lhYlzntEcVmW1kMcABGmc1oA9VPYfvE9ENyohfAGZGQxaNZrhG3DzdZA1kzqbwpmWa8I7abfHrNqSk8CnwUL5rYETzL9kaL85lzU9YPUST3gTYnndnF9oBO0Ev8evHAeToiq904g627Kcav
        ///JszSj8IrPqSBBWhu07LmeicE+UPGuFo0G3hdncuVXi/TDp0UzLYSznlfv63gmmfLLQw4RnH+LuCCzeqH4yrtbsCyzUxPaqc3QhI1mrkCgZEnkf
        //+WlBpJZrz4ssIJoqUgyEZRlhW6ydpIryDlK93uPgmNkcbYB5+/cvHTYDT6VBmHr10j7mvZbthumyR/sokQj48mXTdxmWvlw4u8V04El
        ///+do8rKLboBQhNV0Xzfh2dtRE+K4Wh2DeGG4xIicZNYurc0fA5h8qymyA00KGWzPAI/riXj0GwLJdkRO+fBjo5zt1Gn4/kbg91wcO2q799SbUXZrXdkFbl0wt9PRt24l6qHpLcttv
        ///NC2Q6knKdhtO1Tp84e7/t7sG8LFmZ++nZ4ClRX6W68W1YxhJDf2DYkqOaLbCpox1FHud/XjtdkQHRcCdmxGgsLKxGp8Rq1i7oaETk5
        ///y1Q6V4UWDdKfC+AaRs9qV4RFT5C7RNAwoKI2zG2+QcNoQs5VPfVKB9sZJOlrwySt1/vVmzHI2bu+7e350EE3SzqcIPY+ZjHFE+SRu
        //+9PtbjuR1NzZbnGgZgkGc0CA+UvacqlVQXB7goC+Ng==',
        //            'lastname' => '',
        //            'paymentmethod' => 'credit',
        //            'phonenumber' => '',
        //            'postcode' => '',
        //            'state' => '',
        //            'submit' => 'true',
        //            'token' => $token
        //        ];
        //
        //        $result = $this->request('https://my.freenom.com/cart.php?a=checkout', $postDataCart);
        //        echo "CheckOut:\n";
        //        print_r($result);
        //
        //        $result = $this->request('https://my.freenom.com/cart.php?a=complete');
        //        print_r($result);
        //        $result = $this->request('https://my.freenom.com/cart.php');
        //        print_r($result);
        
        return $output->writeln("Complite");
    }
    
    protected function request($url, $post = null, $headers = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        if (!is_null($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_COOKIEFILE, "/home/insta/cookies/freenom" . $this->username . '-cookies.dat');
        curl_setopt($ch, CURLOPT_COOKIEJAR, "/home/insta/cookies/freenom" . $this->username . '-cookies.dat');
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, count($post));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        if (!empty($this->proxy)) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        }
        $resp = curl_exec($ch);
        $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($resp, 0, $header_len);
        $body = substr($resp, $header_len);
        curl_close($ch);
        if ($this->debug) {
            echo "\nREQUEST: $url\n";
            print_r($post);
        }
        $result = [$header, $body];
        echo "\n--------------Result--------------:\n";
        
        return $result;
    }
}