<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
  body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            background-color: #343a40;
            color: #fff;
            width: 220px;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar h2 {
            margin: 0;
            font-size: 1.5rem;
            text-align: center;
        }

        .nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .nav-item {
            margin: 10px 0;
        }

        .nav-link {
            color: #adb5bd;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .nav-link.active,
        .nav-link:hover {
            background-color: #007bff;
            color: #fff;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Admin Panel</h2>
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link" href="#">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="products.php">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="users.php">Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="update_product_images.php">Update Images</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="show_cart.php">Cart</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="show_delivery_details.php">Orders</a>
                </li>
        </nav>

      
    </div>
</body>

</html>
