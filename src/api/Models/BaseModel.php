<?php
    namespace BaseModel;
    use Exception;
    
    class BaseModel {

        public function __construct($container) {
            $this->container = $container;
        }

        public function __get($name) {
            return $this->container->get($name);
        }
    }

    class ResourcesNotFoundException extends Exception {}