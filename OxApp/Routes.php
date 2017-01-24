<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 03.06.15
 * Time: 12:49
 */

use Ox\Router\RouteMiddleware;
use Ox\Router\Router;
use OxApp\helpers\DefaultRequest;
use OxApp\middleware\AuthTest;
use OxApp\models\SystemSettings;

Router::$requestDriver = DefaultRequest::getRequest();

Router::addMiddlewareGroup('Json', array(
    'ToJson' => [],
));


Router::setMiddlewareGroup('Json', function () {
    Router::rout('/users')->app('Users')->save();
});