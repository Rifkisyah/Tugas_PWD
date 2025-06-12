<?php
require_once('Connection.php');

class Order {
  private $conn;

  public function __construct() {
    $database = new Connection();
    $this->conn = $database->getConnection();
  }

  public function createOrder($userId, $shippingAddress, $paymentMethod, $totalAmount, $items) {
    $this->conn->begin_transaction();

    try {
      // Insert orders
      $stmt = $this->conn->prepare("INSERT INTO orders (user_id, shipping_address, payment_method, total_amount) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("issd", $userId, $shippingAddress, $paymentMethod, $totalAmount);
      $stmt->execute();
      $orderId = $stmt->insert_id;

      // Insert order_items
      $itemStmt = $this->conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
      foreach ($items as $item) {
        $itemStmt->bind_param("iiid", $orderId, $item['product_id'], $item['quantity'], $item['price']);
        $itemStmt->execute();
      }

      $this->conn->commit();
      return $orderId;

    } catch (Exception $e) {
      $this->conn->rollback();
      return false;
    }
  }

  public function getOrdersByUser($userId, $status = null) {
    $query = "SELECT * FROM orders WHERE user_id = ?";
    if ($status !== null) {
      $query .= " AND status = ?";
    }

    $stmt = $this->conn->prepare($query);
    if ($status !== null) {
      $stmt->bind_param("is", $userId, $status);
    } else {
      $stmt->bind_param("i", $userId);
    }
    $stmt->execute();
    return $stmt->get_result();
  }

  public function getOrderItems($orderId) {
    $stmt = $this->conn->prepare("
      SELECT oi.*, p.product_name, p.product_preview 
      FROM order_items oi 
      JOIN products p ON oi.product_id = p.product_id 
      WHERE oi.order_id = ?
    ");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    return $stmt->get_result();
  }
}
