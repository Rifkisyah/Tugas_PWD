<?php
require_once('Connection.php');

class Product extends Connection {
    private $conn;

    public function __construct() {
        parent::__construct();
        $this->conn = $this->getConnection();
    }

    public function insert($store_id, $product_name, $product_description, $product_price, $product_stock, $product_image, $category_product_id) {
        $created_at = date("Y-m-d H:i:s");
        $insertQuery = "INSERT INTO products (store_id, product_name, product_description, product_price, product_stock, product_image, created_at, category_product_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($insertQuery);
        $stmt->bind_param("isssdssi", $store_id, $product_name, $product_description, $product_price, $product_stock, $product_image, $created_at, $category_product_id);
    
        return $stmt->execute();
    }
    
    public function update($product_id, $store_id, $product_name, $product_description, $product_price, $product_stock, $product_image, $category_product_id) {
        $updateQuery = "UPDATE products SET store_id = ?, product_name = ?, product_description = ?, product_price = ?, product_stock = ?, product_image = ?, category_product_id = ? WHERE product_id = ?";
        $stmt = $this->conn->prepare($updateQuery);
        $stmt->bind_param("issdssii", $store_id, $product_name, $product_description, $product_price, $product_stock, $product_image, $category_product_id, $product_id);
    
        return $stmt->execute();
    }

    public function isProductExist($store_id, $product_name) {
        $query = "SELECT * FROM products WHERE store_id = ? AND product_name = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $store_id, $product_name);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    
    
    public function delete($product_id) {
        $query = "DELETE FROM products WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);

        return $stmt->execute();
    }

    public function getAllProduct() {
        $query = 'SELECT p.product_id, p.product_name, p.product_price, p.product_stock, p.product_description, p.product_image, p.created_at,
                  s.store_name, cp.category_product_name
                  FROM products p
                  LEFT JOIN stores s ON p.store_id = s.store_id
                  JOIN category_product cp ON p.category_product_id = cp.category_product_id
                  ORDER BY p.product_id ASC';
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

    public function getAllProductById($store_id) {
        $query = 'SELECT p.product_id, p.product_name, p.product_price, p.product_stock, p.product_description, p.product_image, p.created_at,
                  s.store_name, cp.category_product_name
                  FROM products p
                  LEFT JOIN stores s ON p.store_id = s.store_id
                  JOIN category_product cp ON p.category_product_id = cp.category_product_id
                  WHERE p.store_id = ?
                  ORDER BY p.product_id ASC';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $store_id);
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

    public function getProductImage($id) {
        $query = "SELECT product_image FROM products WHERE product_id = ?";
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

    public function getAllCategoryProduct() {
        $query = "SELECT * FROM category_product";
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

    public function getById($product_id) {
        $query = "SELECT * FROM products WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        return $result->fetch_assoc();
    }
    

    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM products";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
?>
