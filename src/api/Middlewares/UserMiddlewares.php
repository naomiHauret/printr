<?php
    namespace App\Middlewares;

    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    use Exception;

    class AuthentificationRequiredException extends Exception {}
    class AdminStatusRequiredException extends Exception {}
    class NotAuthorizedException extends Exception {}
    
    $container = $app->getContainer();
    $db = $container["db"];
    $GLOBALS["db"] = $db;
    $GLOBALS["checkIfAuthentified"] = $checkIfAuthentified;
    $GLOBALS["checkIfAuthorized"] = $checkIfAuthorized;
    $GLOBALS["checkIfAdminStatus"] = $checkIfAuthorized;
    $GLOBALS["checkIfOwner"] = $checkIfAuthorized;

    $checkIfAuthentified= function($request, $response, $next ) {
        try {
            if($_SESSION['is_loggedIn'] == true) {
                $result = $next($request, $response);
            }
            else {
                throw new AuthentificationRequiredException();
            }
        }
         catch(AuthentificationRequiredException $exception) {
             $error = [
                 "error" => [
                     "message" => "Unauthorized : user authentification is required."
                 ]
             ];
             $data= array_merge($error, $_SESSION);
             $result = $response->withStatus(401)
             ->withHeader('Content-Type', 'application/json')
             ->write(json_encode($data));
            }
        return $result;
    };

    $checkIfAuthorized= function($request, $response, $next ) {
        try {
                $route = $request->getAttribute('route');
                $routeName = $route->getName();
                $currentUserId = $_SESSION["currentUser_id"];
                if($routeName == "user") {
                    $idUserRequest = $route->getArguments()['id'];
                } else {
                    $idUserRequest = $request->getParam('user');
                }
                    
                if($currentUserId == $idUserRequest || $_SESSION['is_admin'] == true) {
                    $result = $next($request, $response);
                }
                else {
                    throw new NotAuthorizedException();
                }
        }
        catch(NotAuthorizedException $exception) {
            $error = [
                "error" => [
                    "message" => "Access forbidden : user must own the resource or be admin."
                ]
            ];
            $data= array_merge($error, $_SESSION);
            $result = $response->withStatus(403)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($data));
            }

        return $response;
    };

    $checkIfAdminStatus= function($request, $response, $next ) {
        try {
            if($_SESSION['is_admin'] == true) {
                $result = $next($request, $response);
            }
            else {
                throw new AdminStatusRequiredException();
            }
        }
         catch(AdminStatusRequiredException $exception) {
             $error = [
                 "error" => [
                     "message" => "Access forbidden : admin status is required."
                 ]
             ];
             $data= array_merge($error, $_SESSION);
             $result = $response->withStatus(403)
             ->withHeader('Content-Type', 'application/json')
             ->write(json_encode($data));
         }

         return $result;
    };


    $checkIfOwner= function($request, $response, $next ) {
        try {
                $route = $request->getAttribute('route');
                $routeName = $route->getName();
                $currentUserId = $_SESSION["currentUser_id"];
                if($routeName == "user") {
                    $idUserRequest = $route->getArguments()['id'];
                } else {
                    $idUserRequest = $request->getParam('user');
                }
                    
                if($currentUserId == $idUserRequest) {
                    $result = $next($request, $response);
                }
                else {
                    throw new NotAuthorizedException();
                }
        }
        catch(NotAuthorizedException $exception) {
            $error = [
                "error" => [
                    "message" => "Access forbidden : user must own the resource."
                ]
            ];
            $data= array_merge($error, $_SESSION);
            $result = $response->withStatus(403)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($data));
            }

        return $response;
    };