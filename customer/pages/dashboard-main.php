<div class="content-grid">
    <a href="#" class="card card1" style="background-image: url('https://i.pinimg.com/474x/13/48/9c/13489ce8e1ede4630f4ab29b1a338fa0.jpg'); background-size: cover;">
        <h3>Save on personal care</h3>
        <p>shop now</p>
    </a>
    <a href="#" class="card card2" style="background-image: url('https://i.pinimg.com/474x/11/a3/cc/11a3cc7960fdd1cd1e8e196415b35183.jpg'); background-size: cover;">
        <h3>La Roche-Posay just landed</h3>
        <p>shop now</p>
    </a>
    <a href="#" class="card card3" style="background-image: url('https://i.pinimg.com/474x/ac/c3/56/acc356927064f2499cd0b90ff140389b.jpg'); background-size: cover;">
        <h3>Top 100 Easter basket fillers</h3>
        <p>shop now</p>
    </a>
    <a href="#" class="card card4" style="background-image: url('https://i.pinimg.com/474x/93/ab/5a/93ab5a8222234d2b015bc62a24ea2d95.jpg'); background-size: cover;">
        <h3>The women's shoe edit</h3>
        <p>shop now</p>
    </a>
    <a href="#" class="card card5" style="background-image: url('https://i.pinimg.com/474x/47/19/be/4719be16543ca429db54adfd135ec76f.jpg'); background-size: cover;">
        <h3>Top style trends for women</h3>
        <p>shop now</p>
    </a>
    <a href="#" class="card card6" style="background-image: url('https://i.pinimg.com/474x/3d/c1/35/3dc135d38de489358f85ddd0885392eb.jpg'); background-size: cover; background-position: center;">
        <h3>Up to 65% off</h3>
        <p>shop now</p>
    </a>
    <a href="#" class="card card7" style="background-image: url('https://i.pinimg.com/474x/45/d4/6a/45d46aeb46abd6cad7298b2b202596f8.jpg'); background-size: cover;">
        <h3>Patio upgrades for all</h3>
        <p>shop now</p>
    </a>
    <a href="#" class="card card8" style="background-image: url('https://i.pinimg.com/474x/b9/de/96/b9de96db62c05c0c4e90975e629c4384.jpg'); background-size: cover;">
        <h3>Time to glow</h3>
        <p>shop now</p>
    </a>
    <a href="#" class="card card9" style="background-image: url('https://i.pinimg.com/474x/c1/c8/fe/c1c8fe897dbe6c95ebe675f63ec01a55.jpg'); background-size: cover;">
        <h3>Free shipping with Walmart+</h3>
        <p>shop now</p>
    </a>
    <a href="#" class="card card10" style="background-image: url('https://i.pinimg.com/474x/83/70/7d/83707d6a9f7e4a31c4868efc262f93fc.jpg'); background-size: cover;">
        <h3>More offers inside</h3>
        <p>shop now</p>
    </a>
</div>

<?php
    require_once('../model/Stores.php');
    require_once('../model/Products.php');
    require_once '../model/cart.php';

    $storeObj = new Store();
    $productObj = new Product();
    $cartModel = new Cart();

    $stores = $storeObj->getAllStores();
    $products = $productObj->getAllProduct();

?>

<div class="all-store-sections">
    <?php foreach ($stores as $store): ?>
        <?php
            $storeProducts = array_filter($products, function ($product) use ($store) {
                return $product['store_id'] === $store['store_id'];
            });

            if (empty($storeProducts)) continue;

            $storeImage = isset($store['store_image']) && $store['store_image'] !== ''
                ? $store['store_image']
                : 'no-image-store.jpg';
        ?>

        <div class="carousel-section">
            <div class="carousel-header">
                <div class="store-info">
                    <img src="/assets/images/store/<?= htmlspecialchars($storeImage) ?>"
                         alt="<?= htmlspecialchars($store['store_name']) ?>"
                         class="store-image"
                         onerror="this.src='/assets/images/store/no-image-store.jpg'"     
                         onclick='window.location.href="dashboard.php?module=store&pages=store-detail&store-id=<?= urlencode($store['store_id']) ?>"'
                    >
                    <h3><?= htmlspecialchars($store['store_name']) ?></h3>
                </div>
                <a href="dashboard.php?module=store&pages=store-product&id=<?= urlencode($store['store_id']) ?>" class="view-all-link">View All</a>
            </div>

            <div class="carousel-wrapper">
                <button class="carousel-prev">&#10094;</button>
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
                                    <form method="POST" action="dashboard.php?module=cart&pages=cart-delete">
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
                <button class="carousel-next">&#10095;</button>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
    document.querySelectorAll('.carousel-wrapper').forEach(wrapper => {
        const carousel = wrapper.querySelector('.product-carousel');
        wrapper.querySelector('.carousel-next').addEventListener('click', () => {
            carousel.scrollBy({ left: 240, behavior: 'smooth' });
        });
        wrapper.querySelector('.carousel-prev').addEventListener('click', () => {
            carousel.scrollBy({ left: -240, behavior: 'smooth' });
        });
    });
</script>