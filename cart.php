<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];

try {
    $pdo = new PDO('mysql:host=localhost;dbname=arsa', 'root', 'Sun123flower@');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Initialize variables
    $cart_items = [];
    $total_original_price = 0;
    $total_discounted_price = 0;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['remove'])) {
            $product_id = $_POST['remove'];
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->execute();
        } elseif (isset($_POST['update'])) {
            $product_id = $_POST['product_id'];
            $quantity = $_POST['quantity'];
            $stmt = $pdo->prepare("UPDATE cart SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id");
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->execute();
        }
    }

    // Fetch cart items
    $stmt = $pdo->prepare("SELECT p.product_id, p.product_name, p.original_price, p.discounted_price, p.image_url, c.quantity
                            FROM cart c
                            JOIN products p ON c.product_id = p.product_id
                            WHERE c.user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If cart_items are returned, calculate prices
    if ($cart_items) {
        foreach ($cart_items as $item) {
            $total_original_price += $item['original_price'] * $item['quantity'];
            $total_discounted_price += $item['discounted_price'] * $item['quantity'];
        }
    }

    $amount_to_pay = $total_discounted_price; // Assuming discounted price is the final amount to pay
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- For icons -->
    <style>
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
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            width: 90%;
            max-width: 500px;
        }

        .popup-form label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        .popup-form input,
        .popup-form select {
            width: calc(100% - 20px);
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
        }

        .popup-form button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        .popup-form button:hover {
            background-color: #0056b3;
        }

        .payment-methods {
            margin-bottom: 15px;
        }

        .payment-methods label {
            display: block;
            /* Ensure the label is above the dropdown */
            margin-bottom: 10px;
            /* Space between label and dropdown */
            font-weight: bold;
        }

        .payment-methods select {
            width: 100%;
            /* Full width for the select box */
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            box-sizing: border-box;
            /* Include padding in width calculation */
        }


        @media (max-width: 600px) {
            .payment-methods {
                display: block;
                /* Stack labels vertically on smaller screens */
            }

            .payment-methods label {
                flex-direction: row;
                /* Ensure items are in a row */
                justify-content: flex-start;
                /* Align items to the start */
            }
        }
    </style>
</head>

<body>
    <header>
        <h1>Your Cart</h1>
        <a href="shop.php" class="btn back-to-shopping-btn"><i class="fas fa-arrow-left"></i> Back to Shopping</a>
    </header>

    <section class="cart-section">
        <div class="cart-items">
            <?php if ($cart_items): ?>
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="product-image">
                        <div class="cart-item-details">
                            <h3><?php echo htmlspecialchars($item['product_name']); ?></h3>
                            <p>Original Price: <span class="original-price">Rs <?php echo number_format($item['original_price'], 2); ?></span></p>
                            <p>Discounted Price: <span class="discounted-price">Rs <?php echo number_format($item['discounted_price'], 2); ?></span></p>
                            <form method="POST" class="quantity-form">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['product_id']); ?>">
                                <input type="number" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1">
                                <button type="submit" name="update" class="btn">Update Quantity</button>
                            </form>
                            <form method="POST" class="remove-form">
                                <input type="hidden" name="remove" value="<?php echo htmlspecialchars($item['product_id']); ?>">
                                <button type="submit" class="btn">Remove</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>

        <div class="cart-summary">
            <h2>Order Summary</h2>
            <div class="price-details">
                <p>Total Original Price: <span class="total-original-price">Rs <?php echo number_format($total_original_price, 2); ?></span></p>
                <p>Total Discounted Price: <span class="total-discounted-price">Rs <?php echo number_format($total_discounted_price, 2); ?></span></p>
                <p class="amount-to-pay">Amount to Pay: <strong>Rs <?php echo number_format($amount_to_pay, 2); ?></strong></p>
            </div>
            <button id="checkoutBtn" class="btn checkout-btn">Proceed to Checkout</button>
        </div>
    </section>

    <!-- Popup Form -->
    <div class="popup-overlay" id="popupOverlay"></div>
    <div id="popupForm">
        <form id="deliveryForm" action="save_data.php" method="post" class="popup-form">
            <input type="hidden" id="paymentMethod" name="payment_method" value="">
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
                <!-- Add other options here -->
            </select>
            <label for="paymentMethod">Payment Method:</label>
<select id="paymentMethod" name="payment" required>
    <option value="" disabled selected>Select a payment method</option>
    <option value="cod">Cash on Delivery</option>
    <option value="card">Card Payment</option>
</select>



            <button type="submit">Submit</button>
        </form>
    </div>

    <script>
        function showPopup() {
            document.getElementById('popupForm').style.display = 'block';
            document.getElementById('popupOverlay').style.display = 'block';
        }

        function closePopup() {
            document.getElementById('popupForm').style.display = 'none';
            document.getElementById('popupOverlay').style.display = 'none';
        }

        document.getElementById('checkoutBtn').addEventListener('click', function() {
    showPopup();
});

document.getElementById('popupOverlay').addEventListener('click', function() {
    closePopup();
});

document.getElementById('deliveryForm').addEventListener('submit', function(event) {
    const paymentMethod = document.getElementById('paymentMethod').value;
    
    // Check if a payment method is selected
    if (paymentMethod) {
        if (paymentMethod === 'cod') {
            alert('Your order is processing. Thank you!');
            closePopup();
            // Optionally, you can redirect to a thank you page or perform other actions here
        } else if (paymentMethod === 'card') {
            this.action = 'payment_gateway.php'; // Redirect to payment gateway
        }
    } else {
        event.preventDefault();
        alert('Please select a payment method.');
    }
});

    </script>
</body>

</html>