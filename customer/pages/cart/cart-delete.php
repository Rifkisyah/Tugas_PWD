<?php
// Include file koneksi / model yang berisi class dengan method deleteItem()
require_once '../model/cart.php';

// Cek user login (optional tapi disarankan)
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $cart_id = intval($_POST['cart_id']);
    
    $cartModel = new Cart();  // sesuaikan dengan classmu
    $deleted = $cartModel->deleteItem($cart_id);

    if ($deleted) {
        $_SESSION['message'] = "Item berhasil dihapus dari keranjang.";
    } else {
        $_SESSION['error'] = "Gagal menghapus item.";
    }

    // Redirect kembali ke halaman cart
    echo '<script>window.location.href = "dashboard.php?module=cart&pages=shopping-cart";</script>';
    exit;
}

// Jika akses tanpa POST atau tanpa cart_id, redirect juga
echo '<script>window.location.href = "dashboard.php?module=cart&pages=shopping-cart";</script>';
exit;
