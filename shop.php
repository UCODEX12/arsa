<?php
include 'db_connect.php';

session_start();

// Check if user is logged in
if (isset($_SESSION['user']['username'])) {
    $username = $_SESSION['user']['username'];
    $user_id = $_SESSION['user']['id']; // Make sure you have the user ID in session

    try {
        $pdo = new PDO('mysql:host=localhost;dbname=arsa', 'root', 'Sun123flower@');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Get the cart count for the logged-in user
        $stmt = $pdo->prepare("SELECT COUNT(*) AS cart_count FROM cart WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $cart_count = $stmt->fetchColumn();

        // Get user details
        $stmt = $pdo->prepare("SELECT email, mobile FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $user_details = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_email = $user_details['email'];
        $user_mobile = $user_details['mobile'];
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
        $cart_count = 0; // Set count to 0 if there's an error
    }
} else {
    // If the user is not logged in, redirect to the login page
    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> ARSA</title>
    <link rel="stylesheet" href="shop.css"> <!-- Include your CSS file here -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- For icons -->
</head>
<body>
<header>
    <h1>Welcome, <?php echo htmlspecialchars($username); ?></h1> <!-- Display the logged-in username -->
</header>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-logo">ARSA</div>
    <button class="nav-toggle" id="nav-toggle">
        <i class="fas fa-bars"></i>
    </button>
    <div class="nav-menu" id="nav-menu">
        <a href="index.php" class="nav-item">Home</a>
        <a href="about.php" class="nav-item">About Us</a>
        <a href="contact.php" class="nav-item">Contact Us</a>
        <a href="terms.php" class="nav-item">Terms</a>
        <div class="nav-icons">
            <a href="cart.php" class="nav-icon"><i class="fas fa-shopping-cart"></i><span class="cart-count"><?php echo htmlspecialchars($cart_count); ?></span></a>
            <div class="user-menu">
                <button class="nav-icon user-icon">
                    <i class="fas fa-user"></i>
                </button>
                <div class="user-dropdown">
                    <p>Username: <?php echo htmlspecialchars($username); ?></p>
                    <p>Email: <?php echo htmlspecialchars($user_email); ?></p>
                    <p>Mobile: <?php echo htmlspecialchars($user_mobile); ?></p>
                    <a href="index.php" class="btn logout-btn">Logout</a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Advanced Search -->
<section class="search-section">
    <form method="GET" action="shop.php">
        <input type="text" name="search" placeholder="Search...">
        <select name="category">
            <option value="">All Categories</option>
            <option value="home">Home</option>
            <option value="kitchen">Kitchen</option>
            <option value="fancy">Fancy</option>
        </select>
        <input type="number" name="min_price" placeholder="Min Price" step="0.01">
        <input type="number" name="max_price" placeholder="Max Price" step="0.01">
        <button type="submit">Search</button>
    </form>
</section>

<!-- Products Display -->
<section class="products-section">
    <div class="products-container">
        <?php
        try {
            // Get the search inputs from the form
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            $category = isset($_GET['category']) ? $_GET['category'] : '';
            $min_price = isset($_GET['min_price']) ? $_GET['min_price'] : 0;
            $max_price = isset($_GET['max_price']) ? $_GET['max_price'] : PHP_INT_MAX;

            // Start building the query
            $query = "SELECT * FROM products WHERE 1=1";

            // Add search term filter
            if (!empty($search)) {
                $query .= " AND product_name LIKE :search";
            }

            // Add category filter
            if (!empty($category)) {
                $query .= " AND category = :category";
            }

            // Add price range filter
            if (!empty($min_price)) {
                $query .= " AND original_price >= :min_price";
            }
            if (!empty($max_price)) {
                $query .= " AND original_price <= :max_price";
            }

            $stmt = $pdo->prepare($query);

            // Bind parameters if values are provided
            if (!empty($search)) {
                $searchTerm = "%$search%";
                $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
            }
            if (!empty($category)) {
                $stmt->bindParam(':category', $category, PDO::PARAM_STR);
            }
            if (!empty($min_price)) {
                $stmt->bindParam(':min_price', $min_price, PDO::PARAM_STR);
            }
            if (!empty($max_price)) {
                $stmt->bindParam(':max_price', $max_price, PDO::PARAM_STR);
            }

            // Execute the query
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Display products
            if ($products) {
                foreach ($products as $row) {
                    echo '<div class="product-item" data-product-id="' . htmlspecialchars($row['product_id']) . '">';
                    echo '<img src="' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['product_name']) . '">';
                    echo '<h3>' . htmlspecialchars($row['product_name']) . '</h3>';
                    echo '<p class="price">';
                    echo '<span class="original-price">Rs ' . number_format($row['original_price'], 2) . '</span>';
                    echo '<span class="discounted-price">Rs ' . number_format($row['discounted_price'], 2) . '</span>';
                    echo '</p>';
                    echo '<div class="product-buttons">';
                    echo '<button class="btn add-to-cart">Add to Cart</button>';
                    echo '<button class="btn buy-now" onclick="window.location.href=\'product.php?id=' . htmlspecialchars($row['product_id']) . '\'">Buy Now</button>';
                    echo '</div>'; // End of product-buttons
                    echo '</div>'; // End of product-item
                }
            } else {
                echo '<p>No products found matching your search criteria.</p>';
            }
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
        ?>
    </div>
</section>

<script src="shop.js"></script>
    
</body>
</html>
