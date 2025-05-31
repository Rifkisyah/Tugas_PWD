<?php
require_once('Connection.php');

// controllers/ProductReview.php
class ProductReview extends Connection {
    private $conn;

    public function __construct() {
        parent::__construct();
        $this->conn = $this->getConnection();
    }

    public function addReview($product_id, $user_id, $rating, $comment) {
        // Cek apakah review sudah ada
        $checkQuery = "SELECT review_id FROM product_reviews WHERE product_id = ? AND user_id = ?";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bind_param("ii", $product_id, $user_id);
        $checkStmt->execute();
        $checkStmt->store_result();
    
        if ($checkStmt->num_rows > 0) {
            // Update review jika sudah ada
            $checkStmt->bind_result($review_id);
            $checkStmt->fetch();
            $checkStmt->close();
    
            $updateQuery = "UPDATE product_reviews SET rating = ?, comment = ?, updated_at = NOW() WHERE review_id = ?";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bind_param("isi", $rating, $comment, $review_id);
            return $updateStmt->execute();
        }
    
        // Insert jika belum ada
        $query = "INSERT INTO product_reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiis", $product_id, $user_id, $rating, $comment);
        return $stmt->execute();
    }    

    public function getReviewsByProductId($product_id) {
        $query = "SELECT pr.*, u.username, u.photo_profile 
                  FROM product_reviews pr 
                  JOIN users u ON pr.user_id = u.user_id 
                  WHERE product_id = ? 
                  ORDER BY pr.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAverageRating($product_id) {
        $query = "SELECT AVG(rating) as average_rating FROM product_reviews WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['average_rating'] ?? 0;
    }

    public function getRatingSummary($product_id) {
        $stmt = $this->conn->prepare("
            SELECT 
                COUNT(*) as total_reviews,
                AVG(rating) as average_rating
            FROM product_reviews
            WHERE product_id = ?
        ");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
}


?>