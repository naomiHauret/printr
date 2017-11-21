<?php
    namespace App\Models;

    require "BaseModel.php";
    use BaseModel\BaseModel;

    class Carts extends BaseModel {

        public function getAll($request, $response) {
            try {
                $statement = $this->db->prepare(
                "
                    SELECT * 
                    FROM carts
                    ORDER BY _id
                "
                );
                $statement->execute();
                $carts = $statement->fetchAll();

                $result= $this->response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($carts));
            }
          
            catch(PDOException $exception) {
                $result= $exception->getMessage();
                $result= $this->response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($carts));
            }
          return $result;
        }

        public function getAllForUser($request, $response) {

        }

        public function getOne() {
            
        }

        public function add() {

        }

        public function update() {
            
        }

        public function delete() {
            
        }
    }