<?php 
require_once('Connection.php');

class Cart extends Connection {
    private $conn;

    public function __construct() {
        $this->conn = new Connection();
    }

    public function getCartItems($user_id) {
        $stmt = $this->conn->getConnection()->prepare("
            SELECT c.*, p.product_name, p.product_price, p.product_preview
            FROM cart c
            JOIN products p ON c.product_id = p.product_id
            WHERE c.user_id = ?
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function addToCart($user_id, $product_id, $quantity = 1) {
        // Check if item already in cart
        $check = $this->conn->getConnection()->prepare("SELECT cart_id FROM cart WHERE user_id=? AND product_id=?");
        $check->bind_param("ii", $user_id, $product_id);
        $check->execute();
        $checkResult = $check->get_result();

        if ($checkResult->num_rows > 0) {
            $stmt = $this->conn->getConnection()->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id=? AND product_id=?");
            $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        } else {
            $stmt = $this->conn->getConnection()->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        }

        return $stmt->execute();
    }

    public function updateQuantity($cart_id, $quantity) {
        $stmt = $this->conn->getConnection()->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
        $stmt->bind_param("ii", $quantity, $cart_id);
        return $stmt->execute();
    }

    public function deleteItem($cart_id) {
        $stmt = $this->conn->getConnection()->prepare("DELETE FROM cart WHERE cart_id = ?");
        $stmt->bind_param("i", $cart_id);
        return $stmt->execute();
    }

    public function getCartTotal($user_id) {
        $stmt = $this->conn->getConnection()->prepare("
            SELECT SUM(p.product_price * c.quantity) AS total
            FROM cart c
            JOIN products p ON c.product_id = p.product_id
            WHERE c.user_id = ?
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    }

    public function getItem($user_id, $product_id) {
        $query = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
        $stmt = $this->conn->getConnection()->prepare($query);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function addItem($user_id, $product_id, $quantity) {
        $query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $this->conn->getConnection()->prepare($query);
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        return $stmt->execute();
    }

    public function updateItem($user_id, $product_id, $quantity) {
        $query = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $stmt = $this->conn->getConnection()->prepare($query);
        $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        return $stmt->execute();
    }
}

?>
