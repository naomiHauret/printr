<?php
    namespace App\Models;

    require "BaseModel.php";
    use BaseModel\BaseModel;

    class Prestations extends BaseModel {
        //
        // Get all prestations
        //
        public function getAll($request, $response) {
            try {
                $statement = $this->db->prepare(
                "
                    SELECT * 
                    FROM prestations
                "
                );
                $statement->execute();
                $prestations = $statement->fetchAll();
                $data= array_merge(["prestations" => $prestations], $_SESSION);
                $result= $this->response->withStatus(200)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }
          
            catch(PDOException $exception) {
                $error = [
                    "error" => [
                        "message" => "Bad request: prestations could not be fetched."
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
        // Get prestation with given id
        //
        public function getOne($request, $response) {
            $prestation_id= $request->getAttribute("id");
            try {
                // check if prestation exists
                $statement = $this->db->prepare(
                "
                    SELECT * 
                    FROM prestations
                    WHERE _id = :_id
                "
                );
                $statement->execute(array(
                    ":_id" => $prestation_id
                ));
                $prestation = $statement->fetch();

                if ($prestation) {
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
                    $prestation= ["info" => $prestation, "options"=> $prestationsOptions];

                    $data= array_merge(["prestations" => [$prestation]], $_SESSION);
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
                        "message" => "Prestation not found"
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
                        "message" => "Bad request: prestation could not be updated due to invalid parameter."
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
        // Add an option to given prestation
        //
        public function addOption($request, $response) {
            $prestation_id= $request->getAttribute("id");
            $option_id= $request->getParam("option_id");

            try {    
                $statement = $this->db->prepare(
                    "
                        SELECT * 
                        FROM prestations
                        WHERE _id = :_id
                    "
                );
                $statement->execute(array(
                    ":_id" => $prestation_id,
                ));
                $prestation = $statement->fetch();

                if($prestation) {
                    $statement = $this->db->prepare(
                        "
                            SELECT * 
                            FROM options
                            WHERE _id = :_id
                        "
                    );
                    $statement->execute(array(
                        ":_id" => $option_id,
                    ));
                    $option = $statement->fetch();

                    if($option) {
                        $statement = $this->db->prepare(
                            "
                                INSERT INTO prestation_options(
                                    prestation_id,
                                    option_id
                                )
                                VALUES(
                                    :prestation_id,
                                    :option_id
                                )
                            "
                        );
                        $queryResult = $statement->execute(array(
                            ":prestation_id" => $prestation_id,
                            ":option_id" => $option_id,
                        ));

                        $data= array_merge(["inserted"=> $queryResult], $_SESSION);
                        $result = $this->response->withStatus(201)
                        ->withHeader("Content-Type", "application/json")
                        ->write(json_encode($data));
                    }
                    else {
                        throw new ResourcesNotFoundException();
                    }
                }

                else {
                    throw new ResourcesNotFoundException();
                }
            }
            catch(ResourcesNotFoundException $exception) {
                $error = [
                    "error" => [
                        "message" => "Prestation or option not found"
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
                        "message" => "Bad request: option could not be added due to missing or invalid parameters."
                    ]
                ];

                $data= array_merge($error, $_SESSION);

                $result = $this->response->withStatus(400)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }       
            
            return $result;
        }
        

        // Update option with given id of prestation
        public function updateOption($request, $response) {
            $prestation_option_id= $request->getAttribute("option_id");

            try {    
                $statement = $this->db->prepare(
                    "
                        SELECT * 
                        FROM prestation_options
                        WHERE _id = :_id
                    "
                );
                $statement->execute(array(
                    ":_id" => $prestation_option_id,
                ));
                $prestation_option = $statement->fetch();

                if($prestation_option) {
                    $statement = $this->db->prepare(
                        "
                            UPDATE prestation_options 
                            SET 
                                option_id = :option_id
                            WHERE _id = :_id
                        "
                    );
                    $queryResult = $statement->execute(array(
                        ":_id" => $prestation_option_id,
                        ":option_id" => $request->getParam("option_id"),
                    ));

                    $data= array_merge(["updated"=> $queryResult], $_SESSION);
                    $result = $this->response->withStatus(201)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode($data));
                    }
                else {
                    throw new ResourcesNotFoundException();
                }
            }
            catch(ResourcesNotFoundException $exception) {
                $error = [
                    "error" => [
                        "message" => "Option of prestation not found"
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
                        "message" => "Bad request: option could not be added due to missing or invalid parameters."
                    ]
                ];

                $data= array_merge($error, $_SESSION);

                $result = $this->response->withStatus(400)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }       
            
            return $result;
        }

        // Delete option with given id of prestation
        public function deleteOption($request, $response) {
            $prestation_option_id= $request->getAttribute("option_id");

            try {    
                $statement = $this->db->prepare(
                    "
                        SELECT * 
                        FROM prestation_options
                        WHERE _id = :_id
                    "
                );
                $statement->execute(array(
                    ":_id" => $prestation_option_id,
                ));
                $prestation_option = $statement->fetch();

                if($prestation_option) {
                    $statement = $this->db->prepare(
                        "
                            DELETE FROM prestations_options WHERE _id = :_id
                        "
                    );
                    $queryResult = $statement->execute(array(
                        ":_id" => $prestation_option_id,
                    ));

                    $data= array_merge(["deleted"=> $queryResult], $_SESSION);
                    $result = $this->response->withStatus(204)
                    ->withHeader("Content-Type", "application/json")
                    ->write(json_encode($data));
                    }
                else {
                    throw new ResourcesNotFoundException();
                }
            }
            catch(ResourcesNotFoundException $exception) {
                $error = [
                    "error" => [
                        "message" => "Option of prestation not found"
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
                        "message" => "Bad request: option could not be added due to missing or invalid parameters."
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
        // Add a prestation to a cart
        //
        public function add($request, $response) {
            try {
                // get cart of current user
                $statement = $this->db->prepare(
                    "
                        SELECT * 
                        FROM carts
                        WHERE cart_userId = :cart_userId
                        AND cart_isOrdered = :cart_isOrdered
                    "
                );
                $statement->execute(array(
                    ":cart_userId" => $_SESSION["current_user"]["id"],
                    ":cart_isOrdered" => 0
                ));
                $cart = $statement->fetch();
                $cart_id= $cart["_id"];

                // Add prestation to cart
                $statement = $this->db->prepare(
                "
                    INSERT INTO prestations(
                        prestation_cartId,
                        prestation_formatId,
                        prestation_iconPath,
                        prestation_filePath,
                        prestation_quantity,
                        prestation_isAvailable 
                    )
                    VALUES(
                        :prestation_cartId,
                        :prestation_formatId,
                        :prestation_iconPath,
                        :prestation_filePath,
                        :prestation_quantity,
                        :prestation_isAvailable 
                    )
                "
                );
                 $queryResult = $statement->execute(array(
                    ":prestation_cartId" => $cart_id,
                    ":prestation_formatId" => $request->getParam("prestation_formatId"),
                    ":prestation_iconPath" => $request->getParam("prestation_iconPath"),
                    ":prestation_filePath" => $request->getParam("prestation_filePath"),
                    ":prestation_quantity" => $request->getParam("prestation_quantity"),
                    ":prestation_isAvailable" => $request->getParam("prestation_isAvailable")
                ));

                $data= array_merge(["inserted"=> $queryResult], $_SESSION);
                $result = $this->response->withStatus(201)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }

            catch(PDOException $exception) {
                $error = [
                    "error" => [
                        "message" => "Bad request: prestation could not be added due to missing or invalid parameters."
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
        // Update prestation with given id
        //
        public function update($request, $response) {
            $prestation_id= $request->getAttribute("id");
            try {
                // check if prestation exists
                $statement = $this->db->prepare(
                "
                    SELECT * 
                    FROM prestations
                    WHERE _id = :_id
                "
                );
                $statement->execute(array(
                    ":_id" => $prestation_id
                ));

                $prestation = $statement->fetch();

                // if prestation exists, update it
                if ($prestation) {
                    $query=  $this->db->prepare(
                    "   
                        UPDATE prestations 
                        SET 
                                prestation_formatId = :prestation_formatId,
                                prestation_iconPath = :prestation_iconPath,
                                prestation_filePath = :prestation_filePath,
                                prestation_quantity = :prestation_quantity,
                                prestation_isAvailable = :prestation_isAvailable                                
                        WHERE _id = :_id
                    ");
                    $queryResult= $query->execute(array(
                        "prestation_formatId" => $request->getParam("prestation_formatId"),
                        "prestation_iconPath" => $request->getParam("prestation_iconPath"),
                        "prestation_filePath" => $request->getParam("prestation_filePath"),
                        "prestation_quantity" => $request->getParam("prestation_quantity"),
                        "prestation_isAvailable" => $request->getParam("prestation_isAvailable"),
                        "_id" => $prestation_id
                    ));

                    $data= array_merge([$queryResult], $_SESSION);
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
                        "message" => "Prestation not found"
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
                        "message" => "Bad request: prestation could not be updated due to invalid parameter."
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
        //  Delete prestation with given id
        //
        public function delete($request, $response) {
            $prestation_id= $request->getAttribute("id");
            try {
                // check if prestation exists
                $statement = $this->db->prepare(
                    "
                        SELECT * 
                        FROM prestations
                        WHERE _id = :_id
                    "
                );
                $statement->execute(array(
                    ":_id" => $prestation_id
                ));
                $prestation = $statement->fetch();

                if ($prestation) {

                    // delete related options
                    $statement = $this->db->prepare(
                        "
                            DELETE FROM prestations_options WHERE prestation_id = :prestation_id
                        "
                    );
                    $queryResult = $statement->execute(array(
                        ":prestation_id" => $prestation_id
                    ));

                    // delete prestation
                    $statement = $this->db->prepare(
                        "
                            DELETE FROM prestations WHERE _id = :_id
                        "
                    );
                    $queryResult = $statement->execute(array(
                        ":_id" => $prestation_id
                    ));

                    $data= array_merge([$queryResult], $_SESSION);
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
                        "message" => "Prestation not found"
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
                        "message" => "Bad request: prestation could not be deleted due to invalid parameter."
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

    