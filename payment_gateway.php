<?php
require 'stripe-php-master/init.php'; // Adjust path if needed

// Your Stripe secret key
\Stripe\Stripe::setApiKey('sk_test_51Pz3cY2L31FnvvEbvXGdpDJNh2QNYb4iSE68S0dEMsETg8Zo8kqsQOK2nQoqrq03c2avQc9fVK1OzHYqXF726PL700NbzzLAV9');

// Create the PaymentIntent when the page loads
$intent = \Stripe\PaymentIntent::create([
    'amount' => 5000, // Amount in cents (e.g., 5000 = $50.00)
    'currency' => 'usd', // Set your currency here
    'automatic_payment_methods' => [
        'enabled' => true,
    ],
]);

$clientSecret = $intent->client_secret;

// Include database connection
include 'db.php'; // Ensure this path is correct

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $delivery_method = $_POST['delivery_method'];

    // Save form data to database
    $sql = "INSERT INTO delivery_details (name, address, phone, city, delivery_method)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssss", $name, $address, $phone, $city, $delivery_method);
        
        if ($stmt->execute()) {
           
            // Here you can redirect to the actual payment gateway
            // header("Location: process_payment.php");
        } else {
            
        }

        $stmt->close();
    } else {
        
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Payment Gateway</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 500px;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 1rem;
            color: #333;
            text-align: center;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #333;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 5px;
            background: #f9f9f9;
            box-sizing: border-box;
        }
        #card-element {
            border: 1px solid #ced4da;
            border-radius: 5px;
            padding: 0.75rem;
            background: #f9f9f9;
            margin-top: 0.5rem;
        }
        #card-errors {
            color: #e74c3c;
            margin-top: 0.5rem;
        }
        button {
            background-color: #ffc107; /* Yellow color */
            border: none;
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 1rem;
        }
        button:disabled {
            background-color: #c0c0c0;
        }
    </style>
    <script src="https://js.stripe.com/v3/"></script> <!-- Stripe.js -->
</head>
<body>
    <div class="container">
        <h1>Payment Information</h1>
        <!-- Payment Form -->
        <form id="payment-form">
            <div class="form-group">
                <label for="name">Cardholder's Name</label>
                <input type="text" id="name" placeholder="John Doe" required>
            </div>
            <div class="form-group">
                <label for="card-element">Credit Card Details</label>
                <div id="card-element">
                    <!-- Stripe.js will insert the card element here -->
                </div>
                <div id="card-errors" role="alert"></div>
            </div>
            <a href="purchase.php?id=<?php echo htmlspecialchars($product['product_id']); ?>" id="submit-button">
  <button type="submit">Pay</button>
</a>

        </form>
    </div>

    <script>
        // Initialize Stripe.js with your publishable key
        var stripe = Stripe('pk_test_51Pz3cY2L31FnvvEbTHIiiFryLjwZFxHay0v8E0CDVClrcB6Owul4XvEkUzjeyc404aDbbvq912QnDCtQ7SddmQSv00woXsBVcP'); // Replace with your Stripe test publishable key
        var elements = stripe.elements();

        // Create an instance of the card Element
        var card = elements.create('card', {
            hidePostalCode: true, // Hide the postal code input field if not needed
            style: {
                base: {
                    color: '#333',
                    fontSize: '16px',
                    fontFamily: '"Arial", sans-serif',
                    '::placeholder': {
                        color: '#888'
                    }
                },
                invalid: {
                    color: '#e74c3c',
                    iconColor: '#e74c3c'
                }
            }
        });
        card.mount('#card-element');

        // Handle real-time validation errors from the card Element
        card.on('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Handle form submission
        var form = document.getElementById('payment-form');

        form.addEventListener('submit', function(event) {
            event.preventDefault();

            // Disable the button to prevent multiple clicks
            var submitButton = document.getElementById('submit-button');
            submitButton.disabled = true;

            // Confirm the card payment with the clientSecret from the server
            stripe.confirmCardPayment("<?php echo $clientSecret; ?>", {
                payment_method: {
                    card: card,
                    billing_details: {
                        name: document.getElementById('name').value,
                    }
                }
            }).then(function(result) {
                if (result.error) {
                    // Show error to the user
                    document.getElementById('card-errors').textContent = result.error.message;
                    submitButton.disabled = false; // Re-enable the button if there was an error
                } else {
                    // The payment succeeded!
                    if (result.paymentIntent.status === 'succeeded') {
                        alert('Payment successful!');
                        // Optionally, redirect to a success page or perform any post-payment action
                        window.location.href = "index.php"; // Replace with your success page URL
                    }
                }
            });
        });
    </script>
</body>
</html>
