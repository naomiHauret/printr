<?php

    namespace App\Models;
    require "BaseModel.php";
    use BaseModel\BaseModel;

    class Options extends BaseModel {

        //
        // Get all options
        //     
        public function getAll($request, $response) {
            try {
                $statement = $this->db->prepare(
                "
                        SELECT * 
                        FROM options
                        ORDER BY option_category
                "
                );
                $statement->execute();
                $options = $statement->fetchAll();

                $result= $this->response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($options));
            }
          
            catch(PDOException $exception) {
                $result= $exception->getMessage();
            }
          return $result;
        }

        //
        // Get one option with given id
        //     
        public function getOne($request, $response) {
            try {
                $statement = $this->db->prepare(
                "
                        SELECT * 
                        FROM options
                        WHERE _id = :_id
                "
                );
                $statement->execute(array(
                    ':_id' => $request->getAttribute('id')
                ));
                $option = $statement->fetch();
                $result = $this->response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($option));
            }

            catch(PDOException $exception) {
                $result = $exception->getMessage();
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
                        option_category,
                        option_isAvailable
                    )
                    VALUES(
                        :option_name,
                        :option_price,
                        :option_category,
                        :option_isAvailable
                    )
                "
                );
                 $queryResult = $statement->execute(array(
                    ":option_name" => $request->getParam('option_name'),
                    ":option_price" => $request->getParam('option_price'),
                    ":option_category" => $request->getParam('option_category'),
                    ":option_isAvailable" => $request->getParam('option_isAvailable')
                ));

                $result = $this->response->withStatus(201)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($queryResult));
            }

            catch(PDOException $exception) {
                $result = $exception->getMessage();
            }

            return $result;
        }

        //
        // Update one option with given id
        //     
        public function update($request, $response) {
            try { 
                $query=  $this->db->prepare(
                "   
                    UPDATE options 
                    SET 
                            option_name = :option_name,
                            option_price = :option_price,
                            option_category = :option_category,
                            option_isAvailable = :option_isAvailable
                    WHERE _id = :_id
                ");
                $queryResult= $query->execute(array(
                    'option_name' => $request->getParam('option_name') ,
                    'option_price' => $request->getParam('option_price'),
                    'option_category' => $request->getParam('option_category'),
                    'option_isAvailable' => $request->getParam('option_isAvailable'),
                    '_id' => $request->getAttribute('id')
                ));

                $result = $this->response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($queryResult));
            }

            
            catch(PDOException $exception) {
                $result = $exception->getMessage();
            }

            return $result;
        }

        //
        // Delete one option with given id
        //     
        public function delete($request, $response) {
           try {
                $option_id =  $request->getAttribute('id');

                $statement = $this->db->prepare(
                "
                    DELETE FROM options WHERE _id = :_id
                "
                );
                 $queryResult = $statement->execute(array(
                    ":_id" => $option_id
                ));

                $result = $this->response->withStatus(204)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($queryResult));
            }

            catch(PDOException $exception) {
                $result = $exception->getMessage();
            }

            return $result;
        }
    }