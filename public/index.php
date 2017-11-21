<?php
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