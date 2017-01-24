<?php
/**
 * Created by OxGroup.
 * User: Aliaxander
 * Date: 29.06.16
 * Time: 16:38
 *
 * @category  GoogleSearch
 * @package   OxApp\modules\jobs
 * @author    Aliaxander
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link      http://oxgroup.media/
 */
namespace OxApp\modules\jobs;

use OxApp\models\OxSearch;
use Serps\HttpClient\CurlClient;
use Serps\SearchEngine\Google\GoogleClient;
use Serps\SearchEngine\Google\GoogleUrl;

/**
 * Class GoogleSearch
 *
 * @package OxApp\modules\jobs
 */
class GoogleSearch implements CronJobInterface
{
    /**
     * @throws \Serps\Exception
     * @throws \Serps\Exception\RequestError\InvalidResponseException
     * @throws \Serps\Exception\RequestError\PageNotFoundException
     * @throws \Serps\SearchEngine\Google\Exception\GoogleCaptchaException
     */
    public function start()
    {

        // Create a google client using the curl http client
        $googleClient = new GoogleClient(new CurlClient());

        // Tell the client to use a user agent
        $userAgent =
            "Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36";
        $googleClient->request->setUserAgent($userAgent);
        // Create the url that will be parsed
        $googleUrl = new GoogleUrl();
        $googleUrl->setLanguageRestriction("RU");
        $googleUrl->setSearchTerm('oxcpa');
        $googleUrl->setResultsPerPage(100);
        $googleUrl->setParam("bs", "qdr:w");
        $response = $googleClient->query($googleUrl);

        $results = $response->getNaturalResults();

        foreach ($results as $result) {
            $result = $result->getData();
            if (!preg_match("/oxcpa.ru\//i", $result['url']) && !preg_match("/books.google.by\//i", $result['url'])) {
                $hash = md5($result["title"] . $result["description"] . $result["url"]);
                $search = OxSearch::find(["hash" => $hash]);
                if ($search->count == 0) {
                    OxSearch::add([
                        "title" => $result["title"],
                        "description" => $result["description"],
                        "url" => $result["url"],
                        "hash" => $hash,
                        "source" => "google"
                    ]);
                }
            }
        }
    }
}
