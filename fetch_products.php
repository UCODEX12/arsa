<?php
// Include the database connection
include 'db_connect.php';

// Initialize the products variable
$products = array();
$searchTerm = isset($_POST['searchTerm']) ? $_POST['searchTerm'] : '';
$limit = 12; // Number of products to load per request

try {
    // If a search term is provided, fetch matching products
    if (!empty($searchTerm)) {
        $query = "SELECT * FROM products WHERE product_name LIKE :searchTerm LIMIT :limit";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%', PDO::PARAM_STR);
    } else {
        // Fetch all products if no search term is provided
        $query = "SELECT * FROM products LIMIT :limit";
        $stmt = $conn->prepare($query);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($products);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
