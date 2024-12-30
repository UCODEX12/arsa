<?php
include('db.php'); // Include database connection

// SQL query to fetch cart details
$sql = "SELECT c.id, c.user_id, c.product_id, c.quantity, c.added_at, 
               u.username AS user_name, 
               p.product_name 
        FROM cart c
        JOIN users u ON c.user_id = u.id
        JOIN products p ON c.product_id = p.product_id";

$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    // Output data of each row
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Username</th>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Added At</th>
            </tr>";

    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['user_id']}</td>
                <td>{$row['user_name']}</td>
                <td>{$row['product_id']}</td>
                <td>{$row['product_name']}</td>
                <td>{$row['quantity']}</td>
                <td>{$row['added_at']}</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "0 results";
}

// Close connection
$conn->close();
?>
