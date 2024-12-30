<?php
session_start();
session_destroy(); // Destroy all sessions
header("Location: register_login_page.php"); // Redirect to the login page
exit();
?>
