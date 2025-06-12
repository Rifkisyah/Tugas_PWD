<?php
require '../model/order.php';
require '../model/cart.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$userId = $_SESSION['user_id'];
$shippingAddress = $_POST['shipping_address'];
$paymentMethod = $_POST['payment_method'];
$total = $_POST['total_payment'];

$cart = new Cart();
$items = $cart->getCartItems($userId);

$orderItems = [];
while ($row = $items->fetch_assoc()) {
  $orderItems[] = [
    'product_id' => $row['product_id'],
    'quantity' => $row['quantity'],
    'price' => $row['product_price']
  ];
}

$order = new Order();
$orderId = $order->createOrder($userId, $shippingAddress, $paymentMethod, $total, $orderItems);

if ($orderId) {
    $cart->clearCart($userId);
    echo "<script>window.location.href = 'dashboard.php?module=checkout&pages=history';</script>";
    exit;
  } else {
    echo "<script>alert('Gagal memproses pesanan.'); window.history.back();</script>";
  }
  
