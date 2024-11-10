<?php
session_start(); // Start the session to access session variables
include 'db.php'; // Include the database connection

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch user info from the database using the logged-in user's ID
$stmt = $conn->prepare('SELECT * FROM users WHERE id = :id');
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC); // Get user data as an associative array
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external stylesheet -->
    <title>User Profile</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?></h1> <!-- Display username -->
    <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Image" width="100"> <!-- Display profile image -->
    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p> <!-- Display user email -->
    
    <p>
        <button onclick="window.location.href='change_profile.php';">Edit Profile</button> <!-- Button to edit profile -->
    </p>
    <p>
        <button onclick="window.location.href='logout.php';">Logout</button> <!-- Button to log out -->
    </p>
</body>
</html>
