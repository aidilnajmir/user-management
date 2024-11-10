<?php
session_start(); // Start the session
session_destroy(); // Destroy all data registered to a session
header('Location: login.php'); // Redirect to the login page
exit; // Terminate the script to ensure no further code is executed
?>
