<?php
include('db.php');

$message = '';

if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $original_price = $_POST['original_price'];
    $discounted_price = $_POST['discounted_price'];
    $category = $_POST['category'];
    $product_description = $_POST['product_description'];
    $qty = $_POST['qty'];

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['image']['tmp_name'];
        $file_name = basename($_FILES['image']['name']);
        $file_path = 'uploads/' . $file_name;

        // Move the uploaded file to the 'uploads' directory
        if (move_uploaded_file($file_tmp_name, $file_path)) {
            $image_url = $file_path;
        } else {
            $message = "Failed to move uploaded file.";
            $image_url = ''; // or handle the error as needed
        }
    } else {
        $message = "No file uploaded or there was an upload error.";
        $image_url = ''; // or handle the error as needed
    }

    $query = "INSERT INTO products (product_name, image_url, original_price, discounted_price, category, product_description, qty)
              VALUES ('$product_name', '$image_url', '$original_price', '$discounted_price', '$category', '$product_description', '$qty')";

    if ($conn->query($query) === TRUE) {
        $message = "New product added successfully";
    } else {
        $message = "Error: " . $conn->error;
    }
}

if (isset($_POST['remove_product'])) {
    $product_id = $_POST['product_id'];

    $query = "DELETE FROM products WHERE product_id = '$product_id'";

    if ($conn->query($query) === TRUE) {
        $message = "Product removed successfully";
    } else {
        $message = "Error: " . $conn->error;
    }
}

if ($message) {
    echo "<script>alert('$message'); window.location.href = 'admin.php';</script>";
}
?>
