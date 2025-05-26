<?php
require_once('Connection.php');

class Auth {
    private $conn;

    public function __construct() {
        $db = new Connection();
        $this->conn = $db->getConnection();
    }

    public function signup($username, $email, $password, $confirm_password, $role) {
        if ($password !== $confirm_password) {
            return false;
        }

        // Cek apakah email/username sudah terdaftar
        $checkQuery = "SELECT * FROM users WHERE email = ? OR username = ?";
        $stmt = $this->conn->prepare($checkQuery);
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return false;
        }

        // Masukkan data baru
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insertQuery = "INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($insertQuery);
        $stmt->bind_param("sssi", $username, $email, $hashed_password, $role);

        return $stmt->execute();
    }

    public function signin($email, $password) {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                return true;
            }
        }

        return false;
    }

    public function getUserId($email) {
        $query = "SELECT user_id FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['user_id'];
        }

        return null;
    }

    public function getUsername($email) {
        $query = "SELECT username FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['username'];
        }

        return null;
    }

    public function signout() {
        session_destroy();
        header("Location: ../index.php");
        exit;
    }
}
?>
