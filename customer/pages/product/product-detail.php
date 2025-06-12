<?php
  // Tangani POST lebih dulu SEBELUM ada output apapun
  $review_submitted = false;
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      require_once('../model/product-reviews.php');
      $reviewObj = new ProductReview();

      $product_id = intval($_GET['id'] ?? 0);
      $user_id = $_SESSION['user_id'];
      $rating = intval($_POST['rating']);
      $comment = trim($_POST['comment']);
      $reviewObj->addReview($product_id, $user_id, $rating, $comment);

    // Set flag sukses di session
    $_SESSION['review_submitted'] = true;

    // Redirect supaya halaman jadi GET, hindari resubmit form
    echo '<script>window.location.href = "' . $_SERVER['REQUEST_URI'] . '";</script>';
    exit;
  }

  // Selanjutnya baru lanjutkan logic lainnya
  require_once('../model/products.php');
  require_once('../model/stores.php');
  require_once('../model/product-reviews.php');

  $productObj = new Product();
  $product_id = intval($_GET['id'] ?? 0);
  $product = $productObj->getProductById($product_id);

  if (!$product) {
      die("Produk tidak ditemukan.");
  }

  $productGallery = $productObj->getProductImages($product_id);
  if (!in_array($product['product_preview'], $productGallery)) {
      array_unshift($productGallery, $product['product_preview']);
  }

  $storeObj = new Store();
  $store = $storeObj->getStoreById($product['store_id']);

  // Gambar produk
  $productImagePath = $_SERVER['DOCUMENT_ROOT'] . "/assets/images/product/" . $product['product_preview'];
  $productImage = (!empty($product['product_preview']) && file_exists($productImagePath))
      ? $product['product_preview']
      : 'no-image-product.jpg';  

  // Gambar toko
  $storeImagePath = $_SERVER['DOCUMENT_ROOT'] . "/assets/images/store/" . $store['store_image'];
  $storeImage = (!empty($store['store_image']) && file_exists($storeImagePath))
      ? $store['store_image']
      : 'default-store-photo-profile.jpg';
  

  $reviewObj = new ProductReview();
  $reviews = $reviewObj->getReviewsByProductId($product_id);
  $avg_rating = round($reviewObj->getAverageRating($product_id), 1);

  $rating_summary = $reviewObj->getRatingSummary($product_id);
  $total_ratings = $rating_summary['total_reviews'];
  $average_rating = round($rating_summary['average_rating'] ?? 0, 1);

  // Generate ★ visual
  $full_stars = floor($average_rating);
  $half_star = ($average_rating - $full_stars) >= 0.5 ? 1 : 0;
  $empty_stars = 5 - $full_stars - $half_star;

  $stars_visual = str_repeat("★", $full_stars)
    . ($half_star ? "½" : "")
    . str_repeat("☆", $empty_stars);

    // Setelah redirect, cek flag session
  if (!empty($_SESSION['review_submitted'])) {
    $review_submitted = true;
    unset($_SESSION['review_submitted']);
  }

?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($product['product_name']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/assets/css/product-detail-style.css">
</head>
<body>

<div class="container">
  <!-- Gambar -->
  <div class="left">
    <div class="zoom-container">
      <button class="nav-arrow left-arrow" onclick="prevImage()">❮</button>
      <img id="mainPreviewImage"
          src="/assets/images/product/<?= htmlspecialchars($productImage) ?>"
          alt="<?= htmlspecialchars($product['product_name']) ?>"
          onmousemove="zoomImage(event, this)"
          onmouseleave="resetZoom(this)">
      <button class="nav-arrow right-arrow" onclick="nextImage()">❯</button>
    </div>

    <div class="thumbnail-list" id="thumbnailList">
      <?php foreach ($productGallery as $index => $img): ?>
        <img src="/assets/images/product/<?= htmlspecialchars($img) ?>"
            class="thumbnail"
            onclick="setImageIndex(<?= $index ?>)"
            alt="Thumbnail <?= $index ?>">
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Detail Produk -->
  <div class="middle">
    <div class="title"><?= htmlspecialchars($product['product_name']) ?></div>

    <div class="store-info-product">
      <img src="/assets/images/store/<?= htmlspecialchars($storeImage) ?>" 
            alt="Store" 
            style="cursor: pointer;"
            onclick='window.location.href="dashboard.php?module=store&pages=store-detail&store-id=<?= urlencode($store['store_id']) ?>"'      
      >
      <span style="cursor: pointer;"><?= htmlspecialchars($store['store_name']) ?></span>
    </div>

    <div class="rating">
        <?= $stars_visual ?> (<?= $total_ratings ?> rating<?= $total_ratings != 1 ? 's' : '' ?>)
    </div>
    <div class="price">Rp. <?= number_format($product['product_price'], 0, ',', '.') ?></div>

    <div class="info-list">
      <div><strong>Category : </strong> <?= htmlspecialchars($product['category_product_name'] ?? '-') ?></div>
      <div><strong>Condition :</strong> <?= htmlspecialchars($product['product_condition'] ?? 'Baru') ?></div>
    </div>

    <div class="about">
      <h3>Product Description</h3>
      <p><?= nl2br(htmlspecialchars($product['product_description'])) ?></p>
    </div>
  </div>

  <!-- Aksi -->
  <div class="right">
    <div class="stock">
      <?= $product['product_stock'] > 0 ? 'In Stock: ' . $product['product_stock'] : 'Out of Stock' ?>
    </div>
    <div class="actions">
      <form method="post" action="dashboard.php?module=cart&pages=cart-add" onsubmit="<?= isset($_SESSION['user_id']) ? '' : 'return showLoginModal(event);' ?>">
        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">

        <div class="quantity-wrapper">
          <label for="quantity">Quantity:</label>
          <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?= $product['product_stock'] ?>" required>
        </div>

        <button type="submit" class="btn btn-cart">╋ Add to Cart</button>
      </form>

      <button class="btn btn-buy" <?= isset($_SESSION['user_id']) ? '' : 'onclick="showLoginModal(event)"' ?>>
        Buy Now
      </button>
    </div>
  </div>

  <!-- 🌟 Form Ulasan -->
  <div class="review-form">
      <h3>Give A Product Review</h3>
      <form method="POST" onsubmit="<?= isset($_SESSION['user_id']) ? '' : 'return showLoginModal(event);' ?>">
          <div class="form-group">
              <label for="rating">Rating</label>
              <select name="rating" id="rating" required>
                  <option value=""> --- Choose Rating --- </option>
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                      <option value="<?= $i ?>"><?= $i ?> ★</option>
                  <?php endfor; ?>
              </select>
          </div>
          <div class="form-group">
              <label for="comment">Comment</label>
              <textarea name="comment" id="comment" rows="4" placeholder="Tulis ulasan Anda..." required></textarea>
          </div>
          <button type="submit" class="btn-submit">Send review</button>
      </form>
      <?php if ($review_submitted): ?>
        <div style="color: green; margin-top: 10px; text-align: center;">
          Review successfully submitted!
        </div>
      <?php endif; ?>
    </div>

    <!-- 🌟 List Ulasan -->
    <div class="review-list">
        <h3>User reviews (<?= $avg_rating ?> ★)</h3>
        <?php if (empty($reviews)): ?>
            <p class="no-review">There is no review for this product yet.</p>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-item">
                    <div class="review-header">
                        <strong><?= htmlspecialchars($review['username']) ?></strong>
                        <span class="rating"><?= str_repeat("★", $review['rating']) ?></span>
                    </div>
                    <p class="review-comment"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                    <small class="review-time"><?= date('d M Y, H:i', strtotime($review['created_at'])) ?></small>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<script>
  function zoomImage(e, image) {
    const rect = image.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * 100;
    const y = ((e.clientY - rect.top) / rect.height) * 100;
    image.style.transformOrigin = `${x}% ${y}%`;
  }

  function resetZoom(image) {
    image.style.transformOrigin = 'center center';
  }

  function setMainImage(thumbnail) {
    const mainImage = document.getElementById("mainPreviewImage");
    mainImage.src = thumbnail.src;
    mainImage.style.transformOrigin = "center center";
  }

  let galleryImages = <?= json_encode(array_values($productGallery)) ?>;
  let currentImageIndex = 0;

  const mainImage = document.getElementById('mainPreviewImage');
  const thumbnails = document.querySelectorAll('.thumbnail');

  function updateMainImage() {
    const newSrc = '/assets/images/product/' + galleryImages[currentImageIndex];
    mainImage.src = newSrc;

    // Highlight thumbnail
    thumbnails.forEach((thumb, idx) => {
      thumb.classList.toggle('active', idx === currentImageIndex);
    });
  }

  function prevImage() {
    currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
    updateMainImage();
  }

  function nextImage() {
    currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
    updateMainImage();
  }

  function setImageIndex(index) {
    currentImageIndex = index;
    updateMainImage();
  }

  // Initial highlight
  updateMainImage();
</script>

</body>
</html>