<?php
include('db.php'); // Include database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <style>
        /* Include your CSS styles here */
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
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
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

        .product-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: calc(33.333% - 10px);
            box-sizing: border-box;
            padding: 15px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .card img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .card h3 {
            margin: 10px 0;
            font-size: 1rem;
            font-weight: 600;
        }

        .card p {
            font-size: 0.875rem;
            color: #6c757d;
            margin: 5px 0;
        }

        .card .btn {
            background-color: #dc3545;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 8px 12px;
            cursor: pointer;
            font-size: 0.75rem;
            transition: background 0.3s;
            position: absolute;
            bottom: 15px;
            right: 15px;
        }

        .card .btn:hover {
            background-color: #c82333;
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
        <!-- Add Product Form -->
        <div class="form-container">
            <h3>Add New Product</h3>
            <form action="product_actions.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="product_name">Product Name</label>
                    <input type="text" id="product_name" name="product_name" required>
                </div>
                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label for="original_price">Original Price</label>
                    <input type="number" id="original_price" name="original_price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="discounted_price">Discounted Price</label>
                    <input type="number" id="discounted_price" name="discounted_price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                    <input type="text" id="category" name="category">
                </div>
                <div class="form-group">
                    <label for="product_description">Description</label>
                    <textarea id="product_description" name="product_description" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label for="qty">Quantity</label>
                    <input type="number" id="qty" name="qty">
                </div>
                <button type="submit" name="add_product" class="btn-submit">Add Product</button>
            </form>
        </div>

        <!-- Search Bar -->
        <div class="search-container">
            <form method="get" action="">
                <input type="text" name="search" placeholder="Search for products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </form>
        </div>

        <!-- Display Existing Products -->
        <div class="product-container">
            <?php
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            $query = "SELECT * FROM products WHERE product_name LIKE '%$search%'";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "
                    <div class='card'>
                        <img src='{$row['image_url']}' alt='{$row['product_name']}'>
                        <h3>{$row['product_name']}</h3>
                        <p>Original Price: \${$row['original_price']}</p>
                        <p>Discounted Price: \${$row['discounted_price']}</p>
                        <p>Category: {$row['category']}</p>
                        <p>Description: {$row['product_description']}</p>
                        <p>Quantity: {$row['qty']}</p>
                        <form action='product_actions.php' method='post' style='display: inline;'>
                            <input type='hidden' name='product_id' value='{$row['product_id']}'>
                            <button type='submit' name='remove_product' class='btn'>Remove</button>
                        </form>
                    </div>";
                }
            } else {
                echo "<p>No products found</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
