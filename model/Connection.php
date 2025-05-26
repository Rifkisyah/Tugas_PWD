<?php
    class Connection{
        private $conn;
        private $server = 'localhost';
        private $user = 'root';
        private $password = 'root';
        private $database = 'db_tubes_pwd';

        public function __construct(){
            $this->conn = new mysqli($this->server, $this->user, $this->password, $this->database);
            if($this->conn->connect_error){
                die("Connection failed: " . $this->conn->connect_error);
            }
        }

        public function getConnection(){
            return $this->conn;
        }
    }

?>