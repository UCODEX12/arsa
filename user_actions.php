<?php
include('db.php');

if (isset($_POST['remove_user'])) {
    $user_id = $_POST['user_id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        header('Location: adminpanel.php#users-section&message=User removed successfully');
    } else {
        header('Location: adminpanel.php#users-section&message=Failed to remove user');
    }

    $stmt->close();
}

$conn->close();
?>
