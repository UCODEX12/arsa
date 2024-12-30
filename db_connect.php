<?php
// db_connect.php

$host = 'localhost';
$dbname = 'arsa';
$username = 'root';
$password = 'Sun123flower@';

try {
    // Create a new PDO instance
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection error
    die("Connection failed: " . $e->getMessage());
}


?>
