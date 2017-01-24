<?php
/**
 * Created by OxGroup.
 * User: Александр
 * Date: 06.12.2015
 * Time: 12:05
 */

namespace OxApp\middleware;

use Ox\Router\RouteMiddleware;

/**
 * Class ToJson
 *
 * @package OxApp\middleware
 */
class ToJson
{
    /**
     * @param array $rule
     *
     * @return bool
     */
    public function rules($rule = array())
    {
        if (0 !== count($rule)) {
            return false;
        }
        RouteMiddleware::$handlerFormat = 'json';
        
        return true;
    }
}
