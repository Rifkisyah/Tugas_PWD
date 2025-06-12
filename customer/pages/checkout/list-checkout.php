<?php
require '../model/cart.php';
require '../model/products.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$userId = $_SESSION['user_id'];
$cart = new Cart();
$productModel = new Product();

$mode = $_GET['mode'] ?? 'cart';
$items = null;
$subtotal = 0;
$itemCount = 0;

// Jika mode buy now
if ($mode === 'buy_now' && isset($_SESSION['buy_now'])) {
  $buyNow = $_SESSION['buy_now'];
  $product = $productModel->getProductById($buyNow['product_id']);

  if ($product) {
    $product['quantity'] = $buyNow['quantity'];
    $items = [$product]; // ubah jadi array biasa
  } else {
    $items = [];
  }
} else {
  // default ambil dari cart
  $items = $cart->getCartItems($userId);
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Checkout</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f6f6f6;
      padding: 0;
      margin: 0;
    }

    .checkout-container {
      max-width: 1000px;
      margin: 40px auto;
      background-color: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    h2 {
      margin-top: 0;
      margin-bottom: 20px;
      color: #333;
    }

    .product-list {
      border-top: 1px solid #ccc;
      margin-top: 20px;
      padding-top: 20px;
    }

    .product-item {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
      border-bottom: 1px solid #eee;
      padding-bottom: 10px;
    }

    .product-item img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      margin-right: 20px;
      border-radius: 6px;
    }

    .product-info {
      flex: 1;
    }

    .product-name {
      font-size: 16px;
      font-weight: bold;
    }

    .store-name {
      font-size: 14px;
      color: #666;
      margin-bottom: 5px;
      background-color:rgb(207, 124, 0);
      color: white;
      width: fit-content;
      padding: 2px;
    }

    .price {
      color: #B12704;
      font-weight: bold;
    }

    .summary {
      margin-top: 30px;
      font-size: 18px;
      font-weight: bold;
      color: #333;
    }

    .form-section {
      margin-top: 30px;
    }

    .form-section input,
    .form-section select {
      width: 100%;
      padding: 10px;
      font-size: 16px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .checkout-button {
      background-color: #FF9900;
      color: white;
      padding: 12px 20px;
      font-size: 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .checkout-button:hover {
      background-color: #e68a00;
    }
  </style>
</head>
<body>

<div class="checkout-container">
  <h2>Checkout</h2>

  <?php if ((is_array($items) && count($items) > 0) || ($items instanceof mysqli_result && $items->num_rows > 0)): ?>
    <div class="product-list">
      <?php if (is_array($items)): ?>
        <?php foreach ($items as $row): 
          $total = $row['product_price'] * $row['quantity'];
          $subtotal += $total;
        ?>
          <div class="product-item">
            <img src="../assets/images/product/<?= htmlspecialchars($row['product_preview']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>">
            <div class="product-info">
              <div class="product-name"><?= htmlspecialchars($row['product_name']) ?></div>
              <div class="store-name"><?= htmlspecialchars($row['store_name'] ?? 'Unknown Store') ?></div>
              <div class="price">Rp. <?= number_format($row['product_price'], 0, ',', '.') ?> x <?= $row['quantity'] ?> = <b>Rp. <?= number_format($total, 0, ',', '.') ?></b></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <?php while ($row = $items->fetch_assoc()): 
          $total = $row['product_price'] * $row['quantity'];
          $subtotal += $total;
          $itemCount++;
        ?>
          <div class="product-item">
            <img src="../assets/images/product/<?= htmlspecialchars($row['product_preview']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>">
            <div class="product-info">
              <div class="product-name"><?= htmlspecialchars($row['product_name']) ?></div>
              <div class="store-name"><?= htmlspecialchars($row['store_name'] ?? 'Unknown Store') ?></div>
              <div class="price">Rp. <?= number_format($row['product_price'], 0, ',', '.') ?> x <?= $row['quantity'] ?> = <b>Rp. <?= number_format($total, 0, ',', '.') ?></b></div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>

    <div class="summary">
      Total Pembayaran: Rp. <?= number_format($subtotal, 0, ',', '.') ?>
    </div>

    <form method="POST" action="dashboard.php?module=checkout&pages=process-checkout">
      <div class="form-section">
        <h3>Informasi Pengiriman</h3>
        <label>Alamat Lengkap:</label>
        <input type="text" name="shipping_address" required>

        <label>Metode Pembayaran:</label>
        <select name="payment_method" required>
          <option value="">-- Pilih --</option>
          <option value="transfer">Transfer Bank</option>
          <option value="cod">Cash on Delivery (COD)</option>
          <option value="ewallet">E-Wallet</option>
        </select>

        <input type="hidden" name="total_payment" value="<?= $subtotal ?>">
        <button type="submit" class="checkout-button">Konfirmasi & Bayar</button>
      </div>
    </form>
  <?php else: ?>
    <p>Keranjang belanja kosong.</p>
  <?php endif; ?>
</div>

</body>
</html>
