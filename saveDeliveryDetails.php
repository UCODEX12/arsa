<?php
// Include database connection
include 'db_c.php'; // Ensure this file initializes the $pdo variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Retrieve form data
        $name = $_POST['name'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        $price = $_POST['price'];
        $payment_method = $_POST['payment_method'];

        // Insert order details into database
        $stmt = $pdo->prepare("INSERT INTO orders (name, address, phone, product_id, quantity, price, payment_method, order_date) VALUES (:name, :address, :phone, :product_id, :quantity, :price, :payment_method, NOW())");

        // Bind parameters
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':payment_method', $payment_method);

        // Execute the statement
        $stmt->execute();

        // Success message and redirection
        echo '<script>
                alert("Order placed successfully!");
                window.location.href = "shop.php";
              </script>';
    } catch (PDOException $e) {
        echo '<script>
                alert("Error: ' . $e->getMessage() . '");
                window.location.href = "shop.php";
              </script>';
    }
}
?>
