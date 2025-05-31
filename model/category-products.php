<?php
require_once('Connection.php');

class CategoryProducts extends Connection {
    private $conn;

    public function __construct() {
        $db = new Connection();
        $this->conn = $db->getConnection();
    }

    public function getAllCategories() {
        $sql = "SELECT category_product_id, category_product_name FROM category_product ORDER BY category_product_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        $categories = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
        }

        return $categories;
    }
}