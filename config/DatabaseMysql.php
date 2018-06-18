<?php
    require_once __DIR__.'/../vendor/autoload.php';

    class DatabaseMysql {
        private $host = '';
        private $db_name = '';
        private $username = '';
        private $password = '';
        private $mysql_conn;       

        public function connect() {

            $dotEnv = new Dotenv\Dotenv(__DIR__.'/../');
            $dotEnv->load();
            $this->host = getenv('DB_MYSQL_HOST');
            $this->db_name = getenv('DB_MYSQL_NAME');
            $this->username = getenv('DB_MYSQL_USERNAME');
            $this->password = getenv('DB_MYSQL_PASSWORD');

            $this->mysql_conn = null;

            try {
                $this->mysql_conn = new PDO('mysql:host=' .$this->host. ';dbname=' .$this->db_name, $this->username, $this->password);
                $this->mysql_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                echo 'Connection Error: ' .$e->getMessage();
            }

            return $this->mysql_conn;
        }
    }
