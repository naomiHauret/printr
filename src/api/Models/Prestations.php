<?php
    namespace App\Models;

    require "BaseModel.php";
    use BaseModel\BaseModel;

    class Prestations extends BaseModel {
        public function getAll($request, $response) {
            $statement = $this->db->prepare(
                "
                        SELECT * 
                        FROM prestations
                "
            );
            $statement->execute();
            $prestations = $statement->fetchAll();
            return $this->response->withJson($prestations);
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

    