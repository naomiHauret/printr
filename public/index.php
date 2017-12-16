<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET,PUT,POST,DELETE,OPTIONS");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    require '../vendor/autoload.php';
     session_start();
    //
    // App initialization
    $app = new \Slim\App([
        'settings' => [
            'displayErrorDetails' => true
        ]
    ]);
    
    // container
    require '../src/api/container.php';

    // routes
    require '../src/api/config/routes.php';

$app->run();