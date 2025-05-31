<?php
  require '../model/Cart.php';
  $userId = $_SESSION['user_id']; // Pastikan user sudah login
  $cart = new Cart();
  $items = $cart->getCartItems($userId);
  $subtotal = 0;
  $itemCount = 0; 
?>

<!DOCTYPE html>
<html>
<head>
  <title>Shopping Cart</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #eaeded;
      margin: 0;
      padding: 0;
    }

    .main {
      display: flex;
      width: 1200px;
      min-height: 700px; /* ukuran static minimal */
      height: auto;       /* memungkinkan konten memanjang */
      margin: 30px auto;
      gap: 20px;
    }

    .cart-section {
      flex: 3;
      background-color: #fff;
      padding: 20px;
      min-height: 700px;
      height: auto;
    }

    .summary-section {
      flex: 1;
      background-color: #fff;
      padding: 20px;
      height: fit-content;
      min-height: 300px;
      position: sticky;
      top: 30px;
    }

    .cart-item {
      display: flex;
      border-bottom: 1px solid #ddd;
      padding: 20px 0;
      gap: 20px;
    }

    .cart-item img {
      width: 180px;
      object-fit: cover;
    }

    .item-details {
      flex: 1;
    }

    .item-title {
      font-size: 18px;
      font-weight: bold;
    }

    .badge {
      background-color: #c45500;
      color: white;
      padding: 2px 6px;
      font-size: 12px;
      font-weight: bold;
      display: inline-block;
      margin-top: 5px;
      margin-bottom: 5px;
    }

    .item-price {
      color: #B12704;
      font-size: 16px;
      margin: 10px 0;
    }

    .in-stock {
      color: #007600;
      font-size: 14px;
    }

    .gift-option {
      margin-top: 8px;
    }

    .quantity-box {
      display: flex;
      align-items: center;
      margin-top: 10px;
      gap: 10px;
      background-color: #f0f2f2;
      padding: 4px 10px;
      border-radius: 25px;
      width: fit-content;
    }

    .quantity-box button {
      background: none;
      border: none;
      font-size: 16px;
      cursor: pointer;
    }

    .actions {
      margin-top: 10px;
      font-size: 14px;
      color: #007185;
    }

    .actions a {
      margin-right: 15px;
      text-decoration: none;
      color: #007185;
    }

    .actions form {
      display: inline;
    }

    .subtotal {
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 20px;
    }

    .checkout-btn {
      background-color: #FFD814;
      border: 1px solid #FCD200;
      padding: 10px 20px;
      font-size: 16px;
      font-weight: bold;
      width: 100%;
      cursor: pointer;
      border-radius: 10px;
    }

    .checkout-btn:hover {
      background-color: #F7CA00;
    }

    .actions form button {
    color: white;
    background-color: #B12704; /* merah gelap */
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
  }
  .actions form button:hover {
    background-color: #7a1a02;
  }

  /* Tombol quantity */
  .quantity-box button {
    width: 30px;
    height: 30px;
    font-weight: bold;
    font-size: 18px;
    border: 1px solid #ccc;
    background-color: white;
    cursor: pointer;
    user-select: none;
    border-radius: 4px;
    transition: background-color 0.2s ease;
  }
  .quantity-box button:hover {
    background-color: #eee;
  }

  /* Angka quantity */
  .quantity-box {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-weight: bold;
    font-size: 16px;
  }
  </style>
</head>
<body>
  <div class="main">
    <!-- Left: Cart Items -->
    <div class="cart-section">
      <h2>Shopping Cart</h2>

      <?php if ($items->num_rows > 0): ?>
        <?php 
          $subtotal = 0;
          $itemCount = 0;
          while ($row = $items->fetch_assoc()) :
            $total = $row['product_price'] * $row['quantity'];
            $subtotal += $total;
            $itemCount++;
        ?>
          <div class="cart-item" data-price="<?= $row['product_price'] ?>" data-cart-id="<?= $row['cart_id'] ?>">
            <img 
              src="../assets/images/product/<?= htmlspecialchars($row['product_preview']) ?>" 
              alt="<?= htmlspecialchars($row['product_name']) ?>"
              onclick="window.location.href='dashboard.php?module=product&pages=product-detail&id=<?= $row['product_id'] ?>'"
              style="cursor: pointer;"  
            >
            <div class="item-details">
              <div class="item-title" onclick="window.location.href='dashboard.php?module=product&pages=product-detail&id=<?= $row['product_id'] ?>'" style="cursor: pointer;"><?= htmlspecialchars($row['product_name']) ?></div>
              <div class="item-price">Rp. <?= number_format($row['product_price'], 0, ',', '.') ?></div>
              <div class="quantity-box">
                <button class="btn-minus">-</button>
                <span class="quantity-number"><?= $row['quantity'] ?></span>
                <button class="btn-plus">+</button>
              </div>
              <div class="actions">
                <form method="POST" action=" dashboard.php?module=cart&pages=cart-delete">
                  <input type="hidden" name="cart_id" value="<?= $row['cart_id'] ?>">
                  <button type="submit">Delete</button>
                </form>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p style="font-size: 18px; color: #555; padding: 38%;">Your shopping cart is empty.</p>
      <?php endif; ?>
    </div>

    <!-- Right: Summary -->
    <?php if ($itemCount > 0): ?>
      <div class="summary-section">
        <div class="subtotal">Subtotal (<?= $itemCount ?> item<?= $itemCount > 1 ? 's' : '' ?>):<br> <span id="subtotal-price" style="color:#B12704;">Rp. <?= number_format($subtotal, 0, ',', '.') ?></span></div>
        <button class="checkout-btn">Proceed to checkout</button>
      </div>
    <?php endif; ?>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const btnPlus = document.querySelectorAll('.btn-plus');
      const btnMinus = document.querySelectorAll('.btn-minus');
      const subtotalElem = document.getElementById('subtotal-price');

      function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
      }

      function updateSubtotal() {
        let newSubtotal = 0;
        document.querySelectorAll('.cart-item').forEach(item => {
          const price = parseInt(item.getAttribute('data-price'));
          const qty = parseInt(item.querySelector('.quantity-number').textContent);
          newSubtotal += price * qty;
        });
        subtotalElem.textContent = formatRupiah(newSubtotal);
      }

      btnPlus.forEach(button => {
        button.addEventListener('click', function() {
          const qtyElem = this.previousElementSibling;
          let currentQty = parseInt(qtyElem.textContent);
          qtyElem.textContent = currentQty + 1;
          updateSubtotal();
        });
      });

      btnMinus.forEach(button => {
        button.addEventListener('click', function() {
          const qtyElem = this.nextElementSibling;
          let currentQty = parseInt(qtyElem.textContent);
          if(currentQty > 1) {
            qtyElem.textContent = currentQty - 1;
            updateSubtotal();
          }
        });
      });
    });
  </script>
</body>
</html>
