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

Router::rout('/')->app('Index')->save();
//description
Router::rout('/generateProfile')->app('GenerateProfile')->save();
Router::rout('/showProfile')->app('ShowProfile')->save();
Router::rout('/testMacros')->app('TestMacros')->save();
Router::rout('/deleteProfile/:num=>id')->app('DeleteProfile')->save();

//users
Router::rout('/deleteUsers')->app('DeleteUsers')->save();

//proxy
Router::rout('/proxy')->app('Proxy')->save();
Router::rout('/addProxy')->app('AddProxy')->save();
//users
Router::rout('/users')->app('Users')->save();
