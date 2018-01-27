<?php

    namespace App\Models;
    require "BaseModel.php";
    use BaseModel\BaseModel;
    use BaseModel\ResourcesNotFoundException;

    class Options extends BaseModel {

        //
        // Get all options
        //     
        public function getAll($request, $response) {
            try {
                $statement = $this->db->prepare(
                    "
                        SELECT option_category
                        FROM options
                        GROUP BY option_category 
                    "
                );
                $statement->execute();
                $optionsCategories = $statement->fetchAll();
                $options= [];
                for ($i = 0; $i < count($optionsCategories); $i++) {
                    $category= $optionsCategories[$i]["option_category"];
                    $statement = $this->db->prepare(
                        "
                            SELECT options._id, options.option_name, options.option_price
                            FROM options
                            WHERE options.option_category = :option_category
                        "
                    );
                    $statement->execute(array(
                        ":option_category" =>  $category
                    ));
                    $matchingOptions = $statement->fetchAll();
                     $options[$i]= ["category" => $category, "choices" => $matchingOptions];
                }
                $data= array_merge(["options"=> $options], $_SESSION);
                $result= $this->response->withStatus(200)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }
          
            catch(PDOException $exception) {
                $error = [
                    "error" => [
                        "message" => "Bad request: options could not be fetched."
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
        // Get one option with given id
        //     
        public function getOne($request, $response) {
            $option_id= $request->getAttribute("id");

            try {
                $statement = $this->db->prepare(
                    "
                        SELECT * 
                        FROM options
                        WHERE _id = :_id
                    "
                );
                $statement->execute(array(
                    ":_id" =>  $option_id
                ));
                $option = $statement->fetch();

                if ($option) {
                    $data= array_merge(["options"=> [$option]], $_SESSION);
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
                        "message" => "Option not found"
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
                        "message" => "Bad request: option could not be fetched due to an invalid parameter."
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
        // Add one option
        //     
        public function add($request, $response) {
            try {
                $statement = $this->db->prepare(
                    "
                        INSERT INTO options(
                            option_name,
                            option_price,
                            option_category
                        )
                        VALUES(
                            :option_name,
                            :option_price,
                            :option_category
                        )
                    "
                );
                 $queryResult = $statement->execute(array(
                    ":option_name" => $request->getParam("option_name"),
                    ":option_price" => $request->getParam("option_price"),
                    ":option_category" => $request->getParam("option_category")
                ));

                $data= array_merge(["added"=> $queryResult], $_SESSION);
                $result = $this->response->withStatus(201)
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
        // Update one option with given id
        //     
        public function update($request, $response) {
            $option_id= $request->getAttribute("id");

            try {
                $statement = $this->db->prepare(
                    "
                        SELECT * 
                        FROM options
                        WHERE _id = :_id
                    "
                );
                $statement->execute(array(
                    ":_id" => $option_id
                ));
                $option = $statement->fetch();

                if ($option) {
                    $query=  $this->db->prepare(
                        "   
                            UPDATE options 
                            SET 
                                    option_name = :option_name,
                                    option_price = :option_price,
                                    option_category = :option_category
                            WHERE _id = :_id
                        "
                    );
                    $queryResult= $query->execute(array(
                        "option_name" => $request->getParam("option_name") ,
                        "option_price" => $request->getParam("option_price"),
                        "option_category" => $request->getParam("option_category"),
                        "_id" => $option_id
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
                        "message" => "Option not found"
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
                        "message" => "Bad request: option could not be updated due to missing or invalid parameters."
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
        // Delete one option with given id
        //     
        public function delete($request, $response) {
            $option_id= $request->getAttribute("id");
            try {
                $statement = $this->db->prepare(
                    "
                        SELECT * 
                        FROM options
                        WHERE _id = :_id
                    "
                );
                $statement->execute(array(
                    ":_id" => $option_id
                ));
                $option = $statement->fetch();

                if ($option) {
                    $statement = $this->db->prepare(
                        "
                            DELETE FROM options
                            WHERE _id = :_id
                        "
                    );
                    $queryResult = $statement->execute(array(
                        ":_id" => $option_id
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
                        "message" => "Option not found"
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
                        "message" => "Bad request: option could not be deleted due to invalid parameter."
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