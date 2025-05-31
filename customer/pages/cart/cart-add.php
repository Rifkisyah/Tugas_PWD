<?php
require_once('../model/cart.php');

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 1);

// Validasi sederhana
if ($product_id <= 0 || $quantity <= 0) {
    die("Invalid request");
}

// Simpan ke cart
$cart = new Cart();
$existing = $cart->getItem($user_id, $product_id);

if ($existing) {
    // Tambahkan quantity jika sudah ada
    $newQty = $existing['quantity'] + $quantity;
    $cart->updateItem($user_id, $product_id, $newQty);
} else {
    // Tambahkan item baru
    $cart->addItem($user_id, $product_id, $quantity);
}

echo '<script>window.location.href = "dashboard.php?module=cart&pages=shopping-cart";</script>';
exit;
