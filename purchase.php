<?php
// Include db_connect.php for database connection
include 'db_connect.php';

try {
    $pdo = new PDO('mysql:host=localhost;dbname=arsa', 'root', 'Sun123flower@');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch and validate product ID from URL
    $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($product_id <= 0) {
        throw new Exception('Invalid product ID.');
    }

    // Get product details
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = :product_id");
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        throw new Exception('Product not found.');
    }

    // Fetch related images for carousel
    $stmt_related = $pdo->prepare("SELECT * FROM product_images WHERE product_id = :product_id");
    $stmt_related->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt_related->execute();
    $related_images = $stmt_related->fetchAll(PDO::FETCH_ASSOC);

    // Calculate prices
    $original_price = $product['original_price'];
    $discounted_price = $product['discounted_price'];
    $delivery_charges = 400;
    $total_price = $discounted_price + $delivery_charges;
} catch (PDOException $e) {
    echo 'Database Error: ' . htmlspecialchars($e->getMessage());
    exit;
} catch (Exception $e) {
    echo 'Error: ' . htmlspecialchars($e->getMessage());
    exit;
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase</title>
    <link rel="stylesheet" href="checkout.css"> <!-- Include your CSS file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" /> <!-- Swiper CSS -->
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .navbar {
            background-color: #333;
            /* Dark background */
            overflow: hidden;
            /* Clear floats */
            text-align: center;
            padding: 10px 0;
        }

        .navbar a {
            display: inline-block;
            /* Horizontally aligned */
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
            background-color: #f4b400;
            /* Hover effect with a highlight color */
            color: black;
        }

        .navbar a:active {
            background-color: #555;
            /* Active state color */
            color: white;
        }

        /* Product Layout Container */
        .product-layout {
            display: flex;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        /* Left Side: Image Carousel */
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

        /* Right Side: Product Details */
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

        .details-container div,
        .payment-method-container {
            margin-bottom: 15px;
            /* Adds consistent spacing between each section */
        }


        .btn {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-align: center;
            display: inline-block;
        }

        .btn:hover {
            background-color: #218838;
        }

        .btn-buy {
            background-color: #FF4500;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            padding: 10px 20px;
        }

        .btn-buy:hover {
            background-color: #e03c31;
        }

        



        /* Highlighted sections for delivery charges and total */
        .highlighted {
            background-color: #fefcbf;
            /* Light yellow background */
            border: 2px solid #f8e6a1;
            /* Light yellow border */
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .highlighted i {
            color: #f8c21e;
            /* Matching icon color */
        }

        /* Popup form style */

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    padding-top: 100px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: #fff;
    margin: auto;
    padding: 20px;
    border-radius: 10px;
    width: 80%;
    max-width: 600px;
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

  /* Popup form style */
  .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        #popupForm {
            display: none;
            position: absolute;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 500px;
            background-color: white;
            border: 1px solid #ccc;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
            border-radius: 8px;
            z-index: 1000;
        }

        .popup-form input,
        .popup-form select {
            width: calc(100% - 16px);
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .popup-form button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            font-size: 1em;
        }

        .popup-form button:hover {
            background-color: #0056b3;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .product-layout {
                flex-direction: column;
            }

            .product-images,
            .product-details {
                max-width: 100%;
            }

            .product-details {
                padding-left: 0;
                padding-top: 20px;
            }

            .swiper-slide img {
                max-width: 100%;
            }

            #popupForm {
                width: 90%;
                top: 80px;
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
                    <div class="swiper-slide">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Main Product Image">
                    </div>

                    <?php foreach ($related_images as $image): ?>
                        <div class="swiper-slide">
                            <img src="<?php echo htmlspecialchars($image['image_url']); ?>" alt="Related Image">
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Swiper Pagination and Navigation -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            </div>
        </div>

        <!-- Right Side: Product Details -->
        <div class="product-details">
            <h1 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h1>

            <div class="review-stars">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star-half-alt"></i>
            </div>

            <div class="price-container">
                <span class="original-price">Rs. <?php echo number_format($product['original_price'], 2); ?></span>
                <span class="discounted-price">Rs. <?php echo number_format($product['discounted_price'], 2); ?></span>
            </div>
            <div class="quantity-container">
        <label for="quantity">Quantity:</label>
        <button type="button" id="decrease-quantity">-</button>
        <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo htmlspecialchars($product['qty']); ?>">
        <button type="button" id="increase-quantity">+</button>
    </div>
    &nbsp;
            <div class="details-container">
                <div>
                    <i class="fas fa-palette"></i>
                    <span>Color: Null</span>
                </div>
                <div class="highlighted">
                    <i class="fas fa-truck"></i>
                    <span>Delivery Charges: Rs. <?php echo number_format($delivery_charges, 2); ?></span>
                </div>
                <div class="highlighted">
                    <i class="fas fa-calculator"></i>
                    <span>Total: Rs. <?php echo number_format($total_price, 2); ?></span>
                </div>
            </div>
            &nbsp;
            <h2>Checkout Page</h2>

<!-- Payment Options -->
<label>
    <input type="radio" name="payment" value="COD" onclick="showPopup('COD')"> Cash on Delivery
</label>
<label>
    <input type="radio" name="payment" value="Card" onclick="showPopup('Card')"> Card Payment
</label>

<!-- Popup Form for both Cash on Delivery and Card Payment -->
<div class="popup-overlay"></div>
<div id="popupForm">
    <form id="deliveryForm" action="saveDeliveryDetails.php" method="post" class="popup-form">
    <input type="hidden" id="paymentMethod" name="payment_method" value="">
    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
    <input type="hidden" name="quantity" id="quantity-hidden" value="1"> <!-- Captured from the quantity input -->
    <input type="hidden" name="price" value="<?php echo $total_price; ?>">

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="address">Address:</label>
        <input type="text" id="address" name="address" required>

        <label for="phone">Phone Number:</label>
        <input type="tel" id="phone" name="phone" required pattern="[0-9]{10}">
        
        <label for="city">District:</label>
        <select id="city" name="city" required>
    <option value="">Select your district</option>
    <option value="Ampara">Ampara</option>
    <option value="Anuradhapura">Anuradhapura</option>
    <option value="Badulla">Badulla</option>
    <option value="Batticaloa">Batticaloa</option>
    <option value="Colombo">Colombo</option>
    <option value="Galle">Galle</option>
    <option value="Gampaha">Gampaha</option>
    <option value="Hambantota">Hambantota</option>
    <option value="Jaffna">Jaffna</option>
    <option value="Kalutara">Kalutara</option>
    <option value="Kandy">Kandy</option>
    <option value="Kegalle">Kegalle</option>
    <option value="Kilinochchi">Kilinochchi</option>
    <option value="Kurunegala">Kurunegala</option>
    <option value="Mannar">Mannar</option>
    <option value="Matale">Matale</option>
    <option value="Matara">Matara</option>
    <option value="Moneragala">Moneragala</option>
    <option value="Mullaitivu">Mullaitivu</option>
    <option value="Nuwara Eliya">Nuwara Eliya</option>
    <option value="Polonnaruwa">Polonnaruwa</option>
    <option value="Puttalam">Puttalam</option>
    <option value="Ratnapura">Ratnapura</option>
    <option value="Trincomalee">Trincomalee</option>
    <option value="Vavuniya">Vavuniya</option>
</select>
 <button type="submit">Submit</button>
    </form>
</div>

<script>
    // Hide popup after form submission
    document.getElementById('deliveryForm').addEventListener('submit', function() {
        hidePopup();
    });
</script>


    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script>
    // Swiper initialization
    const swiper = new Swiper('.mySwiper', {
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        loop: true
    });

    // Show popup form when payment method is selected
    function showPopup(paymentMethod) {
        document.getElementById('paymentMethod').value = paymentMethod;

        // Change the form action based on payment method
        if (paymentMethod === 'COD') {
            document.getElementById('deliveryForm').action = 'saveDeliveryDetails.php';
        } else if (paymentMethod === 'Card') {
            document.getElementById('deliveryForm').action = 'payment_gateway.php';
        }

        document.querySelector('.popup-overlay').style.display = 'block';
        document.getElementById('popupForm').style.display = 'block';
    }

    function hidePopup() {
        document.querySelector('.popup-overlay').style.display = 'none';
        document.getElementById('popupForm').style.display = 'none';
    }

    // Quantity and Price Calculation
    const discountedPrice = parseFloat(document.querySelector('.discounted-price').textContent.replace('Rs. ', '').replace(',', ''));
    const deliveryCharges = parseFloat(document.querySelector('.highlighted span').textContent.replace('Delivery Charges: Rs. ', '').replace(',', ''));
    const quantityInput = document.getElementById('quantity');
    const totalPriceElement = document.querySelector('.highlighted:nth-of-type(2) span');

    function updateTotalPrice() {
        const quantity = parseInt(quantityInput.value);
        const totalPrice = (discountedPrice * quantity) + deliveryCharges;
        totalPriceElement.textContent = `Total: Rs. ${totalPrice.toFixed(2)}`;
    }

    document.getElementById('increase-quantity').addEventListener('click', function() {
        let quantity = parseInt(quantityInput.value);
        if (quantity < quantityInput.max) {
            quantityInput.value = quantity + 1;
            updateTotalPrice();
        }
    });

    document.getElementById('decrease-quantity').addEventListener('click', function() {
        let quantity = parseInt(quantityInput.value);
        if (quantity > 1) {
            quantityInput.value = quantity - 1;
            updateTotalPrice();
        }
    });

    quantityInput.addEventListener('input', function() {
        if (quantityInput.value > quantityInput.max) {
            quantityInput.value = quantityInput.max;
        }
        updateTotalPrice();
    });

    // Hide popup after form submission
    document.getElementById('deliveryForm').addEventListener('submit', function() {
        hidePopup();
    });
</script>

</body>
</html>
