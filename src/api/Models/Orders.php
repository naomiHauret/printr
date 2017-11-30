<?php
    namespace App\Models;

    require "BaseModel.php";
    use BaseModel\BaseModel;
    use BaseModel\ResourcesNotFoundException;

    class Orders extends BaseModel {

        public function getAll($request, $response) {
            try {
                $statement = $this->db->prepare(
                "
                    SELECT orders._id, orders.order_userId, orders.order_cartId, orders.order_totalPrestations, orders.order_date, orders.order_dateDelivery, orders.order_deliveryAddress, orders.order_status, orders.order_total
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
                        SELECT * 
                        FROM prestations
                        WHERE prestation_cartId = :prestation_cartId
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
                    $carts[$i]= ["info" => $orders[$i], "prestations"=> $prestations];
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
                        "message" => "Order not found not found"
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

        public function add() {

        }

        public function update() {
            
        }

        public function delete() {
            
        }
    }