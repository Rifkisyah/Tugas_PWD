<?php
    session_start();
    // if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    //     header("Location: signin.php");
    //     exit();
    // }
    require_once '../model/category-products.php';

    $categoryObj = new CategoryProducts();
    $categories = $categoryObj->getAllCategories();

    $onclick = isset($_SESSION['user_id']) 
    ? "window.location.href='dashboard.php?module=cart&pages=shopping-cart'" 
    : "return showLoginModal(event);";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tubes - Spend Your Money</title>
    <link rel="stylesheet" href="../assets/css/customer-style.css">
    <link rel="stylesheet" href="/assets/css/product-detail-style.css">
</head>
<body class="home-page-body">
    <div class="nav-top">
        <div class="nav-left">
            <img src="../assets/images/icon/amazon-white.png" alt="amazon-logo" id="amazon-logo" onclick="window.location.href='dashboard.php'">
            <div class="delivery-location">
                <img src="../assets/images/icon/location-white.png" id="white-location-icon">
                <div class="location-description">
                    <span id="progresive-location-line1">Deliver to</span>
                    <span id="progresive-location-line2">Indonesia</span>
                </div>
            </div>
        </div>
        <div class="nav-fill-search">
            <form method="get" action="#" class="form-fill-search">
                <select id="category-dropwdown-button" name="category_id" onchange="this.form.submit()">
                    <option value="All">All</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['category_product_id'] ?>" <?= (isset($_GET['category_id']) && $_GET['category_id'] == $category['category_product_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['category_product_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" id="field-search" name="q" value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" placeholder="Search...">
                <button type="submit" id="button-search">
                    <img src="../assets/images/icon/search-black.png" id="icon-search">
                </button> 
            </form>
        </div>
        <div class="nav-tools">
        <div class="language">
            <img src="../assets/images/icon/usa-country-flag.png" id="country-flag" alt="Country Flag">
            <select id="language-dropdown" onchange="changeLanguage(this.value)">
                <option value="en"  selected>ENG</option>
                <option value="id">IDN</option>
            </select>
        </div>
            <!-- Pastikan session_start(); sudah dijalankan di file ini -->
            <div class="authentication" onmouseover="showDropdown()" onmouseout="hideDropdown()">
                <a href="<?= isset($_SESSION['username']) ? '#' : 'signin.php' ?>" class="auth-button">
                    <?= isset($_SESSION['username']) ? 'Hello, ' . htmlspecialchars($_SESSION['username']) : 'Hello, sign in'; ?>
                    <br>
                    <span style="font-weight: bold;">Account & Lists</span>
                </a>
                <div class="auth-dropdown" id="authDropdown">
                    <?php if (!isset($_SESSION['username'])): ?>
                        <button class="sign-in-btn" onclick="window.location.href='signin.php'">Sign in</button>
                        <p class="new-customer">New customer? <a href="signup.php">Start here.</a></p>
                    <?php else: ?>
                        <p style="margin: 0 0 10px 0; font-size: 13px;">Hello, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
                        <button class="logout-btn" onclick="window.location.href='signout.php'">Sign out</button>
                    <?php endif; ?>
                    
                    <div class="dropdown-columns">
                        <div class="column">
                            <h4>Your Lists</h4>
                            <a href="#">Create a List</a>
                            <a href="#">Find a List or Registry</a>
                        </div>
                        <div class="column">
                            <h4>Your Account</h4>
                            <a href="#">Account</a>
                            <a href="#">Orders</a>
                            <a href="#">Recommendations</a>
                            <a href="#">Browsing History</a>
                            <a href="#">Watchlist</a>
                            <a href="#">Video Purchases & Rentals</a>
                            <a href="#">Kindle Unlimited</a>
                            <a href="#">Content & Devices</a>
                            <a href="#">Subscribe & Save Items</a>
                            <a href="#">Memberships & Subscriptions</a>
                            <a href="#">Music Library</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="returns-order" onclick="<?= isset($_SESSION['user_id']) ? "window.location.href='dashboard.php?module=checkout&pages=history'" : "return showLoginModal(event);" ?>">
                <span>Returns</span>
                <span>& Orders</span>
            </div>
            <div class="cart" onclick="<?= $onclick ?>">
                <img src="../assets/images/icon/shopping-cart-white.png" id="cart-icon">
                <span>Cart</span>
            </div>
        </div>
    </div>
    <div class="nav-bottom">
        <div class="hamburger-menu">
            <img src="../assets/images/icon/burger-menu-white.png" id="humberger-icon" alt="menu">
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

    <div class="container-content">
        <?php
            $page = 'pages/dashboard-main.php';
            if(isset($_GET['module'])){
              $page = 'pages/'. $_GET['module'].'/'.$_GET['pages'].'.php';
            }
            require($page);
          ?>
      </div><!--END container-fluid-->
    </div>

    <div id="loginModal" class="modal-overlay" style="display:none;">
        <div class="modal-box">
        <h2>Account Required</h2>
        <p>To proceed with this action, please log in or register first.</p>
        <div class="modal-actions">
            <a href="signin.php" class="btn amazon-primary">signin</a>
            <a href="signup.php" class="btn amazon-secondary">signup</a>
        </div>
        <button class="modal-close" onclick="closeLoginModal()">×</button>
        </div>
    </div>

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
    <script>
        const dropdown = document.getElementById('authDropdown');
        const overlay = document.getElementById('pageOverlay');
        
        function showDropdown() {
            dropdown.style.display = 'block';
            overlay.style.display = 'block';
        }
        
        function hideDropdown() {
            dropdown.style.display = 'none';
            overlay.style.display = 'none';
        }
        
        // Supaya klik di overlay juga menutup dropdown
        overlay.addEventListener('click', () => {
            hideDropdown();
        });

        function showLoginModal(event) {
            event.preventDefault();
            document.getElementById('loginModal').style.display = 'flex';
            return false;
        }

        function closeLoginModal() {
            document.getElementById('loginModal').style.display = 'none';
        }

        function changeLanguage(lang) {
            const flag = document.getElementById("country-flag");
            
            if (lang === "id") {
                flag.src = "../assets/images/icon/id-country-flag.jpg";
                flag.alt = "Indonesian Flag";
                // Optional: redirect
                // window.location.href = "index.php?lang=id";
            } else if (lang === "en") {
                flag.src = "../assets/images/icon/usa-country-flag.png";
                flag.alt = "US Flag";
                // Optional: redirect
                // window.location.href = "index.php?lang=en";
            }
        }
    </script>
    <div id="pageOverlay"></div>
</body>
</html>