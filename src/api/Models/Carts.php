<?php
    namespace App\Models;

    require "BaseModel.php";
    use BaseModel\BaseModel;
    use BaseModel\ResourcesNotFoundException;

    class Carts extends BaseModel {

        public function getAll($request, $response) {
            try {
                $statement = $this->db->prepare(
                "
                    SELECT * 
                    FROM carts
                "
                );
                $statement->execute();
                $carts = $statement->fetchAll();

                for($i= 0; $i < count($carts); $i++) {
                    // get related prestations
                    $statement = $this->db->prepare(
                    "
                        SELECT * 
                        FROM prestations
                        WHERE prestation_cartId = :prestation_cartId
                    "
                    );
                    $statement->execute(array(
                        ":prestation_cartId" => $carts[$i]["_id"]
                    ));
                    $prestations = $statement->fetchAll();

                    // for each prestations, get chosen options
                    for ($k = 0; $k < count($prestations); $k++) {
                        $prestation_id= $prestations[$k]["_id"];

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
                        $prestations[$k]= ["info" => $prestations[$i], "options"=> $prestationsOptions];
                    }
                    $carts[$i]= ["info" => $carts[$i], "prestations"=> $prestations];
                }

                $data= array_merge(["carts" => $carts], $_SESSION);
                $result= $this->response->withStatus(200)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }
          
            catch(PDOException $exception) {
                $error = [
                    "error" => [
                        "message" => "Bad request: carts could not be fetched."
                    ]
                ];

                $data= array_merge($error, $_SESSION);

                $result = $this->response->withStatus(400)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }
            return $result;
        }


        public function getOne($request, $response) {
            $cart_id = $request->getAttribute("id");

            try {
                // get cart
                $statement = $this->db->prepare(
                "
                    SELECT * 
                    FROM carts
                    WHERE _id = :_id
                "
                );
                $statement->execute(array(
                    ":_id" => $cart_id
                ));
                $cart = $statement->fetch();

                // if cart exist
                if ($cart) {

                    // get related prestations
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

                    // for each prestations, get chosen options
                    for ($i = 0; $i < count($prestations); $i++) {
                        $prestation_id= $prestations[$i]["_id"];

                        $statement = $this->db->prepare(
                            "
                                SELECT option_name, option_price, option_category
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


                    $data= array_merge(["carts" => ["info"=>$cart, "prestations"=>[$prestations]]], $_SESSION);
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
                        "message" => "Cart not found not found"
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
                        "message" => "Bad request: cart could not be fetched due to an invalid parameter."
                    ]
                ];

                $data= array_merge($error, $_SESSION);

                $result = $this->response->withStatus(400)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }
            return $result;
        }

        public function add($request, $response) {
            try {
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
                    ":cart_userId" => $_SESSION["current_user"]["id"] ,
                    ":cart_isOrdered" => 0
                ));

                $data= array_merge(["inserted"=> $queryResult], $_SESSION);
                $result = $this->response->withStatus(201)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }

            catch(PDOException $exception) {
                $error = [
                    "error" => [
                        "message" => "Bad request: cart could not be added due to missing or invalid parameters."
                    ]
                ];

                $data= array_merge($error, $_SESSION);

                $result = $this->response->withStatus(400)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }       
            
            return $result;
        }

        public function update($request, $response) {
            $cart_id= $request->getAttribute("id");

            try {
                $statement = $this->db->prepare(
                "
                    SELECT * 
                    FROM carts
                    WHERE _id = :_id
                "
                );
                $statement->execute(array(
                    ":_id" => $cart_id
                ));
                $cart = $statement->fetch();

                if ($cart) {
                    $query=  $this->db->prepare(
                    "   
                        UPDATE carts 
                        SET 
                            cart_isOrdered = :cart_isOrdered
                        WHERE _id = :_id
                    ");
                    $queryResult= $query->execute(array( 
                        ":_id" => $cart_id,
                        "cart_isOrdered" => $request->getParam("cart_isOrdered")
                    ));

                    $data= array_merge([$queryResult], $_SESSION);
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
                        "message" => "Format not found"
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
                        "message" => "Bad request: cart could not be updated due to missing or invalid parameters."
                    ]
                ];

                $data= array_merge($error, $_SESSION);

                $result = $this->response->withStatus(400)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }
            return $result;
        }

        public function delete($request, $response) {
            $cart_id= $request->getAttribute("id");
            try {
                $statement = $this->db->prepare(
                "
                    SELECT * 
                    FROM carts
                    WHERE _id = :_id
                "
                );
                $statement->execute(array(
                    ":_id" => $cart_id
                ));
                $cart = $statement->fetch();

                if ($cart) {

                    // get all prestations of this cart
                    $statement = $this->db->prepare(
                    "
                        SELECT * FROM prestations WHERE prestation_cartId = :prestation_cartId
                    "
                    );
                    $queryResult = $statement->execute(array(
                        ":prestation_cartId" => $cart_id
                    ));
                    $prestations = $queryResult->fetchAll();

                    // delete related options in intermediary table
                    foreach($prestations as $prestations) {
                        $statement = $this->db->prepare(
                        "
                            DELETE FROM prestation_options WHERE prestation_id = :prestation_id
                        "
                        );
                        $queryResult = $statement->execute(array(
                            ":prestation_id" => $prestation["_id"]
                        ));
                    }

                    // delete prestations related to cart
                    $statement = $this->db->prepare(
                    "
                        DELETE FROM prestations WHERE prestation_cartId = :prestation_cartId
                    "
                    );
                    $queryResult = $statement->execute(array(
                        ":prestation_cartId" => $cart_id
                    ));

                    // delete cart
                    $statement = $this->db->prepare(
                    "
                        DELETE FROM carts WHERE _id = :_id
                    "
                    );
                    $queryResult = $statement->execute(array(
                        ":_id" => $cart_id
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
                        "message" => "Cart not found"
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
                        "message" => "Bad request: cart could not be deleted due to invalid parameter."
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