<?php
require_once('Connection.php');

class Store extends Connection {
    private $conn;

    public function __construct() {
        parent::__construct();
        $this->conn = $this->getConnection();
    }

    public function getAllStore() {
        $query = "SELECT * FROM stores";
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

    public function insert($store_name, $owner_name, $address, $email, $contact, $store_image) {
        // Cek apakah email sudah digunakan
        $checkQuery = "SELECT * FROM stores WHERE store_email = ?";
        $stmt = $this->conn->prepare($checkQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            return false; // Email sudah terdaftar
        }
    
        // Query insert ke tabel stores
        $insertQuery = "INSERT INTO stores (store_name, store_owner, store_address, store_email, store_contact, store_image) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($insertQuery);
        $stmt->bind_param("ssssss", $store_name, $owner_name, $address, $email, $contact, $store_image);
    
        return $stmt->execute();
    }
    

    public function update($store_id, $store_name, $owner_name, $address, $email, $contact, $store_image) {
        // Cek apakah email sudah digunakan oleh store lain
        $checkQuery = "SELECT * FROM stores WHERE store_email = ? AND store_id != ?";
        $stmt = $this->conn->prepare($checkQuery);
        $stmt->bind_param("si", $email, $store_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            return false; // Email sudah digunakan oleh store lain
        }
    
        // Update data store
        $updateQuery = "UPDATE stores 
                        SET store_name = ?, store_owner = ?, store_address = ?, store_email = ?, store_contact = ?, store_image = ? 
                        WHERE store_id = ?";
        $stmt = $this->conn->prepare($updateQuery);
        $stmt->bind_param("ssssssi", $store_name, $owner_name, $address, $email, $contact, $store_image, $store_id);
    
        return $stmt->execute();
    }
    
    public function getStoreById($store_id) {
        $query = "SELECT * FROM stores WHERE store_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $store_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        return $result->fetch_assoc();
    }

    public function delete($store_id) {
        $query = "DELETE FROM stores WHERE store_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $store_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $defaultImage = 'default-store-photo-profile.jpg';
        $imageFolder = '../assets/images/store/';
    
        // Hapus file gambar (kecuali default)
        while ($row = $result->fetch_assoc()) {
            $filename = $row['filename'];
            if ($filename !== $defaultImage) {
                $filePath = $imageFolder . $filename;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        return $stmt->affected_rows > 0;
    }

    public function getAllStores() {
        $query = 'SELECT * FROM stores ORDER BY store_id ASC';
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

    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM stores";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?>
