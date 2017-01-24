<?php
/**
 * Created by OxGroup.
 * User: Aliaxander
 * Date: 14.03.16
 * Time: 14:27
 */

namespace OxApp\helpers;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultRequest
 *
 * @package OxApp\helpers
 */
class DefaultRequest
{
    /**
     * @var
     */
    private static $object;

    /**
     * @return Request
     */
    public static function getRequest()
    {
        if (!empty(self::$object)) {
            $request = self::$object;
        } else {
            $request = Request::createFromGlobals();
            if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
                $data = json_decode($request->getContent(), true);
                $resultData = array();
                if (0 !== count($data)) {
                    foreach ($data as $key => $val) {
                        if (is_array($val)) {
                            $val = json_encode($val);
                        }
                        $resultData[$key] = $val;
                    }
                    $request->request->add(is_array($resultData) ? $resultData : array());
                    $request->query->add(is_array($resultData) ? $resultData : array());
                }
            }
            self::$object = $request;
        }

        return $request;
    }
}
