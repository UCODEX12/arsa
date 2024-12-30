<?php
session_start();

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

$total_amount = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['products']) && is_array($_POST['products'])) {
        $products = $_POST['products'];

        foreach ($products as $product) {
            // Assuming product array keys are 'price' and 'quantity'
            if (isset($product['price']) && isset($product['quantity'])) {
                $total_amount += $product['price'] * $product['quantity'];
            }
        }
    } else {
        // Handle case where 'products' is not set or not an array
        // You might want to log this or handle it gracefully
    }
} else {
    // Redirect to cart if accessed directly
    header('Location: cart.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="checkout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- For icons -->
    <style>
        /* checkout.css */
        body {
            font-family: Arial, sans-serif;
        }

        header {
            text-align: center;
            margin: 20px 0;
        }

        .checkout-section {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .checkout-items {
            width: 80%;
            margin: 0 auto;
        }

        .checkout-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 20px;
        }

        .checkout-item-details {
            flex: 1;
        }

        .price {
            font-weight: bold;
        }

        .quantity {
            font-style: italic;
        }

        .confirm-order-btn {
            background-color: #28a745;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }

        .confirm-order-btn:hover {
            background-color: #218838;
        }

        .checkout-summary {
            margin-top: 20px;
            text-align: center;
        }

        .total-amount {
            font-size: 1.5em;
            font-weight: bold;
            color: #fff;
            background-color: burlywood;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin: 10px 0;
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

        .payment-options {
            margin-top: 20px;
        }

        .payment-options label {
            display: block;
            margin-bottom: 10px;
        }

        .space-between {
            margin-top: 30px; /* Adjust space as needed */
        }

        /* Hidden payment options */
        .payment-options {
            display: none;
            margin-top: 20px;
        }

        .payment-options label {
            display: block;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Checkout</h1>
    </header>

    <section class="checkout-section">
        <div class="checkout-items">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                <div class="checkout-item">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                    <div class="checkout-item-details">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p>Price: <span class="price">Rs <?php echo number_format($product['price'], 2); ?></span></p>
                        <p>Quantity: <span class="quantity"><?php echo htmlspecialchars($product['quantity']); ?></span></p>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No items found.</p>
            <?php endif; ?>
        </div>

        <div class="checkout-summary">
            <p class="total-amount">Total Amount: Rs <?php echo number_format($total_amount, 2); ?></p>
            <div id="paymentOptions" class="payment-options">
                <label>
                    <input type="radio" name="payment" value="COD" onclick="showPopup('COD')"> Cash on Delivery
                </label>
                <label>
                    <input type="radio" name="payment" value="Card" onclick="showPopup('Card')"> Card Payment
                </label>
            </div>
            <div class="space-between">
            <button id="confirmOrderBtn" class="confirm-order-btn">Confirm Order</button>
            </div>
        </div>
    </section>

    <!-- Popup Form for both Cash on Delivery and Card Payment -->
    <div class="popup-overlay"></div>
    <div id="popupForm">
        <form id="deliveryForm" action="save_data.php" method="post" class="popup-form">
            <input type="hidden" id="paymentMethod" name="delivery_method" value="">
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

        // Hide popup after form submission
        document.getElementById('deliveryForm').addEventListener('submit', function () {
            document.querySelector('.popup-overlay').style.display = 'none';
            document.getElementById('popupForm').style.display = 'none';
        });

        // Hide popup when overlay is clicked
        document.querySelector('.popup-overlay').addEventListener('click', function () {
            document.querySelector('.popup-overlay').style.display = 'none';
            document.getElementById('popupForm').style.display = 'none';
        });
              // Show payment options when Confirm Order button is clicked
              document.getElementById('confirmOrderBtn').addEventListener('click', function () {
            document.getElementById('paymentOptions').style.display = 'block';
        });
    </script>
</body>
</html>
