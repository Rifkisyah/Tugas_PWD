<?php
require '../model/order.php';

$order = new Order();
$userId = $_SESSION['user_id'];

$statuses = ['diproses', 'dikirim', 'diterima'];
?>

<style>
    #orderContent {
    margin: 20px;              /* 1. Margin luar */
    height: 500px;             /* 2. Static height */
    overflow-y: auto;          /* 3. Scroll jika konten lebih panjang */
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 15px;
    background-color: #fff;
    }

    .order-group {
    height: 100%;
    }

    .order-group .no-orders {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999; /* 4. Warna pudar */
    font-style: italic;
    text-align: center;
    }

  .nav-tabs {
    list-style: none;
    padding-left: 0;
    margin-bottom: -1px;
    display: flex;
    border-bottom: none;
  }

  .nav-tabs .nav-item {
    margin-right: 5px;
  }

  .nav-tabs .nav-link {
    border: none;
    background-color: #f1f1f1;
    color: #555;
    font-weight: 500;
    border-radius: 8px 8px 0 0;
    padding: 10px 16px;
    transition: all 0.3s ease;
  }

  .nav-tabs .nav-link.active {
    background-color: #fff;
    border-bottom: 2px solid #ff9900;
    color: #ff9900;
  }

  .tab-content {
    background-color: #fff;
    padding: 24px;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  }

  .card.order-card {
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    background-color: #fafafa;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    padding: 16px;
  }

  .card.order-card h5 {
    font-weight: 600;
    color: #333;
    margin-bottom: 12px;
  }

  .card.order-card p {
    margin-bottom: 6px;
    color: #444;
    font-size: 14px;
  }

  .product-list {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin-top: 12px;
  }

  .product-item {
    width: 110px;
    text-align: center;
    border: 1px solid #eee;
    border-radius: 8px;
    padding: 8px;
    background-color: #fff;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
  }

  .product-item img {
    width: 100%;
    height: 100px;
    object-fit: contain;
    margin-bottom: 6px;
    border-radius: 4px;
  }

  .product-item small {
    font-size: 12px;
    color: #333;
  }

  .product-item {
    width: 130px; /* Perbesar lebar */
    margin: 0 10px;
    text-align: center;
    flex-shrink: 0;
}

.product-item img {
    width: 100%;
    height: auto;
    border-radius: 6px;
    margin-bottom: 5px;
}

.product-item small {
    display: -webkit-box;
    -webkit-line-clamp: 3;        /* Maksimal 3 baris */
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 0.85rem;
    color: #333;
    line-height: 1.2rem;
}


  @media (max-width: 768px) {
    .product-list {
      justify-content: center;
    }

    .product-item {
      width: 100px;
    }
  }
</style>

<!-- Filter Tab Buttons -->
<ul class="nav nav-tabs mb-3" id="orderTab" role="tablist" style="list-style: none; padding-left: 0;">
  <?php foreach ($statuses as $index => $status): ?>
    <li class="nav-item" role="presentation">
      <button class="nav-link <?= $index === 0 ? 'active' : '' ?>" data-status="<?= $status ?>" type="button">
        <?= ucfirst($status) ?>
      </button>
    </li>
  <?php endforeach; ?>
</ul>

<!-- All Orders Rendered Here -->
<div id="orderContent">
  <?php foreach ($statuses as $status): 
    $orders = $order->getOrdersByUser($userId, $status); ?>
    <div class="order-group" data-status="<?= $status ?>" <?= $status !== $statuses[0] ? 'style="display:none;"' : '' ?>>
    <?php if ($orders->num_rows === 0): ?>
        <div class="no-orders">No order history.</div>
    <?php endif; ?>
      <?php while ($row = $orders->fetch_assoc()): ?>
        <div class="card order-card mb-4">
          <h5>Order #<?= $row['order_id'] ?> - <?= ucfirst($row['status']) ?></h5>
          <p><strong>Address:</strong> <?= $row['shipping_address'] ?></p>
          <p><strong>Total:</strong> Rp <?= number_format($row['total_amount'], 0, ',', '.') ?></p>
          <div class="product-list">
            <?php 
            $items = $order->getOrderItems($row['order_id']);
            while ($item = $items->fetch_assoc()): ?>
              <div class="product-item">
                <img src="../assets/images/product/<?= $item['product_preview'] ?>" alt="<?= $item['product_name'] ?>" />
                <small><?= $item['product_name'] ?> x <?= $item['quantity'] ?></small>
              </div>
            <?php endwhile; ?>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endforeach; ?>
</div>

<script>
  document.querySelectorAll('#orderTab .nav-link').forEach(tab => {
    tab.addEventListener('click', () => {
      const status = tab.getAttribute('data-status');

      // Aktifkan tab yang dipilih
      document.querySelectorAll('#orderTab .nav-link').forEach(btn => btn.classList.remove('active'));
      tab.classList.add('active');

      // Tampilkan hanya order sesuai status
      document.querySelectorAll('#orderContent .order-group').forEach(group => {
        group.style.display = (group.getAttribute('data-status') === status) ? 'block' : 'none';
      });
    });
  });
</script>



