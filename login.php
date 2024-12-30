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

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate inputs
    if (empty($username) || empty($password)) {
        echo "Both fields are required.";
    } else {
        // Check if the username exists in the database
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Verify the password
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Start a session and save user data
                session_start();
                // Save entire user information in the session
                $_SESSION['user'] = [
                    'username' => $user['username'],
                    'id' => $user['id']
                ];
                // Redirect to a dashboard or home page
                header("Location: shop.php");
                exit();
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "No user found with that username.";
        }

        $stmt->close();
    }
}

$conn->close();
?>
