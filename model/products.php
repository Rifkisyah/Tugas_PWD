<?php
require_once('Connection.php');

class Product extends Connection {
    private $conn;

    public function __construct() {
        parent::__construct();
        $this->conn = $this->getConnection();
    }

    public function insert($store_id, $product_name, $product_description, $product_price, $product_stock, $product_condition, $product_preview, $category_product_id, $product_images = []) {
        $created_at = date("Y-m-d H:i:s");
    
        // Insert ke tabel products
        $insertQuery = "INSERT INTO products (store_id, product_name, product_description, product_price, product_stock, product_condition, product_preview, created_at, category_product_id) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($insertQuery);
        if (!$stmt) {
            return false;
        }
    
        // Perhatikan: jumlah tipe = jumlah parameter
        $stmt->bind_param("isssdsssi", 
            $store_id, 
            $product_name, 
            $product_description, 
            $product_price, 
            $product_stock, 
            $product_condition, 
            $product_preview, 
            $created_at, 
            $category_product_id
        );
    
        if (!$stmt->execute()) {
            return false;
        }
    
        $product_id = $this->conn->insert_id;
    
        // Insert preview image ke product_images
        if (!empty($product_preview)) {
            $insertImageQuery = "INSERT INTO product_images (product_id, filename, type) VALUES (?, ?, 'preview')";
            $stmtImg = $this->conn->prepare($insertImageQuery);
            if ($stmtImg) {
                $stmtImg->bind_param("is", $product_id, $product_preview);
                $stmtImg->execute();
            }
        }
    
        // Insert content images ke product_images
        if (!empty($product_images)) {
            $insertImageQuery = "INSERT INTO product_images (product_id, filename, type) VALUES (?, ?, 'content')";
            $stmtImg = $this->conn->prepare($insertImageQuery);
            if ($stmtImg) {
                foreach ($product_images as $imgPath) {
                    $stmtImg->bind_param("is", $product_id, $imgPath);
                    $stmtImg->execute();
                }
            }
        }
    
        return true;
    }    
    
    public function update($product_id, $store_id, $product_name, $product_description, $product_price, $product_stock, $product_preview, $category_product_id) {
        $updateQuery = "UPDATE products SET store_id = ?, product_name = ?, product_description = ?, product_price = ?, product_stock = ?, product_preview = ?, category_product_id = ? WHERE product_id = ?";
        $stmt = $this->conn->prepare($updateQuery);
        $stmt->bind_param("issdssii", $store_id, $product_name, $product_description, $product_price, $product_stock, $product_preview, $category_product_id, $product_id);
    
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
        // 1. Ambil semua filename gambar terkait product_id dari product_images
        $queryImages = "SELECT filename, type FROM product_images WHERE product_id = ?";
        $stmt = $this->conn->prepare($queryImages);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $defaultImage = 'no-image-product.jpg';
        $imageFolder = '../assets/images/product/';
    
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
        $stmt->close();
    
        // 2. Hapus record gambar di product_images
        $delImages = "DELETE FROM product_images WHERE product_id = ?";
        $stmtDelImages = $this->conn->prepare($delImages);
        $stmtDelImages->bind_param("i", $product_id);
        $stmtDelImages->execute();
        $stmtDelImages->close();
    
        // 3. Ambil preview image produk (dari tabel products)
        $queryPreview = "SELECT product_preview FROM products WHERE product_id = ?";
        $stmtPrev = $this->conn->prepare($queryPreview);
        $stmtPrev->bind_param("i", $product_id);
        $stmtPrev->execute();
        $resultPrev = $stmtPrev->get_result();
    
        if ($resultPrev->num_rows > 0) {
            $rowPrev = $resultPrev->fetch_assoc();
            $preview_image = $rowPrev['product_preview'];
            if ($preview_image && $preview_image !== $defaultImage) {
                $previewPath = $imageFolder . $preview_image;
                if (file_exists($previewPath)) {
                    unlink($previewPath);
                }
            }
        }
        $stmtPrev->close();
    
        // 4. Hapus produk dari tabel products
        $delProduct = "DELETE FROM products WHERE product_id = ?";
        $stmtDelProd = $this->conn->prepare($delProduct);
        $stmtDelProd->bind_param("i", $product_id);
        $success = $stmtDelProd->execute();
        $stmtDelProd->close();
    
        return $success;
    }    

    public function getAllProduct() {
        $query = 'SELECT p.*,
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

    public function getProductById($product_id) {
        $query = 'SELECT p.*,
                  s.store_name, cp.category_product_name
                  FROM products p
                  LEFT JOIN stores s ON p.store_id = s.store_id
                  JOIN category_product cp ON p.category_product_id = cp.category_product_id
                  WHERE p.product_id = ?
                  ORDER BY p.product_id ASC';
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    public function getProductByStoreId($store_id) {
        $query = 'SELECT p.*,
                  s.store_name, cp.category_product_name, ip.filename AS product_image
                  FROM products p
                  LEFT JOIN stores s ON p.store_id = s.store_id
                  JOIN category_product cp ON p.category_product_id = cp.category_product_id
                  JOIN product_images ip ON p.product_id = ip.product_id AND ip.type = "preview"
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

    public function getProductImages($product_id) {
        $stmt = $this->conn->prepare("SELECT filename FROM product_images WHERE product_id = ?");
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $images = [];
        while ($row = $result->fetch_assoc()) {
            $images[] = $row['filename'];
        }
        return $images;
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
    

    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM products";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getFilteredProducts($category_id = 'All', $search = '') {
        $sql = "SELECT * FROM products WHERE 1";

        $params = [];
        $types = '';

        if ($category_id !== 'All') {
            $sql .= " AND category_id = ?";
            $params[] = $category_id;
            $types .= 'i'; // assuming category_id is integer
        }

        if (!empty($search)) {
            $sql .= " AND product_name LIKE ?";
            $params[] = '%' . $search . '%';
            $types .= 's';
        }

        $stmt = $this->conn->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        $stmt->close();
        $this->conn->close();

        return $products;
    }
}
?>
