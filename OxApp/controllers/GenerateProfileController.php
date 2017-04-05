<?php
/**
 * Created by PhpStorm.
 * User: kinkytail
 * Date: 05.04.17
 * Time: 12:17
 */

namespace OxApp\controllers;

use Ox\App;
use Ox\View;
use OxApp\helpers\TextTemplateGenerator;
use OxApp\models\ProfileGenerate;

class GenerateProfileController extends App
{
    public function get()
    {
        return View::build("generateProfile");
        //        $text = "{FREE|UNPAID|Free} M{E|Е}{E|Е}T {&|and|&|AND|'N'} FU{*|**|***|с|C}K! {Attractive|Cute|Fine|Good-Looking|Gorgeous|Graceful|Handsome|Love||Pleasing|Pretty|Splendid|Stunning|Superb|Wonderful|Admirable|Angelic|Beauteous|Bewitching|Classy|Comely|Divine|Enticing|Excellent|Fair|Foxy|Ideal|Nice|Radiant|Ravishing|Refined|Resplendent|Shapely|Sightly|Statuesque|Sublime|Symmetrical|Taking|Well-Formed} {slutty|easily accessible|lustful|kinky} {ladies|girls|teens|woman} {want|need|wish} to f{*|**|***|у}{с|с}{k|к}{!|.}";
        //
        //        $generator = new TextTemplateGenerator($text);
        //        $biography = $generator->generate(2000);
        //        echo "<pre>";
        //        $array1 = count($biography);
        //        $array2 = array_unique($biography);
        //        echo "$array1 => " . count($array2);
        //        print_r($array2);
    }
    
    public function post()
    {
        $biography = $this->request->request->get('biography');
        $domain = $this->request->request->get('domain');
        $count = $this->request->request->get('count');
        
        $generator = new TextTemplateGenerator($biography);
        $generator1 = new TextTemplateGenerator($domain);
        $biography = array_unique($generator->generate($count));
        $domains = array_unique($generator1->generate($count));
  
        $counts = count($domains)-1;
        foreach ($biography as $key => $item) {
            $url = @$domains[mt_rand(0, $counts)];
            $data= ['description' => $item, 'url' => $url, 'status' => 0];
            if(!empty($item) && !empty($url)) {
                ProfileGenerate::add($data);
            }
        }
        
        header("Location: /showProfile");
    }
}