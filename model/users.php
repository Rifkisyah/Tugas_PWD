<?php
require_once('Connection.php');

class Users extends Connection {
    private $conn;

    public function __construct() {
        parent::__construct();
        $this->conn = $this->getConnection();
    }

    public function insert($email, $username, $password, $role_id, $photo_profile) {
        $checkQuery = "SELECT * FROM users WHERE email = ? OR username = ?";
        $stmt = $this->conn->prepare($checkQuery);
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return false; // Email or username already exists
        }

        $passwordHashed = password_hash($password, PASSWORD_DEFAULT);
        $insertQuery = "INSERT INTO users (email, username, password, role_id, photo_profile) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($insertQuery);
        $stmt->bind_param("ssssi", $email, $username, $passwordHashed, $role_id, $photo_profile);

        return $stmt->execute();
    }

    public function update($user_id, $email, $username, $password, $role_id, $photo_profile) {
        $checkQuery = "SELECT * FROM users WHERE (email = ? OR username = ?) AND user_id != ?";
        $stmt = $this->conn->prepare($checkQuery);
        $stmt->bind_param("ssi", $email, $username, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return false; // Email or username already exists
        }

        $passwordHashed = password_hash($password, PASSWORD_DEFAULT);
        $updateQuery = "UPDATE users SET email = ?, username = ?, password = ?, role_id = ?, photo_profile = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($updateQuery);
        $stmt->bind_param("sssisi", $email, $username, $passwordHashed, $role_id, $photo_profile, $user_id);

        return $stmt->execute();
    }

    public function updateProfile($id, $email, $username, $password, $photo_profile = null) {
        $passwordHashed = password_hash($password, PASSWORD_DEFAULT);

        if ($photo_profile) {
            $query = "UPDATE users SET email = ?, username = ?, password = ?, photo_profile = ? WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssssi", $email, $username, $passwordHashed, $photo_profile, $id);
        } else {
            $query = "UPDATE users SET email = ?, username = ?, password = ? WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sssi", $email, $username, $passwordHashed, $id);
        }

        return $stmt->execute();
    }

    public function delete($user_id) {
        $query = "DELETE FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);

        return $stmt->execute();
    }

    public function getUsers() {
        $query = 'SELECT us.user_id, us.username, us.email, ru.role_name
                  FROM users us
                  LEFT JOIN role_users ru ON us.role_id = ru.role_id
                  ORDER BY us.user_id ASC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }

    public function getPhotoProfile($id) {
        $query = "SELECT photo_profile FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['photo_profile'];
        }

        return null;
    }

    public function getAllRoles() {
        $query = "SELECT * FROM role_users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }

    public function getById($user_id) {
        $query = "SELECT * FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        return $result->fetch_assoc();
    }

    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    public function getUserRoleIdByEmail($email) {
        $sql = "SELECT role_id FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($row = $result->fetch_assoc()) {
            return $row['role_id'];
        }
        return null;
    }
    

}
?>