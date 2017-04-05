<?php
/**
 * Created by OxGroup.
 * User: aliaxander
 * Date: 03.06.15
 * Time: 12:49
 */

use Ox\Router\Router;
use OxApp\helpers\DefaultRequest;

Router::$requestDriver = DefaultRequest::getRequest();

Router::addMiddlewareGroup('Json', array(
    'ToJson' => [],
));

Router::rout('/generateProfile')->app('GenerateProfile')->save();
Router::rout('/showProfile')->app('ShowProfile')->save();
Router::rout('/deleteProfile/:num=>id')->app('DeleteProfile')->save();
Router::rout('/')->app('Index')->save();


Router::setMiddlewareGroup('Json', function () {
    Router::rout('/users')->app('Users')->save();
});
