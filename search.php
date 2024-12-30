<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "Sun123flower@";
$dbname = "arsa";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = isset($_GET['query']) ? $_GET['query'] : '';
$sql = "SELECT * FROM products WHERE product_name LIKE ?";

$stmt = $conn->prepare($sql);
$searchTerm = "%$query%";
$stmt->bind_param('s', $searchTerm);
$stmt->execute();

$result = $stmt->get_result();
$products = array();

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode($products);

$stmt->close();
$conn->close();
?>
