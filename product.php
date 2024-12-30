<?php
// Include db_connect.php for database connection
include 'db_connect.php';

// Fetch single product details based on the product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    $pdo = new PDO('mysql:host=localhost;dbname=arsa', 'root', 'Sun123flower@');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get product details
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = :product_id");
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch related images for carousel
    $stmt_related = $pdo->prepare("SELECT * FROM product_images WHERE product_id = :product_id");
    $stmt_related->bindParam(':product_id', $product_id);
    $stmt_related->execute();
    $related_images = $stmt_related->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Single Product View</title>
    <link rel="stylesheet" href="shop.css"> <!-- Include your CSS file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css"/> <!-- Swiper CSS -->
    <style>
        /* Layout container */

   /* Navbar */
.navbar {
    background-color: #333; /* Dark background */
    overflow: hidden; /* Clear floats */
    text-align: center;
    padding: 10px 0;
}

.navbar a {
    display: inline-block; /* Horizontally aligned */
    color: white;
    text-align: center;
    padding: 14px 20px;
    text-decoration: none;
    font-size: 1.2rem;
    font-family: 'Poppins', sans-serif;
    margin: 0 10px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.navbar a:hover {
    background-color: #f4b400; /* Hover effect with a highlight color */
    color: black;
}

.navbar a:active {
    background-color: #555; /* Active state color */
    color: white;
}

/* Responsive for mobile */
@media screen and (max-width: 768px) {
    .navbar {
        display: flex;
        flex-direction: column; /* Stack links vertically on smaller screens */
        align-items: center;
    }

    .navbar a {
        width: 100%; /* Make the links full-width */
        margin: 5px 0; /* Space between the stacked links */
    }
}


        .product-layout {
            display: flex;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        /* Left side: Image carousel */
        .product-images {
            flex: 1;
            max-width: 40%;
        }

        .swiper {
            width: 100%;
            height: 400px;
        }

        .swiper-slide {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .swiper-slide img {
            width: 100%;
            max-width: 300px;
            object-fit: contain;
        }

        /* Right side: Product details */
        .product-details {
            flex: 2;
            padding-left: 30px;
        }

        .product-name {
            font-size: 1.8em;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .review-stars {
            color: gold;
            margin-bottom: 10px;
        }

        .price-container {
            margin-bottom: 20px;
        }
        .original-price {
            text-decoration: line-through;
            color: #999;
            font-size: 1.2em;
            margin-right: 10px;
        }
        .discounted-price {
            color: #FF4500;
            font-size: 1.5em;
        }

        /* Product Description */
        .product-description {
            margin-top: 20px;
            font-size: 1.1em;
            line-height: 1.6;
            color: #333;
        }

        /* Quantity and Buttons */
        .quantity-container {
            margin-bottom: 20px;
        }
        .quantity-label {
            font-size: 1.2em;
            margin-right: 10px;
        }
        .quantity-input {
            width: 60px;
            text-align: center;
        }
        .btn {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            margin: 10px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #218838;
        }

        .btn-buy {
            background-color: #FF4500;
            margin-left: 0;
            text-decoration: none;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .product-layout {
                flex-direction: column;
                align-items: center;
            }

            .product-details {
                padding-left: 0;
                text-align: center;
            }

            .swiper {
                height: 300px;
            }

            .product-images img {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<div class="navbar">
    <a href="index.php">Home</a>
    <a href="shop.php">Products</a>
    <a href="cart.php">Cart</a>
</div>

<!-- Product Layout Container -->
<div class="product-layout">
    <!-- Left Side: Image Carousel -->
    <div class="product-images">
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <!-- Main product image -->
                <div class="swiper-slide">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Main Product Image">
                </div>

                <!-- Related product images -->
                <?php foreach ($related_images as $image): ?>
                <div class="swiper-slide">
                    <img src="<?php echo htmlspecialchars($image['image_url']); ?>" alt="Related Image">
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Add Swiper Pagination and Navigation -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination"></div>
        </div>
    </div>

    <!-- Right Side: Product details -->
    <div class="product-details">
        <h1 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h1>

        <!-- Review Stars -->
        <div class="review-stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
        </div>

        <!-- Product Description -->
        <div class="product-description">
            <?php echo nl2br(htmlspecialchars($product['product_description'])); ?>
        </div>
        &nbsp;
        <!-- Prices -->
        <div class="price-container">
            <span class="original-price">Rs. <?php echo number_format($product['original_price'], 2); ?></span>
            <span class="discounted-price">Rs. <?php echo number_format($product['discounted_price'], 2); ?></span>
        </div>


        <!-- Buttons -->
        <!-- Updated Buy Now Button -->
        <a href="purchase.php?id=<?php echo htmlspecialchars($product['product_id']); ?>" class="btn btn-buy">Buy Now</a>

    </div>
</div>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
<script>
    var swiper = new Swiper(".mySwiper", {
        loop: true,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });
</script>
</body>
</html>
