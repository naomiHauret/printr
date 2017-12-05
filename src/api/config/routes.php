<?php
    
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    // middlewares
    require '../src/api/Middlewares/UserMiddlewares.php';

    //
    // Routes 

    //
    // = Auth map
    // - checkIfAuthentified : user must be logged in to access the resource
    // - checkIfAuthorized : user must be either the resource's owner or an admin to access it
    // - checkIfOwner : user must own the resource to access it
    // - checkIfAdminStatus : user must be an admin to access the resource
    //

    $app->group('/api/v1', function () use ($app) {
        $app->post('/verify_account', \App\Models\Users::class . ':verifyAccount');
        $app->get('/disconnect',  function($request, $response) {
            session_destroy();
            return $response->withStatus(200)
                        ->withHeader('Content-Type', 'application/json')
                        ->write(json_encode($_SESSION));
        });

        // -users & clients
        $app->group('/users', function () use ($app) {
            $app->get('', \App\Models\Users::class . ':getAll')->add($GLOBALS["checkIfAdminStatus"]);
            $app->post('',  \App\Models\Users::class . ':add');

            $app->group('/{id}', function () use ($app){
                $app->get('/orders', \App\Models\Users::class . ':getOrders')->setName('user')->setName('user');
                $app->get('/carts', \App\Models\Users::class . ':getCarts')->setName('user')->setName('user')->add($GLOBALS["checkIfAdminStatus"]);
                $app->get('/cart', \App\Models\Users::class . ':getCart')->setName('user')->setName('user');
                $app->get('', \App\Models\Users::class . ':getOne')->setName('user');
                $app->put('', \App\Models\Users::class . ':update')->setName('user')->add($GLOBALS["checkIfOwner"]);
                $app->delete('', \App\Models\Users::class . ':delete')->setName('user');            
            })->add($GLOBALS["checkIfAuthorized"]);
        });

        // - formats
        $app->group('/formats', function () use ($app){
            $app->get('', \App\Models\Formats::class . ':getAll');
            $app->post('',  \App\Models\Formats::class . ':add')->add($GLOBALS["checkIfAdminStatus"]);  
            $app->group('/{id}', function () use ($app){
                $app->get('', \App\Models\Formats::class . ':getOne');
                $app->put('', \App\Models\Formats::class . ':update');
                $app->delete('', \App\Models\Formats::class . ':delete');            
            })->add($GLOBALS["checkIfAdminStatus"]);
        });

        // - options
        $app->group('/options', function () use ($app){
            $app->get('', \App\Models\Options::class . ':getAll');
            $app->post('',  \App\Models\Options::class . ':add')->add($GLOBALS["checkIfAdminStatus"]);  
            $app->group('/{id}', function () use ($app){
                $app->get('', \App\Models\Options::class . ':getOne');
                $app->put('', \App\Models\Options::class . ':update');
                $app->delete('', \App\Models\Options::class . ':delete');            
            })->add($GLOBALS["checkIfAdminStatus"]);
        });

        // - prestations
        $app->group('/prestations', function () use ($app){
            $app->get('', \App\Models\Prestations::class . ':getAll')->add($GLOBALS["checkIfAdminStatus"]);
            $app->post('',  \App\Models\Prestations::class . ':add');
            $app->group('/{id}', function () use ($app){
                $app->get('', \App\Models\Prestations::class . ':getOne');
                $app->put('', \App\Models\Prestations::class . ':update');
                $app->delete('', \App\Models\Prestations::class . ':delete');
                $app->group('/options', function () use ($app){
                    $app->post('', \App\Models\Prestations::class . ':addOption');
                    $app->group('/{option_id}', function () use ($app){
                        $app->put('', \App\Models\Prestations::class . ':updateOption');
                        $app->post('', \App\Models\Prestations::class . ':deleteOption');
                    });
                });
            })->add($GLOBALS["checkIfAuthorized"]);
        })->add($GLOBALS["checkIfAuthentified"]);

        // - carts
        $app->group('/carts', function () use ($app){
            $app->get('', \App\Models\Carts::class . ':getAll')->add($GLOBALS["checkIfAdminStatus"]);
            $app->post('',  \App\Models\Carts::class . ':add');
            $app->group('/{id}', function () use ($app){
                $app->get('', \App\Models\Carts::class . ':getOne');
                $app->put('', \App\Models\Carts::class . ':update');
                $app->delete('', \App\Models\Carts::class . ':delete');
            });
        })->add($GLOBALS["checkIfAuthorized"]);

        // - orders
        $app->group('/orders', function () use ($app){
            $app->get('', \App\Models\Orders::class . ':getAll')->add($GLOBALS["checkIfAdminStatus"]);
            $app->post('',  \App\Models\Orders::class . ':add');
            $app->group('/{id}', function () use ($app){
                $app->get('', \App\Models\Orders::class . ':getOne');
                $app->put('', \App\Models\Orders::class . ':update');
                $app->delete('', \App\Models\Orders::class . ':delete');
                $app->get('/prestations', \App\Models\Orders::class . ':getPrestations');
            });
        })->add($GLOBALS["checkIfAuthorized"]);
    });
