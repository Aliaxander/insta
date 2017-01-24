<?php
/**
 * Created by OxGroup.
 * User: Aliaxander
 * Date: 23.11.15
 * Time: 16:51
 */

namespace OxApp\modules\jobs;

use OxApp\models\CronApi;
use OxApp\models\SystemPostback;

/**
 * Class PostBack
 *
 * @package OxApp\modules\jobs
 */
class PostBack implements CronJobInterface
{
    /**
     * @return int
     */
    public function start()
    {
        $counts = 0;
        $postBacks = SystemPostback::find(array("status" => 0));
        if ($postBacks->count > 0) {
            foreach ($postBacks->rows as $row) {
                $post = new CronApi();
                if ($row->post === "post") {
                    $postData = array();
                    $url = $row->url;
                    $url = str_replace("://", "{TMP_SL}", $url);
                    
                    $url = explode("/", $url);
                    $final = "";
                    $finalUrl1 = "";
                    $finalUrl2 = "";
                    foreach ($url as $count => $tmpUrl) {
                        $counts++;
                        if ($count > 0) {
                            $tmp2Url = explode("?", $tmpUrl);
                            if (isset($tmp2Url[1])) {
                                $finalUrl2 = "?" . $tmp2Url[0];
                            }
                            foreach ($tmp2Url as $tmp3Url) {
                                $tmp4Url = explode("&", $tmp3Url);
                                foreach ($tmp4Url as $tmp5Url) {
                                    $tmp6Url = explode("=", $tmp5Url);
                                    if (isset($tmp6Url[1])) {
                                        $postData[$tmp6Url[0]] = $tmp6Url[1];
                                        $final .= "&" . urlencode($tmp6Url[0]) . "=" . urlencode($tmp6Url[1]);
                                    } else {
                                        $postData[$tmp5Url] = "";
                                        $final .= urlencode($tmp5Url);
                                    }
                                }
                            }
                        } else {
                            $finalUrl1 = $tmpUrl;
                        }
                    }
                    
                    $resultUrl = $finalUrl1 . "/" . $finalUrl2 . $final;
                    $resultUrl = str_replace("{TMP_SL}", "://", $resultUrl);
                    echo $resultUrl . "\n";
                    $postResult = $post->postData($resultUrl, $postData);
                } else {
                    $postResult = $post->postData($row->url);
                }
                
                if (isset($postResult['status']) && $postResult['status'] === "ok") {
                    SystemPostback::update(array("status" => 1, "error" => "ok"), array("id" => $row->id));
                } elseif (isset($postResult['status'])) {
                    SystemPostback::update(
                        array(
                            "status" => 1,
                            "error" => $postResult['status']
                        ),
                        array("id" => $row->id)
                    );
                } else {
                    SystemPostback::update(
                        array(
                            "status" => 1,
                            "error" => "Bad POST URL: {$resultUrl}"
                        ),
                        array("id" => $row->id)
                    );
                }
            }
        }
        
        return $counts;
    }
}
