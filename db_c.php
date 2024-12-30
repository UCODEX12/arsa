
<?php
// Database connection details
$host = 'localhost';  // Your database host
$dbname = 'arsa';  // Your database name
$username = 'root';  // Your database username
$password = 'Sun123flower@';  // Your database password

try {
    // Create a new PDO instance and store it in the $pdo variable
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set PDO error mode to exception to handle errors properly
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If there is an error, output the message
    die("Connection failed: " . $e->getMessage());
}
?>
