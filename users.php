<?php
include('db.php'); // Include database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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

        .search-container {
            margin-bottom: 20px;
        }

        .search-container input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 0.875rem;
            box-sizing: border-box;
        }

        .user-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .user-card {
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

        .user-card h3 {
            margin: 10px 0;
            font-size: 1rem;
            font-weight: 600;
        }

        .user-card p {
            font-size: 0.875rem;
            color: #6c757d;
            margin: 5px 0;
        }

        .user-card .btn {
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

        .user-card .btn:hover {
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
        <!-- Search Bar -->
        <div class="search-container">
        <a href="admin.php" class="btn-back">Back to Admin Panel</a>
            <form method="get" action="">
                <input type="text" name="search" placeholder="Search for users..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </form>
        </div>

        <!-- Display Existing Users -->
        <div class="user-container">
            <?php
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            $query = "SELECT * FROM users WHERE username LIKE '%$search%'";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "
                    <div class='user-card'>
                        <h3>{$row['username']}</h3>
                        <p>Email: {$row['email']}</p>
                        <p>mobile: {$row['mobile']}</p>
                        <form action='user_actions.php' method='post' style='display: inline;'>
                            <input type='hidden' name='user_id' value='{$row['id']}'>
                            <button type='submit' name='remove_user' class='btn'>Remove</button>
                        </form>
                    </div>";
                }
            } else {
                echo "<p>No users found</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
