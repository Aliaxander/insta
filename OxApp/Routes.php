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

Router::$requestDriver = DefaultRequest::getRequest();
RouteMiddleware::$debug=true;


Router::addMiddlewareGroup('Json', array(
    'ToJson' => [],
));

Router::rout('/')->app('Index')->save();
//description
Router::rout('/generateProfile')->app('GenerateProfile')->save();
Router::rout('/descriptionProfile')->app('DescriptionProfile')->save();
Router::rout('/testMacros')->app('TestMacros')->save();
Router::rout('/deleteProfile/:num=>id')->app('DeleteProfile')->save();

Router::rout('/tunnels')->app('Tunnels')->save();


Router::rout('/autoclick')->app('AutoClick')->save();

///webhook
Router::rout('/webhook')->app('WebHook')->save();

//Reset users:
Router::rout('/resetUsers')->app('ResetUsers')->save();

//users
Router::rout('/deleteUsers')->app('DeleteUsers')->save();

//proxy
Router::rout('/proxy')->app('Proxy')->save();
//users
Router::rout('/users')->app('Users')->save();
Router::rout('/userGroup')->app('UserGroup')->save();
//task
Router::rout('/task')->app('Task')->save();
Router::rout('/taskType')->app('TaskType')->save();

//techAccount
Router::rout('/techAccount')->app('TechAccount')->save();

//servers
Router::rout('/servers')->app('Servers')->save();

//domains
Router::rout('/domains')->app('Domains')->save();

//systemSettings
Router::rout('/systemSettings')->app('SystemSettings')->save();

Router::setMiddlewareGroup('Json', function () {
    Router::rout('/api/users')->app('api\\Users')->save();
    Router::rout('/api/systemSettings')->app('api\\SystemSettings')->save();
    Router::rout('/api/techAccount')->app('api\\TechAccount')->save();
    Router::rout('/api/domains')->app('api\\Domains')->save();
    Router::rout('/api/proxy')->app('api\Proxy')->save();
    Router::rout('/api/tunnels')->app('api\Tunnels')->save();
    Router::rout('/api/servers')->app('api\Servers')->save();
});