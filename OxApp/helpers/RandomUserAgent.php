<?php
/**
 * Created by PhpStorm.
 * User: irina
 * Date: 23.05.17
 * Time: 19:23
 */

namespace OxApp\helpers;


class RandomUserAgent
{
    public function __construct()
    {
    
       $this->linux_proc = array(
            'i686',
            'x86_64'
        );
        /**
         * Mac processors (i also added U;)
         */
        $this->mac_proc = array(
            'Intel',
            'PPC',
            'U; Intel',
            'U; PPC'
        );
        /**
         * Add as many languages as you like.
         */
        $this->lang = array(
            'en-US',
            'sl-SI',
            'fr-MC',
            'fr-LU',
            'de-CH',
            'es-PR',
            'eu-ES',
            'en-US',
            'fr-CH',
            'uk-UA',
        );
    }
    
    /**
     * Possible processors on Linux
     */
    public function firefox()
    {
      
        $ver = array(
            'Gecko/' . date('Ymd', mt_rand(strtotime('2011-1-1'), mktime())) . ' Firefox/' . mt_rand(5, 7) . '.0',
            'Gecko/' . date('Ymd', mt_rand(strtotime('2011-1-1'), mktime())) . ' Firefox/' . mt_rand(5, 7) . '.0.1',
            'Gecko/' . date('Ymd', mt_rand(strtotime('2010-1-1'), mktime())) . ' Firefox/3.6.' . mt_rand(1, 20),
            'Gecko/' . date('Ymd', mt_rand(strtotime('2010-1-1'), mktime())) . ' Firefox/3.8'
        );
        $platforms = array(
            '(Windows NT ' . mt_rand(5, 6) . '.' . mt_rand(0, 1) . '; ' . $this->lang[array_rand($this->lang,
                1)] . '; rv:1.9.' . mt_rand(0,
                2) . '.20) ' . $ver[array_rand($ver, 1)],
            '(X11; Linux ' . $this->linux_proc[array_rand($this->linux_proc, 1)] . '; rv:' . mt_rand(5,
                7) . '.0) ' . $ver[array_rand($ver, 1)],
            '(Macintosh; ' . $this->mac_proc[array_rand($this->mac_proc, 1)] . ' Mac OS X 10_' . mt_rand(5, 7) . '_' . mt_rand(0,
                9) . ' rv:' . mt_rand(2, 6) . '.0) ' . $ver[array_rand($ver, 1)]
        );
        
        return $platforms[array_rand($platforms, 1)];
    }
    
    public function safari()
    {
      
        $saf = mt_rand(531, 535) . '.' . mt_rand(1, 50) . '.' . mt_rand(1, 7);
        if (rand(0, 1) == 0) {
            $ver = mt_rand(4, 5) . '.' . mt_rand(0, 1);
        } else {
            $ver = mt_rand(4, 5) . '.0.' . mt_rand(1, 5);
        }
        $platforms = array(
            '(Windows; U; Windows NT ' . mt_rand(5, 6) . '.' . mt_rand(0,
                1) . ") AppleWebKit/$saf (KHTML, like Gecko) Version/$ver Safari/$saf",
            '(Macintosh; U; ' . $this->mac_proc[array_rand($this->mac_proc, 1)] . ' Mac OS X 10_' . mt_rand(5, 7) . '_' . mt_rand(0,
                9) . ' rv:' . mt_rand(2, 6) . '.0; ' . $this->lang[array_rand($this->lang,
                1)] . ") AppleWebKit/$saf (KHTML, like Gecko) Version/$ver Safari/$saf",
            '(iPod; U; CPU iPhone OS ' . mt_rand(3, 4) . '_' . mt_rand(0,
                3) . ' like Mac OS X; ' . $this->lang[array_rand($this->lang,
                1)] . ") AppleWebKit/$saf (KHTML, like Gecko) Version/" . mt_rand(3,
                4) . ".0.5 Mobile/8B" . mt_rand(111,
                119) . " Safari/6$saf",
        );
        
        return $platforms[array_rand($platforms, 1)];
    }
    
    public function iexplorer()
    {
        $ie_extra = array(
            '',
            '; .NET CLR 1.1.' . mt_rand(4320, 4325) . '',
            '; WOW64'
        );
        $platforms = array(
            '(compatible; MSIE ' . mt_rand(5, 9) . '.0; Windows NT ' . mt_rand(5, 6) . '.' . mt_rand(0,
                1) . '; Trident/' . mt_rand(3, 5) . '.' . mt_rand(0, 1) . ')'
        );
        
        return $platforms[array_rand($platforms, 1)];
    }
    
    public function opera()
    {
        $op_extra = array(
            '',
            '; .NET CLR 1.1.' . mt_rand(4320, 4325) . '',
            '; WOW64'
        );
        $platforms = array(
            '(X11; Linux ' . $this->linux_proc[array_rand($this->linux_proc, 1)] . '; U; ' . $this->lang[array_rand($this->lang,
                1)] . ') Presto/2.9.' . mt_rand(160, 190) . ' Version/' . mt_rand(10, 12) . '.00',
            '(Windows NT ' . mt_rand(5, 6) . '.' . mt_rand(0, 1) . '; U; ' . $this->lang[array_rand($this->lang,
                1)] . ') Presto/2.9.' . mt_rand(160, 190) . ' Version/' . mt_rand(10, 12) . '.00'
        );
        
        return $platforms[array_rand($platforms, 1)];
    }
    
    public function chrome()
    {
      
        $saf = mt_rand(531, 536) . mt_rand(0, 2);
        $platforms = array(
            '(X11; Linux ' . $this->linux_proc[array_rand($this->linux_proc,
                1)] . ") AppleWebKit/$saf (KHTML, like Gecko) Chrome/" . mt_rand(13, 15) . '.0.' . mt_rand(800,
                899) . ".0 Safari/$saf",
            '(Windows NT ' . mt_rand(5, 6) . '.' . mt_rand(0,
                1) . ") AppleWebKit/$saf (KHTML, like Gecko) Chrome/" . mt_rand(13,
                15) . '.0.' . mt_rand(800, 899) . ".0 Safari/$saf",
            '(Macintosh; U; ' . $this->mac_proc[array_rand($this->mac_proc, 1)] . ' Mac OS X 10_' . mt_rand(5, 7) . '_' . mt_rand(0,
                9) . ") AppleWebKit/$saf (KHTML, like Gecko) Chrome/" . mt_rand(13, 15) . '.0.' . mt_rand(800,
                899) . ".0 Safari/$saf"
        );
        
        return $platforms[array_rand($platforms, 1)];
    }
    
    /**
     * Main public function which will choose random browser
     *
     * @return string user agent
     */
    public function random_uagent()
    {
        $x = mt_rand(1, 4);
        switch ($x) {
            case 1:
                echo "Mozilla/5.0 " . $this->firefox() . "\n";
                break;
            case 2:
                echo "Mozilla/5.0 " . $this->safari() . "\n";
                break;
            case 3:
                echo "Opera/" . mt_rand(8, 9) . '.' . mt_rand(10, 99) . ' ' . $this->opera() . "\n";
                break;
            case 4:
                echo 'Mozilla/5.0' . $this->chrome() . "\n";
                break;
        }
    }
    
}