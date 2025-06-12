<?php
    require_once('../model/Stores.php');
    require_once('../model/Products.php');
    require_once '../model/cart.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['store_id'])) {
        require_once('../model/Stores.php');
        session_start(); // Jika belum dipanggil sebelumnya
    
        ob_clean(); // Bersihkan buffer output
        header('Content-Type: application/json');
    
        $storeObj = new Store();
        $userId = $_SESSION['user_id'] ?? null;
        $storeId = (int)$_POST['store_id'];
        $action = $_POST['action'];
    
        if (!$userId || !$storeId) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
            exit;
        }
    
        if ($action === 'follow') {
            $storeObj->followStore($userId, $storeId);
        } elseif ($action === 'unfollow') {
            $storeObj->unfollowStore($userId, $storeId);
        }
    
        echo json_encode([
            'status' => 'success',
            'isFollowing' => $storeObj->isUserFollowing($userId, $storeId),
            'followerCount' => $storeObj->getFollowerCount($storeId)
        ]);
        exit; // Sangat penting untuk menghentikan proses lebih lanjut (HTML)
    }

    $storeObj = new Store();
    $productObj = new Product();
    $cartModel = new Cart();

    $storeId = isset($_GET['store-id']) ? (int)$_GET['store-id'] : 0;
    if (!$storeId) {
        die("Toko tidak ditemukan.");
    }

    $stores = $storeObj->getAllStores();
    $products = $productObj->getAllProduct();

    $store = $storeObj->getStoreById($storeId);
    $categories = $storeObj->getStoreCategories($storeId);

    $store['avg_rating'] = $storeObj->getStoreAverageRating($storeId);
    $store['total_ratings'] = $storeObj->getStoreTotalRatings($storeId);
    $store['total_followers'] = $storeObj->getFollowerCount($storeId);
    $isFollowing = isset($_SESSION['user_id']) ? $storeObj->isUserFollowing($_SESSION['user_id'], $storeId) : false;

    $average_rating = floatval($store['avg_rating']);
    $total_ratings = intval($store['total_ratings']);

    // Generate visual stars
    if ($total_ratings > 0) {
        $full_stars = floor($average_rating);
        $half_star = ($average_rating - $full_stars) >= 0.5 ? 1 : 0;
        $empty_stars = 5 - $full_stars - $half_star;

        $stars_visual = str_repeat("★", $full_stars)
                    . ($half_star ? "½" : "")
                    . str_repeat("☆", $empty_stars);
    } else {
        $stars_visual = 'no rating yet';
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($store['store_name']) ?></title>
    <link rel="stylesheet" href="../assets/css/store-detail.css">
</head>
<body>
    <div class="container">
        <div class="store-header">
            <img src="../assets/images/store/<?= htmlspecialchars($store['store_image']) ?>" alt="Foto Toko">
            <div class="store-info">
                <h2><?= htmlspecialchars($store['store_name']) ?></h2>
                <div class="store-meta">
                    <div class="rating">
                        <?= is_numeric($store['avg_rating']) ? "$stars_visual ({$total_ratings} rating" . ($total_ratings != 1 ? "s" : "") . ")" : $stars_visual ?>
                    </div>
                </div>
            </div>
            <div class="follow-row">
                <span id="follower-count"><?= $store['total_followers'] ?> Followers</span>
                <?php if (!isset($_SESSION['user_id'])): ?>
                <button 
                    id="follow-btn"
                    class="<?= $isFollowing ? 'unfollow' : '' ?>"
                    data-following="<?= $isFollowing ? '1' : '0' ?>"
                    data-store-id="<?= $storeId ?>"
                    onclick="return showLoginModal(event);"
                >
                    <?= $isFollowing ? 'Unfollow' : 'Follow' ?>
                </button>
            <?php else: ?>
                <button 
                    id="follow-btn"
                    class="<?= $isFollowing ? 'unfollow' : '' ?>"
                    data-following="<?= $isFollowing ? '1' : '0' ?>"
                    data-store-id="<?= $storeId ?>"
                >
                    <?= $isFollowing ? 'Unfollow' : 'Follow' ?>
                </button>
            <?php endif; ?>
            </div>
        </div>

        <div class="tab-header">
            <button class="tab-button" id="btn-products" onclick="showTab('products')">New Release</button>
            <button class="tab-button" id="btn-categories" onclick="showTab('categories')">Category Product</button>
            <button class="tab-button" id="about" onclick="showTab('categories')">About Us</button>
        </div>

        <?php
            $storeProducts = array_filter($products, function ($product) use ($storeId) {
                return $product['store_id'] === $storeId;
            });

            usort($storeProducts, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
        ?>
        <div class="tab-content active" id="products">
            <?php if (empty($storeProducts)): ?>
                <p>Tidak ada produk untuk toko ini.</p>
            <?php else: ?>
                <div class="carousel-section">
                    <div class="carousel-wrapper">
                        <div class="product-carousel">
                            <?php foreach ($storeProducts as $product): ?>
                                <?php
                                    $isInCart = false;
                                    $cartItem = null;

                                    if (isset($_SESSION['user_id'])) {
                                        $cartItem = $cartModel->getItem($_SESSION['user_id'], $product['product_id']);
                                        $isInCart = $cartItem ? true : false;
                                    }

                                    $photo = isset($product['product_preview']) && is_string($product['product_preview']) && $product['product_preview'] !== ''
                                        ? $product['product_preview']
                                        : 'no-image-product.jpg';
                                ?>
                                <div class="product-card">
                                    <img 
                                        src="/assets/images/product/<?= htmlspecialchars($photo); ?>"
                                        alt="<?= htmlspecialchars($product['product_name']); ?>" 
                                        onerror="this.src='/assets/images/product/no-image-product.jpg'" 
                                        class="product-image" 
                                        onclick='window.location.href="dashboard.php?module=product&pages=product-detail&id=<?= urlencode($product["product_id"]) ?>"'
                                    >
                                    <div class="product-detail">
                                        <h3 class="price">Rp. <?= number_format($product['product_price'], 0, ',', '.') ?></h3>
                                        <h4 onclick='window.location.href="dashboard.php?module=product&pages=product-detail&id=<?= urlencode($product["product_id"]) ?>"'>
                                            <?= htmlspecialchars($product['product_name']) ?>
                                        </h4>
                                        <p><?= htmlspecialchars($product['product_description']) ?></p>

                                        <?php if ($isInCart): ?>
                                            <form method="POST" action="dashboard.php?module=cart&pages=cart-delete" onsubmit="return confirm('Hapus item dari cart?');">
                                                <input type="hidden" name="cart_id" value="<?= htmlspecialchars($cartItem['cart_id']) ?>">
                                                <button type="submit" class="view-product-button">✓ Added</button>
                                            </form>
                                        <?php else: ?>
                                            <form method="post" action="dashboard.php?module=cart&pages=cart-add" onsubmit="<?= isset($_SESSION['user_id']) ? '' : 'return showLoginModal(event);' ?>">
                                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="view-product-button">╋ Add to Cart</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>


        <div class="tab-content" id="categories">
            <ul class="category-list">
                <?php foreach ($categories as $cat): ?>
                    <li onclick="showCategoryProducts('<?= htmlspecialchars($cat) ?>')"><?= htmlspecialchars($cat) ?></li>
                <?php endforeach; ?>
            </ul>
            <div id="category-products" style="margin-top: 20px;"></div>
        </div>
    </div>

    <div id="loginModal" class="modal-overlay" style="display:none;">
      <div class="modal-box">
        <h2>Account Required</h2>
        <p>To proceed with this action, please Sign in or Sign up first.</p>
        <div class="modal-actions">
          <a href="signin.php" class="btn amazon-primary">signin</a>
          <a href="signup.php" class="btn amazon-secondary">signup</a>
        </div>
        <button class="modal-close" onclick="closeLoginModal()">×</button>
      </div>
    </div>
    
    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            document.getElementById('btn-' + tabId).classList.add('active');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const followBtn = document.getElementById('follow-btn');
            const followerCountSpan = document.getElementById('follower-count');
            const storeId = followBtn.dataset.storeId;

            followBtn.addEventListener('click', () => {
                if (!storeId) return;

                followBtn.disabled = true;
                const action = followBtn.classList.contains('unfollow') ? 'unfollow' : 'follow';

                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `action=${action}&store_id=${storeId}`
                })
                .then(res => res.json())
                .then(data => {
                    console.log(data);
                    if (data.status === 'success') {
                        if (data.isFollowing) {
                            followBtn.classList.add('unfollow');
                            followBtn.textContent = '✓ Unfollow';
                        } else {
                            followBtn.classList.remove('unfollow');
                            followBtn.textContent = 'Follow';
                        }
                        followerCountSpan.textContent = `${data.followerCount} Followers`;
                    } else {
                        alert(data.message || 'Terjadi kesalahan');
                    }
                })
                .catch(err => alert('Error: ' + err))
                .finally(() => {
                    followBtn.disabled = false;
                });
            });
        });

        function showLoginModal(event) {
            event.preventDefault();
            document.getElementById('loginModal').style.display = 'flex';
            return false;
        }

        function closeLoginModal() {
            document.getElementById('loginModal').style.display = 'none';
        }
    </script>
</body>
</html>
