<?php
    namespace App\Models;

    require "BaseModel.php";
    use BaseModel\BaseModel;
    use BaseModel\ResourcesNotFoundException;

    class Orders extends BaseModel {
        //
        // Get all orders
        //
        public function getAll($request, $response) {
            try {
                $statement = $this->db->prepare(
                    "
                        SELECT
                            orders._id,
                            orders.order_userId,
                            orders.order_cartId,
                            orders.order_date,
                            orders.order_dateDelivery,
                            orders.order_deliveryAddress,
                            orders.order_status
                        FROM carts INNER JOIN orders
                        ON carts._id = orders.order_cartId
                    "
                );
                
                 $statement->execute();
                
                $orders = $statement->fetchAll();

                for($i= 0; $i < count($orders); $i++) {
                    // get related prestations
                    $statement = $this->db->prepare(
                        "
                            SELECT
                                prestations._id,
                                prestations.prestation_cartId,
                                prestations.prestation_formatId,
                                prestations.prestation_filePath,
                                prestations.prestation_quantity,
                                formats.format_name,
                                formats.format_dimensions,
                                formats.format_price,
                                formats.format_iconPath
                            FROM prestations
                            INNER JOIN formats
                            ON prestations.prestation_formatId = formats._id
                            WHERE prestations.prestation_cartId = :prestation_cartId
                        "
                    );
                    $statement->execute(array(
                        ":prestation_cartId" => $orders[$i]["order_cartId"]
                    ));
                    $prestations = $statement->fetchAll();

                    // for each prestations, get chosen options
                    for ($k = 0; $k < count($prestations); $k++) {
                        $prestation_id= $prestations[$k]["_id"];

                        $statement = $this->db->prepare(
                            "
                                SELECT
                                    option_name,
                                    option_price,
                                    option_category,
                                    prestation_options._id AS prestation_option_id
                                FROM options INNER JOIN prestation_options
                                ON prestation_options.option_id = options._id
                                WHERE prestation_options.prestation_id = :prestation_id
                            "
                        );
                        $statement->execute(array(
                            ":prestation_id" => $prestation_id
                        ));

                        $prestationsOptions= $statement->fetchAll();
                        $prestations[$k]= ["info" => $prestations[$k], "options"=> $prestationsOptions];
                    }
                    $orders[$i]= ["info" => $orders[$i], "prestations"=> $prestations];
                }

                $data= array_merge(["orders" => $orders], $_SESSION);
                $result= $this->response->withStatus(200)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }
          
            catch(PDOException $exception) {
                $error = [
                    "error" => [
                        "message" => "Bad request: orders could not be fetched."
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
        // Get order with given id
        //
        public function getOne($request, $response) {
            $order_id = $request->getAttribute("id");

            try {
                // get cart
                $statement = $this->db->prepare(
                    "
                        SELECT * 
                        FROM orders
                        WHERE _id = :_id
                    "
                );
                $statement->execute(array(
                    ":_id" => $order_id
                ));
                $order = $statement->fetch();

                // if order exist
                if($order) {
                    $cart_id = $order["order_cartId"];
                    // get related prestations
                    $statement = $this->db->prepare(
                        "
                            SELECT
                                prestations._id,
                                prestations.prestation_cartId,
                                prestations.prestation_formatId,
                                prestations.prestation_filePath,
                                prestations.prestation_quantity,
                                formats.format_name,
                                formats.format_dimensions,
                                formats.format_price,
                                formats.format_iconPath
                            FROM prestations
                            INNER JOIN formats
                            ON prestations.prestation_formatId = formats._id
                            WHERE prestations.prestation_cartId = :prestation_cartId
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
                                SELECT
                                    option_name,
                                    option_price,
                                    option_category
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


                    $data= array_merge(["orders" => [["info"=>$order, "prestations"=>[$prestations]]]], $_SESSION);
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
                        "message" => "Order not found"
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
                        "message" => "Bad request: order could not be fetched due to an invalid parameter."
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
        // Add order
        //
        public function add($request, $response) {
            $cart_id= $_SESSION["current_user"]["cart_id"];
            try {
                // get cart
                $statement = $this->db->prepare(
                    "
                        SELECT * 
                        FROM carts
                        WHERE _id = :_id
                        AND cart_isOrdered = 0
                    "
                );
                $statement->execute(array(
                    ":_id" => $cart_id
                ));
                $cart = $statement->fetch();

                // if cart exist and isn't ordered
                if ($cart) {
                    $statement = $this->db->prepare(
                        "
                            INSERT INTO orders(
                                order_userId,
                                order_cartId,
                                order_deliveryAddress
                            )
                            VALUES(
                                :order_userId,
                                :order_cartId,
                                :order_deliveryAddress
                            )
                        "
                    );
                    $order = $statement->execute(array(
                        ":order_userId" => $_SESSION["current_user"]["id"] ,
                        ":order_cartId" => $cart_id,
                        ":order_deliveryAddress" => $request->getParam("order_deliveryAddress")
                    ));

                    if($order) {
                        // update cart status
                        $query=  $this->db->prepare(
                            "   
                                UPDATE carts 
                                SET 
                                    cart_isOrdered = :cart_isOrdered
                                WHERE _id = :_id
                            "
                        );
                        $cart= $query->execute(array( 
                            ":_id" => $cart_id,
                            "cart_isOrdered" => 1
                        ));

                        if($cart) {
                            // create new cart for this user
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
                            $newCart = $statement->execute(array(
                                ":cart_userId" => $_SESSION["current_user"]["id"],
                                ":cart_isOrdered" => 0
                            ));
                            if($newCart) {
                                $statement = $this->db->query("SELECT LAST_INSERT_ID()");
                                $cart_id = $statement->fetchColumn();
                                $_SESSION["current_user"] = [ 
                                    "cart_id" => $cart_id
                                ];
                                $data= array_merge(["inserted"=> $queryResult], $_SESSION);
                                $result = $this->response->withStatus(201)
                                ->withHeader("Content-Type", "application/json")
                                ->write(json_encode($data));
                            }
                        }
                    }
                }
                else {
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
                        "message" => "Bad request: order could not be added due to missing or invalid parameters."
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
        // Update order with given id
        //
        public function update($request, $response) {
            $order_id = $request->getAttribute("id");

            try {
                // get cart
                $statement = $this->db->prepare(
                    "
                        SELECT * 
                        FROM orders
                        WHERE _id = :_id
                    "
                );
                $statement->execute(array(
                    ":_id" => $order_id
                ));
                $order = $statement->fetch();

                // if order exist
                if($order) {
                    $query=  $this->db->prepare(
                        "   
                            UPDATE orders 
                            SET 
                                order_dateDelivery = :order_dateDelivery,
                                order_deliveryAddress = :order_deliveryAddress,
                                order_status = :order_status
                            WHERE _id = :_id
                        "
                    );
                    $queryResult= $query->execute(array( 
                        ":_id" => $order_id,
                        ":order_dateDelivery" => $request->getParam("order_dateDelivery"),
                        ":order_deliveryAddress" => $request->getParam("order_deliveryAddress"),
                        ":order_status" => $request->getParam("order_status")
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
                        "message" => "Order not found"
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
                        "message" => "Bad request: order could not be fetched due to an invalid parameter."
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
        // Delete order with given id
        //
        public function delete($request, $response) {
            
        }
    }