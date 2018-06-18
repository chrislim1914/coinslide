<?php

    require_once __DIR__.'/../vendor/autoload.php';

    class DatabaseMongodb {
        
        private $host;
        private $db_name;
        private $username;
        private $password;
        private $dport;
        private $conn;
                
        public function mongoConnect() {

            $dotEnv = new Dotenv\Dotenv(__DIR__.'/../');
            $dotEnv->load();
            $this->host = getenv('DB_HOST');
            $this->db_name = getenv('DB_DATABASE');
            $this->username = getenv('DB_USERNAME');
            $this->password = getenv('DB_PASSWORD');
            $this->dport = getenv('DB_PORT');

            $conn_string = 'mongodb://'.$this->username.':'.$this->password.'@'.$this->host.':'.$this->dport.'/'.$this->db_name;
          
            try {
                $this->conn = new MongoDB\Client($conn_string);
                $db = $this->conn->listDatabases();
            } catch(Exception $e) {
                echo "failed: " .$e;
            }

            return $this->conn;

        }

        
    }
