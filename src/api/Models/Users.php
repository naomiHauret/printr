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
                $users =[ "users" => $statement->fetchAll()];
                $data= array_merge($users, $_SESSION);
                $result= $this->response->withStatus(200)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }
          
            catch(PDOException $exception) {
                $error = [
                    "error" => [
                        "message" => "Bad request: users could not be fetched."
                    ]
                ];

                $data= array_merge($error, $_SESSION);

                $result = $this->response->withStatus(400)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
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
                    ":user_email" => $request->getParam("user_email"),
                    ":user_password" => $request->getParam("user_password")
                ));
                $user = $statement->fetch();
                if ($user) {
                    if($user["user_isClient"] == 1) {
                        // check if user has cart
                        $statement = $this->db->prepare(
                        "
                            SELECT * 
                            FROM carts
                            WHERE cart_userId = :cart_userId
                            AND cart_isOrdered = :cart_isOrdered
                        "
                        );
                        $statement->execute(array(
                            ":cart_userId" =>  $user["_id"],
                            ":cart_isOrdered" => 0
                        ));
                        $cart = $statement->fetch();

                        if(!$cart) {
                            $statement = $this->db->prepare(
                            "
                                INSERT INTO carts(
                                    cart_userId,
                                    cart_isOrdered
                                )
                                VALUES(
                                    :cart_userId,
                                    :cart_isOrdered
                                )
                            "
                            );
                            $queryResult = $statement->execute(array(
                                ":cart_userId" => $user["_id"],
                                ":cart_isOrdered" => 0
                            ));

                            $statement = $this->db->query("SELECT LAST_INSERT_ID()");
                            $cart_id = $statement->fetchColumn();
                        }

                        else {
                            $cart_id = $cart["_id"];
                        }
                    }
                    $_SESSION["current_user"] = [ 
                        "is_loggedIn" => true,
                        "is_admin" => $user["user_isClient"] == 0,
                        "id" => $user["_id"],
                        "cart_id" => $cart_id
                    ];
                   
                    $data= array_merge(["users"=>$user], $_SESSION);
                    $result = $this->response->withStatus(200)
                    ->withHeader("Content-Type", "application/json")
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
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }
            catch(PDOException $exception) {
                $error = [
                    "error" => [
                        "message" => "Bad request: users could not be fetched."
                    ]
                ];

                $data= array_merge($error, $_SESSION);

                $result = $this->response->withStatus(400)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }

            return $result;
        }

        //
        // Check and get one user  with given id
        //
        public function getOne($request, $response) {
            $user_id = $request->getAttribute("id");
            try {
                $statement = $this->db->prepare(
                    "
                        SELECT * 
                        FROM users
                        WHERE _id = :_id
                    "
                );
                $statement->execute(array(
                    ":_id" => $user_id
                ));
                $user = $statement->fetch();

                if ($user) {
                    $data= array_merge(["users" => [$user]], $_SESSION);
                    $result = $this->response->withStatus(200)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode($data));
                } else {
                    throw new ResourcesNotFoundException();
                }
            }
            catch(ResourcesNotFoundException $exception) {
                $error = [
                    "error" => [
                        "message" => "User not found "
                    ]
                ];

                $data= array_merge($error, $_SESSION);

                $result = $this->response->withStatus(404)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }
            catch(PDOException $exception) {
                $error = [
                    "error" => [
                        "message" => "Bad request: user could not be fetched due to an invalid parameter."
                    ]
                ];

                $data= array_merge($error, $_SESSION);

                $result = $this->response->withStatus(400)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
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
                    ":user_firstname" => $request->getParam("user_firstname"),
                    ":user_lastname" => $request->getParam("user_lastname"),
                    ":user_email" => $request->getParam("user_email"),
                    ":user_password" => $request->getParam("user_password"),
                    ":user_address" => $request->getParam("user_address") ,
                    ":user_isClient"=> $request->getParam("user_isClient") 
                ));
                $data= array_merge(["added"=> $queryResult], $_SESSION);
                $result = $this->response->withStatus(201)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }

            catch(PDOException $exception) {
                $error = [
                    "error" => [
                        "message" => "Bad request: option could not be added due to missing or invalid parameter."
                    ]
                ];

                $data= array_merge($error, $_SESSION);

                $result = $this->response->withStatus(400)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }

            return $result;
        }

        //
        // Update one user with given id
        //     
        public function update($request, $response) {
            try {
                $statement = $this->db->prepare(
                "
                    SELECT * 
                    FROM users
                    WHERE _id = :_id
                "
                );
                $statement->execute(array(
                    ":_id" => $request->getAttribute("id")
                ));
                $user = $statement->fetch();

                if ($user) {
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
                        "user_firstname" => $request->getParam("user_firstname") ,
                        "user_lastname" => $request->getParam("user_lastname"),
                        "user_email" => $request->getParam("user_email"),
                        "user_password" => $request->getParam("user_password"),
                        "user_address" => $request->getParam("user_address"),
                        "user_isClient" => $request->getParam("user_isClient"),
                        "_id" => $request->getAttribute("id")
                    ));
                    $data= array_merge(["updated"=> $queryResult], $_SESSION);
                    $result = $this->response->withStatus(200)
                    ->withHeader("Content-Type", "application/json")
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
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }
            catch(PDOException $exception) {
                $error = [
                    "error" => [
                        "message" => "Bad request: user could not be updated due to missing or invalid parameters."
                    ]
                ];

                $data= array_merge($error, $_SESSION);

                $result = $this->response->withStatus(400)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }

            return $result;
        }

        //
        // Delete one user with given id
        //     
        public function delete($request, $response) {
            $user_id =  $request->getAttribute("id");

            try {
                $statement = $this->db->prepare(
                "
                    SELECT * 
                    FROM users
                    WHERE _id = :_id
                "
                );
                $statement->execute(array(
                    ":_id" => $user_id
                ));

                $user = $statement->fetch();

                if ($user) {
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
                    $data= array_merge(["deleted"=> $queryResult], $_SESSION);
                    $result = $this->response->withStatus(204)
                    ->withHeader("Content-Type", "application/json")
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
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }
            catch(PDOException $exception) {
                $error = [
                    "error" => [
                        "message" => "Bad request: user could not be deleted due to invalid parameter."
                    ]
                ];

                $data= array_merge($error, $_SESSION);

                $result = $this->response->withStatus(400)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }

            return $result;
        }


                //
        // Get car of user with given id
        //     
        public function getCart($request, $response) {
            $user_id= $request->getAttribute("id");

            try {
                $statement = $this->db->prepare(
                    "
                        SELECT * 
                        FROM users
                        WHERE _id = :_id
                    "
                );
                $statement->execute(array(
                    ":_id" => $user_id
                ));
                $user = $statement->fetch();

                if ($user) {
                    $statement = $this->db->prepare(
                        "
                            SELECT *
                            FROM carts
                            WHERE cart_userId = :cart_userId
                            AND cart_isOrdered = 0
                        "
                    );
                    $statement->execute(array(
                        ":cart_userId" => $user_id
                    ));
                    $cart = $statement->fetch();

                    if ($cart) {
                        $cart_id= $cart["_id"];
                        $statement = $this->db->prepare(
                            "
                                SELECT *
                                FROM prestations
                                WHERE prestation_cartId = :prestation_cartId
                            "
                        );
                        $statement->execute(array(
                            ":prestation_cartId" => $cart_id
                        ));
                        $prestations = $statement->fetchAll();

                        if (count($prestations) >= 0) {
                            if (count($prestations) > 0) {
                                for ($i = 0; $i < count($prestations); $i++) {
                                    $prestation_id= $prestations[$i]["_id"];

                                    $statement = $this->db->prepare(
                                        "
                                            SELECT option_name, option_price, option_category, prestation_options._id AS prestation_option_id
                                            FROM options INNER JOIN prestation_options
                                            ON prestation_options.option_id = options._id
                                            WHERE prestation_options.prestation_id = :prestation_id
                                        "
                                    );
                                    $statement->execute(array(
                                        ":prestation_id" => $prestation_id
                                    ));

                                    $prestationsOptions= $statement->fetchAll();
                                    $prestations[$i]= ["info" => $prestations[$i], "options"=> $prestationsOptions];
                                }
                            }

                            $cart = ["infos" => $cart, "prestations" => $prestations];
                            $data= array_merge(["carts" => [$cart]], $_SESSION);
                            $result = $this->response->withStatus(200)
                            ->withHeader("Content-Type", "application/json")
                            ->write(json_encode($data));
                        } else {
                            throw new ResourcesNotFoundException();
                        }
                    } else {
                        throw new ResourcesNotFoundException();
                    }

                } else {
                    throw new ResourcesNotFoundException();
                }
            }
            catch(ResourcesNotFoundException $exception) {
                $error = [
                    "error" => [
                        "message" => "Could not fetch cart for this user: user not found "
                    ]
                ];

                $data= array_merge($error, $_SESSION);

                $result = $this->response->withStatus(404)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }
            catch(PDOException $exception) {
                $error = [
                    "error" => [
                        "message" => "Bad request: cart could not be fetched due to missing or invalid parameters."
                    ]
                ];

                $data= array_merge($error, $_SESSION);

                $result = $this->response->withStatus(400)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }

            return $result;
        }

    }