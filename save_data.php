<?php
include 'db_c.php';
session_start();

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user']['id'];
$payment_method = $_POST['delivery_method'];
$name = $_POST['name'];
$address = $_POST['address'];
$phone = $_POST['phone'];
$city = $_POST['city'];

try {
    $pdo = new PDO('mysql:host=localhost;dbname=arsa', 'root', 'Sun123flower@');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insert order details
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, payment_method, name, address, phone, city, order_date) 
                            VALUES (:user_id, :payment_method, :name, :address, :phone, :city, NOW())");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':payment_method', $payment_method);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':city', $city);
    $stmt->execute();
    
    $order_id = $pdo->lastInsertId();

    // Fetch cart items
    $stmt = $pdo->prepare("SELECT p.product_id, p.product_name, p.discounted_price, c.quantity
                            FROM cart c
                            JOIN products p ON c.product_id = p.product_id
                            WHERE c.user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Insert order items
    foreach ($cart_items as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity)
                                VALUES (:order_id, :product_id, :product_name, :price, :quantity)");
        $stmt->bindParam(':order_id', $order_id);
        $stmt->bindParam(':product_id', $item['product_id']);
        $stmt->bindParam(':product_name', $item['product_name']);
        $stmt->bindParam(':price', $item['discounted_price']);
        $stmt->bindParam(':quantity', $item['quantity']);
        $stmt->execute();
    }

    // Clear cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
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

?>
