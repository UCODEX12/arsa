<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "Sun123flower@";
$dbname = "arsa";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start(); // Start the session

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $password = $_POST['password'];

    // Validate inputs (simple validation)
    if (empty($username) || empty($email) || empty($mobile) || empty($password)) {
        echo "All fields are required.";
    } else {
        // Hash the password before saving it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user data into the database
        $sql = "INSERT INTO users (username, email, mobile, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $mobile, $hashed_password);

        if ($stmt->execute()) {
            // Set session variables
            $_SESSION['user']['username'] = $username;
            $_SESSION['user']['id'] = $conn->insert_id; // Store the user ID

            // Redirect to shop.php
            header("Location: shop.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
}

// Close the connection
$conn->close();
?>
