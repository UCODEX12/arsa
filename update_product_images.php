<?php
include('db.php'); // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle the form submission
    $product_id = $_POST['product_id'];
    $image_files = $_FILES['images'];

    if ($product_id && $image_files) {
        $image_count = count($image_files['name']);

        for ($i = 0; $i < $image_count; $i++) {
            $image_name = basename($image_files['name'][$i]);
            $target_dir = "uploads/";
            $target_file = $target_dir . $image_name;
            $upload_ok = 1;
            $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if image file is an actual image or fake image
            $check = getimagesize($image_files['tmp_name'][$i]);
            if ($check === false) {
                echo "File is not an image.";
                $upload_ok = 0;
            }

            // Check file size (5MB maximum)
            if ($image_files['size'][$i] > 5000000) {
                echo "Sorry, your file is too large.";
                $upload_ok = 0;
            }

            // Allow certain file formats
            if ($image_file_type != "jpg" && $image_file_type != "png" && $image_file_type != "jpeg" && $image_file_type != "gif") {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $upload_ok = 0;
            }

            // Check if $upload_ok is set to 0 by an error
            if ($upload_ok == 0) {
                echo "Sorry, your file was not uploaded.";
            } else {
                // If everything is ok, try to upload file
                if (move_uploaded_file($image_files['tmp_name'][$i], $target_file)) {
                    // Insert image details into database
                    $query = "INSERT INTO product_images (product_id, image_url) VALUES (?, ?)";
                    $stmt = $conn->prepare($query);
                    $image_url = $target_file;
                    $stmt->bind_param("is", $product_id, $image_url);
                    if ($stmt->execute()) {
                        echo "The file ". htmlspecialchars($image_name). " has been uploaded.";
                    } else {
                        echo "Error: " . $stmt->error;
                    }
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        }
    }
}

// Fetch existing images for display
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$existing_images = [];

if ($product_id) {
    $query = "SELECT * FROM product_images WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $existing_images[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product Images</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .container {
            padding: 20px;
        }
        .form-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="file"] {
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 0.875rem;
        }
        .btn-submit {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.875rem;
            transition: background 0.3s;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }
        .image-gallery {
            margin-top: 20px;
        }
        .image-gallery img {
            max-width: 150px;
            height: auto;
            border-radius: 4px;
            margin-right: 10px;
        }
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
    <div class="container">
    <a href="admin.php" class="btn-back">Back to Admin Panel</a>
        <div class="form-container">
            <h3>Update Product Images</h3>
            <form action="update_product_images.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="product_id">Product ID</label>
                    <input type="number" id="product_id" name="product_id" required>
                </div>
                <div class="form-group">
                    <label for="images">Upload Images (multiple files allowed)</label>
                    <input type="file" id="images" name="images[]" multiple required>
                </div>
                <button type="submit" class="btn-submit">Update Images</button>
            </form>

            <?php if (!empty($existing_images)) { ?>
                <div class="image-gallery">
                    <h3>Existing Images</h3>
                    <?php foreach ($existing_images as $image) { ?>
                        <img src="<?php echo htmlspecialchars($image['image_url']); ?>" alt="Product Image">
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>
