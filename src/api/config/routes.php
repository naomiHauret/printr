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
                $app->get('', \App\Models\Users::class . ':getOne')->setName('user');
                $app->put('', \App\Models\Users::class . ':update')->setName('user')->add($GLOBALS["checkIfOwner"]);
                $app->delete('', \App\Models\Users::class . ':delete')->setName('user')->add($GLOBALS["checkIfAdminStatus"]);            
            })->add($GLOBALS["checkIfAuthorized"]);
        });

        $app->group('/client/{id}', function () use ($app){
            $app->get('/orders', \App\Models\Users::class . ':getOrders')->setName('user');
            $app->get('/cart', \App\Models\Users::class . ':getCart')->setName('user');
        })->add($GLOBALS["checkIfAuthorized"]);
    });


    
/*  
    // -options
    $app->get('/api/options', \App\Models\Options::class . ':getAll')->add($checkIfAuthentified);
    $app->get('/api/options/{id}', \App\Models\Options::class . ':getOne')->add($checkIfAdminStatus);
    $app->post('/api/options', \App\Models\Options::class . ':add')->add($checkIfAdminStatus);
    $app->put('/api/options/{id}', \App\Models\Options::class . ':update')->add($checkIfAdminStatus);
    $app->delete('/api/options/{id}', \App\Models\Options::class . ':delete')->add($checkIfAdminStatus);

    // -prestations
    $app->get('/api/prestations', \App\Models\Prestations::class . ':getAll')->add($checkIfAdminStatus);
    $app->get('/api/prestations/{id}', \App\Models\Prestations::class . ':getOne')->add($checkIfAuthentified);
    $app->post('/api/prestations', \App\Models\Prestations::class . ':add')->add($checkIfAuthentified);
    $app->put('/api/prestations/{id}', \App\Models\Prestations::class . ':update')->add($checkIfAuthentified);
    $app->delete('/api/prestations/{id}', \App\Models\Prestations::class . ':delete')->add($checkIfAuthentified);

    // -carts
    $app->get('/api/carts', \App\Models\Carts::class . ':getAll')->add($checkIfAdminStatus);
    $app->get('/api/carts/{id}', \App\Models\Carts::class . ':getOne')->add($checkIfAuthorized);
    $app->get('/api/carts/{id}/prestations', \App\Models\Carts::class . ':getPrestations')->add($checkIfAuthorized);
    $app->post('/api/carts', \App\Models\Carts::class . ':add')->add($checkIfOwner);
    $app->put('/api/carts/{id}', \App\Models\Carts::class . ':update')->add($checkIfOwner);
    $app->delete('/api/carts/{id}', \App\Models\Carts::class . ':delete')->add($checkIfOwner);

    // -orders
    $app->get('/api/orders', \App\Models\Orders::class . ':getAll')->add($checkIfAdminStatus);
    $app->get('/api/orders/{id}', \App\Models\Orders::class . ':getOne')->add($checkIfAuthorized);
    $app->get('/api/orders/{id}/prestations', \App\Models\Orders::class . ':getPrestations')->add($checkIfAuthorized);
    $app->post('/api/orders', \App\Models\Orders::class . ':add')->add($checkIfAuthentified);
    $app->put('/api/orders/{id}', \App\Models\Orders::class . ':update')->add($checkIfAuthentified);
    $app->delete('/api/orders/{id}', \App\Models\Orders::class . ':delete')->add($checkIfAuthentified);

    $app->get('/api/disconnect', function($request, $response) {
        session_destroy();
        return $response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($_SESSION));
    });
 */