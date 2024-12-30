<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user']['id'];
$product_id = json_decode(file_get_contents('php://input'), true)['product_id'];

try {
    $pdo = new PDO('mysql:host=localhost;dbname=arsa', 'root', 'Sun123flower@');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the product is already in the cart
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cart WHERE user_id = :user_id AND product_id = :product_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();
    $exists = $stmt->fetchColumn();

    if ($exists) {
        echo json_encode(['success' => false, 'message' => 'Product already in cart']);
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id) VALUES (:user_id, :product_id)");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();

    $stmt = $pdo->prepare("SELECT COUNT(*) as cart_count FROM cart WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $cart_count = $stmt->fetchColumn();

    echo json_encode(['success' => true, 'cart_count' => $cart_count]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
