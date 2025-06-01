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

    // store followers

    public function getStoreDetail($storeId)
    {
        $query = "
            SELECT s.*, 
                (
                    SELECT ROUND(AVG(r.rating), 1)
                    FROM products p
                    LEFT JOIN product_reviews r ON p.product_id = r.product_id
                    WHERE p.store_id = s.store_id
                ) AS avg_rating,
                (
                    SELECT COUNT(*)
                    FROM store_followers
                    WHERE store_id = s.store_id
                ) AS total_followers
            FROM stores s
            WHERE s.store_id = ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $storeId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getStoreProducts($storeId)
    {
        $query = "
            SELECT p.*, 
                (
                    SELECT ROUND(AVG(r.rating), 1)
                    FROM product_reviews r 
                    WHERE r.product_id = p.product_id
                ) AS rating
            FROM products p
            WHERE p.store_id = ?
            ORDER BY p.created_at DESC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $storeId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getStoreCategories($storeId)
    {
        $query = "
            SELECT DISTINCT c.category_product_name
            FROM products p
            JOIN category_product c ON p.category_product_id = c.category_product_id
            WHERE p.store_id = ?
        ";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $storeId);
        $stmt->execute();
    
        $result = $stmt->get_result();
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row['category_product_name'];
        }
    
        return $categories;
    }
    
    public function isUserFollowing($userId, $storeId) {
        $stmt = $this->conn->prepare("SELECT 1 FROM store_followers WHERE user_id = ? AND store_id = ?");
        $stmt->bind_param("ii", $userId, $storeId);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function followStore($userId, $storeId) {
        $stmt = $this->conn->prepare("INSERT IGNORE INTO store_followers (user_id, store_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $storeId);
        return $stmt->execute();
    }

    public function unfollowStore($userId, $storeId) {
        $stmt = $this->conn->prepare("DELETE FROM store_followers WHERE user_id = ? AND store_id = ?");
        $stmt->bind_param("ii", $userId, $storeId);
        return $stmt->execute();
    }

    public function getFollowerCount($storeId) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM store_followers WHERE store_id = ?");
        $stmt->bind_param("i", $storeId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        return $count;
    }

    public function getStoreAverageRating($storeId){
        $query = "
            SELECT ROUND(AVG(r.rating), 1) AS avg_rating
            FROM products p
            LEFT JOIN product_reviews r ON p.product_id = r.product_id
            WHERE p.store_id = ?
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $storeId);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        return $result['avg_rating'] ?? null;
    }

    public function getStoreTotalRatings($storeId) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS total_ratings
            FROM product_reviews
            WHERE product_id IN (
                SELECT product_id FROM products WHERE store_id = ?
            )
        ");
        $stmt->execute([$storeId]);
        return $stmt->fetch()['total_ratings'] ?? 0;
    }
    
    
}
?>
