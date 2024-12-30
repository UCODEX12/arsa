<?php
include('db.php'); // Include database connection

// Handle search request
$search = isset($_POST['search']) ? $_POST['search'] : '';

// SQL query to fetch delivery details
if (!empty($search)) {
    $sql = "SELECT * FROM delivery_details WHERE name LIKE ? OR address LIKE ? OR phone LIKE ? OR city LIKE ? OR delivery_method LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchParam = "%{$search}%";
    $stmt->bind_param('sssss', $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // If no search query, fetch all delivery details
    $sql = "SELECT * FROM delivery_details";
    $result = $conn->query($sql);
}

// Handle printing a specific card
if (isset($_GET['print_id'])) {
    $printId = intval($_GET['print_id']);
    $printSql = "SELECT * FROM delivery_details WHERE id = ?";
    $printStmt = $conn->prepare($printSql);
    $printStmt->bind_param('i', $printId);
    $printStmt->execute();
    $printResult = $printStmt->get_result()->fetch_assoc();
    $printStmt->close();

    if ($printResult) {
        echo "<html><body>
                <h1>Delivery Details</h1>
                <p><strong>Name:</strong> {$printResult['name']}</p>
                <p><strong>Address:</strong> {$printResult['address']}</p>
                <p><strong>Phone:</strong> {$printResult['phone']}</p>
                <p><strong>City:</strong> {$printResult['city']}</p>
                <p><strong>Delivery Method:</strong> {$printResult['delivery_method']}</p>
                <button onclick='window.print()'>Print</button>
                <a href='show_delivery_details.php'>Back</a>
                </body></html>";
        exit;
    }
}

// Close the statement
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Details</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .card { border: 1px solid #ddd; border-radius: 5px; padding: 16px; margin: 16px 0; }
        .card h3 { margin-top: 0; }
        .search-bar { margin: 20px 0; }
        .search-bar input { padding: 8px; width: 300px; }
        .search-bar button { padding: 8px 16px; }
        .print-btn { margin: 5px; }
        .btn-back {
            display: inline-block;
            margin: 15px 0;
            padding: 10px 15px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 0.875rem;
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn-back:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="search-bar">
<a href="admin.php" class="btn-back">Back to Admin Panel</a>
    <form method="POST">
        <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>
</div>

<?php
// Display delivery details in card format
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='card'>
                <h3>Delivery ID: {$row['id']}</h3>
                <p><strong>Name:</strong> {$row['name']}</p>
                <p><strong>Address:</strong> {$row['address']}</p>
                <p><strong>Phone:</strong> {$row['phone']}</p>
                <p><strong>City:</strong> {$row['city']}</p>
                <p><strong>Delivery Method:</strong> {$row['delivery_method']}</p>
                <a href='show_delivery_details.php?print_id={$row['id']}' class='print-btn'>Print</a>
            </div>";
    }
} else {
    echo "<p>No results found.</p>";
}
?>

</body>
</html>
