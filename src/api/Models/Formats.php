<?php

    namespace App\Models;
    require "BaseModel.php";
    use BaseModel\BaseModel;
    use BaseModel\ResourcesNotFoundException;

    class Formats extends BaseModel {

        //
        // Get all formats
        //     
        public function getAll($request, $response) {
            try {
                $statement = $this->db->prepare(
                    "
                        SELECT * 
                        FROM formats
                    "
                );
                $statement->execute();
                $formats = $statement->fetchAll();
                $data= array_merge(["formats"=> $formats], $_SESSION);
                $result= $this->response->withStatus(200)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }
          
            catch(PDOException $exception) {
                $error = [
                    "error" => [
                        "message" => "Bad request: formats could not be fetched."
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
        // Get one format with given id
        //     
        public function getOne($request, $response) {
            $format_id= $request->getAttribute("id");

            try {
                $statement = $this->db->prepare(
                    "
                        SELECT * 
                        FROM formats
                        WHERE _id = :_id
                    "
                );
                $statement->execute(array(
                    ":_id" =>  $format_id
                ));
                $format = $statement->fetch();

                if ($format) {
                    $data= array_merge(["formats"=> [$format]], $_SESSION);
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
                        "message" => "Bad request: format could not be fetched due to an invalid parameter."
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
        // Add one format
        //     
        public function add($request, $response) {
            try {
                $statement = $this->db->prepare(
                    "
                        INSERT INTO formats(
                            format_name,
                            format_price,
                            format_iconPath,
                            format_dimensions
                        )
                        VALUES(
                            :format_name,
                            :format_price,
                            format_iconPath,
                            :format_dimensions
                        )
                    "
                );
                 $queryResult = $statement->execute(array(
                    ":format_name" => $request->getParam("format_name"),
                    ":format_iconPath" => $request->getParam("format_iconPath"),
                    ":format_price" => $request->getParam("format_price"),
                    ":format_dimensions" => $request->getParam("format_dimensions")
                ));

                $data= array_merge(["inserted"=> $queryResult], $_SESSION);
                $result = $this->response->withStatus(201)
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data));
            }

            catch(PDOException $exception) {
                $error = [
                    "error" => [
                        "message" => "Bad request: format could not be added due to missing or invalid parameters."
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
        // Update one format with given id
        //     
        public function update($request, $response) {
            $format_id= $request->getAttribute("id");

            try {
                $statement = $this->db->prepare(
                    "
                        SELECT * 
                        FROM formats
                        WHERE _id = :_id
                    "
                );
                $statement->execute(array(
                    ":_id" => $format_id
                ));
                $format = $statement->fetch();

                if ($format) {
                    $query=  $this->db->prepare(
                        "   
                            UPDATE formats 
                            SET 
                                    format_name = :format_name,
                                    format_price = :format_price,
                                    format_iconPath = :format_iconPath,
                                    format_dimensions = :format_dimensions
                            WHERE _id = :_id
                        "
                    );
                    $queryResult= $query->execute(array(
                        ":format_name" => $request->getParam("format_name") ,
                        ":format_price" => $request->getParam("format_price"),
                        ":format_iconPath" => $request->getParam("format_iconPath"),
                        ":format_dimensions" => $request->getParam("format_dimensions"),
                        ":_id" => $format_id
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
                        "message" => "Bad request: format could not be updated due to missing or invalid parameters."
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
        // Delete one format with given id
        //     
        public function delete($request, $response) {
            $format_id= $request->getAttribute("id");
            try {
                $statement = $this->db->prepare(
                    "
                        SELECT * 
                        FROM formats
                        WHERE _id = :_id
                    "
                );
                $statement->execute(array(
                    ":_id" => $format_id
                ));
                $format = $statement->fetch();

                if ($format) {
                    $statement = $this->db->prepare(
                        "
                            DELETE FROM formats
                            WHERE _id = :_id
                        "
                    );
                    $queryResult = $statement->execute(array(
                        ":_id" => $format_id
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
                        "message" => "Bad request: format could not be deleted due to invalid parameter."
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