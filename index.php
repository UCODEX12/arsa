<?php
// Include the database connection
include 'db_connect.php';

// Initialize the products variable
$products = array();

try {
    // Fetch all products initially
    $query = "SELECT * FROM products";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>





<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  
    <!-- Boxicons -->
    <link
      href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css"
      rel="stylesheet"
    />
    <!-- Glide js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.4.1/css/glide.core.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.4.1/css/glide.theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom StyleSheet -->
    <link rel="stylesheet" href="./css/styles.css" />
    <title>ARSA ONLINE STORE</title>
  </head>
  <body>
    <!-- Header -->
    <header class="header" id="header">
      <!-- Top Nav -->
      <div class="top-nav">
        <div class="container d-flex">
          <p>Order Online Or Call Us: (072) 587-6139</p>
          <ul class="d-flex">
            <li><a href="#">About Us</a></li>
            <li><a href="#">FAQ</a></li>
            <li><a href="#">Contact</a></li>
          </ul>
        </div>
      </div>
      <div class="navigation">
        <div class="nav-center container d-flex">
        <a href="/" class="logo"><h1>ARSA</h1></a>

          <ul class="nav-list d-flex">
            <li class="nav-item">
              <a href="/" class="nav-link">Home</a>
            </li>
            <li class="nav-item">
              <a href="product.html" class="nav-link">Shop</a>
            </li>
            <li class="nav-item">
            <a href="#terms" class="nav-link">About</a>
            </li>
            <li class="nav-item">
              <a href="#about" class="nav-link">Contact</a>
            </li>
            <li class="nav-item">
              <a href="admin.php" class="nav-link">Admin</a>
            </li>
            <li class="icons d-flex">
            <a href="login.html" class="icon">
              <i class="bx bx-user"></i>
            </a>
          <div class="hamburger">
            <i class="bx bx-menu-alt-left"></i>
          </div>
        </div>
      </div>
<!-- body image -->
<section class="glide">
  <div class="glide__track" data-glide-el="track">
    <ul class="glide__slides">
      <li class="glide__slide"><img src="images/body.png" alt="Slide 1"></li>
    </ul>
  </div>
</section>
&nbsp;

  <!-- Product Section -->
  <section class="product-section container" id="product-section">
    <?php if (!empty($products)): ?>
      <?php foreach ($products as $product): ?>
      <div class="product-box" data-product-name="<?php echo strtolower($product['product_name']); ?>">
        <img src="<?php echo $product['image_url']; ?>" alt="<?php echo $product['product_name']; ?>">
        <h3><?php echo $product['product_name']; ?></h3>
        <div class="price">
          <span class="original-price">Rs. <?php echo number_format($product['original_price'], 2); ?></span>
          <span class="discounted-price">Rs. <?php echo number_format($product['discounted_price'], 2); ?></span>
        </div>
        <div class="product-buttons">
        <a href="register.php" class="btn-shop">Shop Now</a> 
        </div>
      </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No products found.</p>
    <?php endif; ?>
  </section>


<!-- Add More Items Button -->
<div class="more-items-button">
  <button id="loadMoreBtn" class="btn-load-more">More Items</button>
</div>


  </body>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.4.1/glide.min.js"></script>
  <script src="all.js"></script>
</html>
