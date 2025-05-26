<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tubes - Spend Your Money</title>
    <link rel="stylesheet" href="../assets/css/customer-style.css">
</head>
<body class="home-page-body">
    <div class="nav-top">
        <div class="nav-left">
            <img src="https://freelogopng.com/images/all_img/1688364164amazon-logo-transparent.png" alt="amazon-logo" id="amazon-logo">
            <div class="delivery-location">
                <img src="https://img.icons8.com/?size=100&id=WtxXrJ8eK5wU&format=png&color=FFFFFF" id="white-location-icon">
                <div class="location-description">
                    <span id="progresive-location-line1">Deliver to</span>
                    <span id="progresive-location-line2">Indonesia</span>
                </div>
            </div>
        </div>
        <div class="nav-fill-search">
            <form method="post" action="#" class="form-fill-search">
                <select type="dropdown" id="category-dropwdown-button">
                    <option>All</option>
                </select>
                <input type="text" id="field-search">
                <button type="submit" id="button-search">
                    <img src="https://cdn-icons-png.flaticon.com/128/54/54481.png" id="icon-search">
                </button> 
            </form>
        </div>
        <div class="nav-tools">
            <div class="language">
                <img src="https://img.freepik.com/free-vector/illustration-indonesia-flag_53876-27131.jpg?ga=GA1.1.1722104604.1735956614&semt=ais_hybrid" id="country-flag">
                <select type="dropdown" id="language-dropdown">
                    <option>ID</option>
                </select>
            </div>
            <div class="Authentication">
                <a href="signin.php" style="cursor: pointer; background-color: rgb(19, 25, 33); color: white; border: none; text-decoration: none;">Hello, Sign in</a>
                <select type="dropdown" id="auth-menu">
                    <option>Account & Lists</option>
                </select>
            </div>
            <div class="returns-order">
                <span>Returns</span>
                <span>& Orders</span>
            </div>
            <div class="cart">
                <img src="https://img.icons8.com/?size=100&id=9671&format=png&color=FFFFFF" id="cart-icon">
                <span>Cart</span>
            </div>
        </div>
    </div>
    <div class="nav-bottom">
        <div class="hamburger-menu">
            <img src="https://img.icons8.com/?size=100&id=8113&format=png&color=FFFFFF" id="humberger-icon" alt="menu">
            <span>All</span>
        </div>
        <ul>
            <li><a href="#">Today's Deals</a></li>
            <li><a href="#">Customer Service</a></li>
            <li><a href="#">Registry</a></li>
            <li><a href="#">Gift Cards</a></li>
            <li><a href="#">Sell</a></li>
        </ul>
    </div>

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

        $storeObj = new Stores();
        $productObj = new Product();

        $stores = $storeObj->getAllStores();
    ?>

    <div class="all-store-sections">
        <?php foreach ($stores as $store): 
            $products = $productObj->getAllProductById($store['store_id']);
        ?>
        <div class="carousel-section">
            <div class="carousel-header">
                <h3><?= htmlspecialchars($store['store_name']) ?></h3>
                <a href="#">View All</a>
            </div>

            <?php if (empty($products)): ?>
                <div class="no-products-message">
                    <p><em>TIDAK ADA PRODUK DI TOKO INI.</em></p>
                </div>
            <?php else: ?>
                <div class="carousel-wrapper">
                    <button class="carousel-prev">&#10094;</button>
                    <div class="product-carousel">
                        <?php foreach ($products as $product): 
                            $photo = isset($product['product_image']) && is_string($product['product_image']) && $product['product_image'] !== ''
                            ? $product['product_image']
                            : 'no-image-product.jpg';
                        ?>
                        <div class="product-card">
                            <img src="/assets/images/<?php echo htmlspecialchars($photo); ?>" alt="..." onerror="this.src='/assets/images/no-image-product.jpg'" class="product-image">
                            <div class="product-detail">
                                <h4><?= htmlspecialchars($product['product_name']) ?></h4>
                                <p class="price">$<?= number_format($product['product_price'], 2) ?></p>
                                <p><?= htmlspecialchars($product['product_description']) ?></p>
                                <a href="product-detail.php?id=<?= urlencode($product['product_id']) ?>" class="view-product-button">Lihat Produk</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-next">&#10095;</button>
                </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>


    <script>
        document.querySelectorAll('.carousel-wrapper').forEach(wrapper => {
            const carousel = wrapper.querySelector('.product-carousel');
            wrapper.querySelector('.carousel-next').addEventListener('click', () => {
                carousel.scrollBy({ left: 220, behavior: 'smooth' });
            });
            wrapper.querySelector('.carousel-prev').addEventListener('click', () => {
                carousel.scrollBy({ left: -220, behavior: 'smooth' });
            });
        });
    </script>

    <div class="footer">
        <div class="aditional-content">
            <div class="buy-section">
                <h3>Buy</h3>
                <a href="#">Registration</a>
                <a href="#">Bidding & Buying help</a>
                <a href="#">Stores</a>
                <a href="#">Creator Collections</a>
                <a href="#">eBay for Charity</a>
                <a href="#">Charity Shop</a>
                <a href="#">Seasonal Sales and events</a>
                <a href="#">eBay Gift Cards</a>
            </div>
            <div class="footer-section2">
                <div class="sell-section">
                    <h3>Sell</h3>
                    <a href="#">Start Selling</a>
                    <a href="#">How To Sell</a>
                    <a href="#">Bussines Sellers</a>
                    <a href="#">Affiliates</a>
                </div>
                <div class="tools-apps-section">
                    <h3>Tools & Apps</h3>
                    <a href="#">Developers</a>
                    <a href="#">Security center</a>
                    <a href="#">Site map</a>
                </div>
            </div>
            <div class="footer-section3">
                <div class="eBay-companies-section">
                    <h3>eBay Companies</h3>
                    <a href="#">TCGplayer</a>
                </div>
                <div class="stay-connected-section">
                    <h3>Stay Connected</h3>
                    <a href="#">Facebook</a>
                    <a href="#">Twitter</a>
                </div>
            </div>
            <div class="about-ebay-section">
                <h3>About eBay</h3>
                <a href="#">Company Info</a>
                <a href="#">News</a>
                <a href="#">Deferred Prosecution Agreement with District of Massachusetts</a>
                <a href="#">Inverstors</a>
                <a href="#">Careers</a>
                <a href="#">Diversity & Inclusion</a>
                <a href="#">Global Impact</a>
                <a href="#">Government relations</a>
                <a href="#">Advertise with us</a>
                <a href="#">Policies</a>
                <a href="#">Verified Rights Owner (VeRO) Program</a>
                <a href="#">eCI Licenses</a>
            </div>
            <div class="footer-section5">
                <div class="help-contact-section">
                    <h3>Help & Contact</h3>
                    <a href="#">Seller Center</a>
                    <a href="#">Contact Us</a>
                    <a href="#">eBay Returns</a>
                    <a href="#">eBay Money Back Guarantee</a>
                </div>
                <div class="community-section">
                    <h3>Community</h3>
                    <a href="#">Announcements</a>
                    <a href="#">eBay Community</a>
                    <a href="#">eBay for Business Podcast</a>
                </div>
                <div class="ebay-sites-section">
                    <h3>eBay Contact</h3>
                    <select>
                        <option value="ID">Indonesia</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="copyright-licence-section">
            <p>All reserved &#xA9; 2025 marketplace</p>
        </div>
    </div>
</body>
</html>