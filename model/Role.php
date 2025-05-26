<?php
require_once('Connection.php');

class Role {
    private $conn;
    private $table_name = "role_users";

    public $id;
    public $name;

    public function __construct(){
        $this->conn = new Connection();
    }

    public function getRoleId($role_name){
        $query = "SELECT role_id FROM " . $this->table_name . " WHERE role_name = ?";
        $stmt = $this->conn->getConnection()->prepare($query);
        $stmt->bind_param("s", $role_name);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if($row){
            return $row['role_id'];
        } else {
            return null;
        }
    }
    
    
}
?>