<?php
session_start();
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!Doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>FlipMart+</title>
  <link rel="stylesheet" href="templates/style.css">
</head>
<body>
  <!-- ======= Header ======= -->
  <header class="topbar">
    <!-- Toast container for notifications -->
    <div id="toastStack" style="position:fixed;top:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:12px;"></div>
    
    <div class="header-top">
      <a href="main.php" class="brand" style="text-decoration: none; color: inherit;">
        <span style="color: #fff;">Flip</span><span style="color: #2563eb;">Mart</span>+<span class="accent"></span>
      </a>
      <button class="mobile-nav-toggle" id="mobileNavToggle" aria-label="Toggle navigation">☰</button>
    </div>

    <div class="searchbar">
      <input id="searchInput" type="search" placeholder="Search for products, brands and more" />
      <button id="searchBtn">
        <span class="search-icon">🔍</span>
        <span class="search-text">Search</span>
      </button>
    </div>

    <div class="controls">
      <select id="sortSelect" aria-label="Sort">
        <option value="relevance">Sort: Relevance</option>
        <option value="price-asc">Price — Low to High</option>
        <option value="price-desc">Price — High to Low</option>
        <option value="rating-desc">Rating — High to Low</option>
      </select>

      <button id="openCompare" class="muted" aria-label="Open compare">Compare</button>

      <button id="cartBtn" class="cartBtn">
        <span class="cart-icon">🛒</span>
        <span class="cart-text">Cart (<span id="cartCount">0</span>)</span>
      </button>

      <a id="profileBtn" href="profile.php" class="profileBtn">
        <span class="profile-icon">👤</span>
        <span class="profile-text">Profile</span>
      </a>

      <a href="logout.php" class="logoutBtn" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 6px; background: rgba(239, 68, 68, 0.1); color: #ef4444; transition: all 0.2s;">
        <span>🚪</span><span>Logout</span>
      </a>

      <button id="themeToggle" aria-label="Toggle theme" class="muted">🌓</button>
    </div>
  </header>

  <!-- ======= Main Content ======= -->
  <main class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <h3>Filters</h3>
        <button class="sidebar-close" id="sidebarClose">&times;</button>
      </div>
      
      <div class="sidebar-section">
        <h4>Categories</h4>
        <ul id="categoryList" class="category-list"></ul>
      </div>

      <div class="sidebar-section">
        <h4>Price Range</h4>
        <div class="price-filters">
          <button data-max="1000">Under ₹1,000</button>
          <button data-max="3000">Under ₹3,000</button>
          <button data-max="10000">Under ₹10,000</button>
          <button data-max="">All Prices</button>
        </div>
        <div id="sidebarCompareBox" style="margin-top:12px;"></div>
      </div>
    </aside>

    <!-- Products -->
    <section class="products">
      <div id="productsGrid" class="products-grid"></div>
      <div id="noResults" class="no-results" hidden>No products match your search.</div>
      <div style="text-align:center; margin:18px 0;">
        <button id="loadMore" class="muted">Load more</button>
      </div>
    </section>
  </main>

  <!-- ======= Product Modal ======= -->
  <div id="productModal" class="modal" aria-hidden="true">
    <div class="modal-content">
      <button class="close" id="modalClose">&times;</button>
      <div class="modal-body" id="modalBody"></div>
    </div>
  </div>

  <!-- ======= Cart Drawer ======= -->
  <aside id="cartDrawer" class="cart-drawer" aria-hidden="true">
    <div class="cart-header">
      <h3>Your Cart</h3>
      <button id="closeCart">&times;</button>
    </div>
    <div id="cartItems" class="cart-items"></div>

    <div class="cart-footer">
      <div class="totals">
        <div>Subtotal:</div>
        <div id="cartSubtotal">₹0</div>
      </div>
      <!-- ✅ Checkout button linked to script.js -->
<a href="order-confirmation.php" id="checkoutBtn" class="primary"
   style="display:block;text-align:center;width:100%;padding:12px;border-radius:8px;
   font-weight:600;cursor:pointer;background:linear-gradient(90deg,#5b8def 0%,#3a6ee8 100%);
   color:white;text-decoration:none;">
   Checkout
</a>

<script>
  document.getElementById('checkoutBtn').addEventListener('click', function(e) {
    // cart clear karein
    localStorage.removeItem('fk_cart_v1');

    // UI update ke liye optional:
    if (typeof updateCartUI === 'function') {
      updateCartUI();
    }
  });
</script>


      <button id="clearCartBtn" class="muted">Clear Cart</button>
    </div>
  </aside>

  <!-- ======= Compare Modal ======= -->
  <div id="compareModal" class="modal" aria-hidden="true">
    <div class="modal-content">
      <button class="close" id="compareClose">&times;</button>
      <div class="modal-body" id="compareBody"></div>
    </div>
  </div>

  <!-- ======= Footer ======= -->
  <footer class="site-footer">
    <div class="footer-columns">
      <div class="footer-col">
        <h4>ABOUT</h4>
        <a href="#">Contact Us</a><a href="#">About Us</a><a href="#">Careers</a>
        <a href="#">FlipMart+ Stories</a><a href="#">Press</a><a href="#">Corporate Information</a>
      </div>
      <div class="footer-col">
        <h4>GROUP COMPANIES</h4>
        <a href="#">Myntra</a><a href="#">Cleartrip</a><a href="#">Shopsy</a>
      </div>
      <div class="footer-col">
        <h4>HELP</h4>
        <a href="#">Payments</a><a href="#">Shipping</a><a href="#">Cancellation & Returns</a><a href="#">FAQ</a>
      </div>
      <div class="footer-col">
        <h4>CONSUMER POLICY</h4>
        <a href="#">Cancellation & Returns</a><a href="#">Terms Of Use</a>
        <a href="#">Security</a><a href="#">Privacy</a><a href="#">Sitemap</a><a href="#">Grievance Redressal</a>
      </div>
      <div class="footer-col address">
        <h4>Mail Us:</h4>
        <p>FlipMart+ Internet Private Limited,<br>India</p>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2025 FlipMart+ Made by Me 🔨🤖🔧</p>
    </div>
  </footer>

  <!-- ======= Background Animation ======= -->
  <canvas id="hero-canvas"></canvas>

  <!-- Toast container -->
  <div id="toast-container" style="position:fixed;top:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:12px;"></div>

  <!-- Overlay -->
  <div id="overlay" class="overlay" aria-hidden="true"></div>

  <!-- Wishlist & Compare panels (placeholders for script.js) -->
  <div id="wishlistPanel" style="display:none"></div>
  <div id="comparePanel" style="display:none"></div>
  <select id="categoryDropdown" aria-hidden="true" style="display:none"></select>

  <!-- ======= Background Stars (Three.js) ======= -->
  <script type="module">
    import * as THREE from 'https://cdn.jsdelivr.net/npm/three@0.161.0/build/three.module.js';
    const canvas = document.getElementById("hero-canvas");
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth/window.innerHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({canvas, alpha:true});
    renderer.setSize(window.innerWidth, window.innerHeight);
    scene.fog = new THREE.FogExp2(0x000011, 0.00045);
    renderer.setClearColor(0x000011, 1);

    const geometry = new THREE.BufferGeometry();
    const vertices = [], colors = [], color = new THREE.Color();
    for (let i=0;i<2000;i++){
      vertices.push((Math.random()-0.5)*2000,(Math.random()-0.5)*2000,(Math.random()-0.5)*2000);
      const palette=[0xffffff,0x9f7aea,0x6366f1,0xf472b6];
      color.setHex(palette[Math.floor(Math.random()*palette.length)]);
      colors.push(color.r,color.g,color.b);
    }
    geometry.setAttribute('position', new THREE.Float32BufferAttribute(vertices,3));
    geometry.setAttribute('color', new THREE.Float32BufferAttribute(colors,3));
    const material=new THREE.PointsMaterial({size:1.2,vertexColors:true,transparent:true,opacity:0.9});
    const stars=new THREE.Points(geometry,material);scene.add(stars);camera.position.z=800;
    function animate(){requestAnimationFrame(animate);stars.rotation.x+=0.0004;stars.rotation.y+=0.0007;renderer.render(scene,camera);} animate();
    window.addEventListener('resize',()=>{camera.aspect=window.innerWidth/window.innerHeight;camera.updateProjectionMatrix();renderer.setSize(window.innerWidth,window.innerHeight);});
  </script>

  <!-- ======= Scripts ======= -->
  <script src="templates/script2.js"></script> <!-- PRODUCTS data -->
  <script src="templates/script.js"></script> <!-- main logic -->
  <script src="templates/common.js"></script> <!-- helpers (toast, etc.) -->
</body>
</html>
