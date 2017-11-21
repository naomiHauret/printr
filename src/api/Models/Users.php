<?php
    namespace App\Models;
  
    require_once "BaseModel.php";
    use BaseModel\BaseModel;
    use BaseModel\ResourcesNotFoundException;

    class Users extends BaseModel {
        //
        // Get all users
        //
        public function getAll($request, $response) {
            try {
                $statement = $this->db->prepare(
                "
                    SELECT * 
                    FROM users
                    ORDER BY user_lastname
                "
                );
                $statement->execute();
                $users = $statement->fetchAll();
                $data= array_merge($users, $_SESSION);
                $result= $this->response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($data));
            }
          
            catch(PDOException $exception) {
                $result= $exception->getMessage();
            }
          return $result;
        }


        //
        // Get one user with given creditentials
        //
        public function verifyAccount($request, $response) {
            try {
                $statement = $this->db->prepare(
                "
                    SELECT * 
                    FROM users
                    USERS WHERE user_email = :user_email 
                    AND user_password = :user_password
                "
                );
                $statement->execute(array(
                    ':user_email' => $request->getParam('user_email'),
                    ':user_password' => $request->getParam('user_password')
                ));
                $user = $statement->fetch();
                if ($user) {
                    $_SESSION["is_loggedIn"] = true;
                    $_SESSION["is_admin"] = $user["user_isClient"] == 0;
                    $_SESSION["currentUser_id"] = $user["_id"];                    
                    $data= array_merge($user, $_SESSION);
                    $result = $this->response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
                } else {
                    throw new ResourcesNotFoundException();
                }
            }
            catch(ResourcesNotFoundException $exception) {
                $error = [
                    "error" => [
                        "message" => "User not found"
                    ]
                ];

                $data= array_merge($error, $_SESSION);

                $result = $this->response->withStatus(404)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($data));
            }
            catch(PDOException $exception) {
                $result = $exception->getMessage();
            }

            return $result;
        }

        //
        // Check and get one user  with given id
        //
        public function getOne($request, $response) {
            try {
                $statement = $this->db->prepare(
                "
                    SELECT * 
                    FROM users
                    WHERE _id = :_id
                "
                );
                $statement->execute(array(
                    ':_id' => $request->getAttribute('id')
                ));
                $user = $statement->fetch();

                if ($user) {
                    $data= array_merge($user, $_SESSION);
                    $result = $this->response->withStatus(200)
                    ->withHeader('Content-Type', 'application/json')
                    ->write(json_encode($data));
                } else {
                    throw new ResourcesNotFoundException();
                }
            }
            catch(ResourcesNotFoundException $exception) {
                $error = [
                    "error" => [
                        "message" => "User not found"
                    ]
                ];

                $data= array_merge($error, $_SESSION);

                $result = $this->response->withStatus(404)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($data));
            }
            catch(PDOException $exception) {
                $result = $exception->getMessage();
            }
            return $result;
        }

        //
        // Add one user
        //        
        public function add($request, $response) {
            try {
                $statement = $this->db->prepare(
                "
                    INSERT INTO users(
                        user_firstname,
                        user_lastname,
                        user_email,
                        user_password,
                        user_address,
                        user_isClient
                    )
                    VALUES (
                        :user_firstname,
                        :user_lastname,
                        :user_email,
                        :user_password,
                        :user_address,
                        :user_isClient
                    )
                "
                );
                 $queryResult = $statement->execute(array(
                    ":user_firstname" => $request->getParam('user_firstname'),
                    ":user_lastname" => $request->getParam('user_lastname'),
                    ":user_email" => $request->getParam('user_email'),
                    ":user_password" => $request->getParam('user_password'),
                    ":user_address" => $request->getParam('user_address') ,
                    ":user_isClient"=> $request->getParam('user_isClient') 
                ));
                $data= array_merge([$queryResult], $_SESSION);
                $result = $this->response->withStatus(201)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($data));
            }

            catch(PDOException $exception) {
                $result = $exception;
            }

            return $result;
        }

        //
        // Update one user with given id
        //     
        public function update($request, $response) {
            try { 
                $query=  $this->db->prepare(
                "   
                    UPDATE users 
                    SET 
                            user_firstname = :user_firstname,
                            user_lastname = :user_lastname,
                            user_email = :user_email,
                            user_password = :user_password,
                            user_address = :user_address,
                            user_isClient = :user_isClient  
                    WHERE _id = :_id
                ");
                $queryResult= $query->execute(array(
                    'user_firstname' => $request->getParam('user_firstname') ,
                    'user_lastname' => $request->getParam('user_lastname'),
                    'user_email' => $request->getParam('user_email'),
                    'user_password' => $request->getParam('user_password'),
                    'user_address' => $request->getParam('user_address'),
                    'user_isClient' => $request->getParam('user_isClient'),
                    '_id' => $request->getAttribute('id')
                ));
                $data= array_merge([$queryResult], $_SESSION);
                $result = $this->response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($data));
            }

            
            catch(PDOException $exception) {
                $result = $exception->getMessage();
            }

            return $result;
        }

        //
        // Delete one user with given id
        //     
        public function delete($request, $response) {            
            try {
                $user_id =  $request->getAttribute('id');
                $statement = $this->db->prepare(
                "
                    DELETE FROM carts WHERE cart_userId = :cart_userId
                "
                );
                 $queryResult = $statement->execute(array(
                    ":cart_userId" => $user_id
                ));
                $statement = $this->db->prepare(
                "
                    DELETE FROM users WHERE _id = :_id
                "
                );
                 $queryResult = $statement->execute(array(
                    ":_id" => $user_id
                ));
                $data= array_merge([$queryResult], $_SESSION);
                $result = $this->response->withStatus(204)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($data));
            }

            catch(PDOException $exception) {
                $result = $exception->getMessage();
            }

            return $result;
        }
    }