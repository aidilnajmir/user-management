<?php
session_start(); // Start the session
include 'db.php'; // Include the database connection file

// Redirect to login page if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = ''; // Initialize message variable
$user_id = $_SESSION['user_id']; // Get the logged-in user's ID
$hashed_password = null; // Initialize the hashed password variable

// Fetch user info
$stmt = $conn->prepare('SELECT * FROM users WHERE id = :id');
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC); // Get user data as an associative array

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? null; // Current password input
    $new_password = $_POST['new_password'] ?? null; // New password input
    $confirm_password = $_POST['confirm_password'] ?? null; // Confirm password input

    // Validate password complexity
    if (strlen($new_password) < 10 || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[a-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        $message = "Password must be at least 10 characters long, including numbers, lowercase, and uppercase letters.";
    } elseif ($new_password !== $confirm_password) {
        $message = "New password does not match the confirmation.";
    } elseif (empty($current_password)) {
        $message = "Current password is missing.";
    } elseif (!password_verify($current_password, $user['password'])) {
        $message = "Current password does not match.";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in the database
        $stmt = $conn->prepare('UPDATE users SET password = :password WHERE id = :id');
        if ($stmt->execute([':password' => $hashed_password, ':id' => $user_id])) {
            $message = "Password updated successfully.";
        } else {
            $message = "Failed to update password. Please try again.";
        }
    }
}

// Handle profile image change
if (isset($_POST['change_image'])) {
    $profile_image = '';

    // Check if a new profile image is uploaded
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/'; // Directory to store uploaded images
        $tmp_name = $_FILES['profile_image']['tmp_name']; // Temporary file name
        $name = basename($_FILES['profile_image']['name']); // Original file name
        $profile_image = $upload_dir . uniqid() . '_' . $name; // Create unique file path
        move_uploaded_file($tmp_name, $profile_image); // Move uploaded file to the specified directory

        // Update image in the database
        $stmt = $conn->prepare('UPDATE users SET profile_image = :profile_image WHERE id = :id');
        if ($stmt->execute([':profile_image' => $profile_image, ':id' => $user_id])) {
            $message = "Profile image updated successfully.";
        } else {
            $message = "Failed to update profile image. Please try again.";
        }
    } else {
        $message = "No image uploaded or an error occurred.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external stylesheet -->
    <title>Change Profile</title>
    <style>
        /* Add your existing modal styles here */
    </style>
</head>
<body>
    <h1>Edit Profile</h1>

    <!-- Modal for displaying messages -->
    <div id="error-modal" class="modal" style="display: <?php echo !empty($message) ? 'block' : 'none'; ?>">
        <div class="modal-header">Message</div>
        <div id="modal-message" class="modal-content"><?php echo htmlspecialchars($message); ?></div>
        <div class="modal-footer"><button onclick="closeModal()">Close</button></div>
    </div>
    <div id="overlay" class="overlay" style="display: <?php echo !empty($message) ? 'block' : 'none'; ?>"></div>

    <!-- Change Password Form -->
    <form method="POST">
        <h2>Change Password</h2>
        <label for="current_password">Current Password:</label>
        <input type="password" id="current_password" name="current_password" required>

        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required>

        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit" name="change_password">Update Password</button>
    </form>

    <!-- Change Profile Image Form -->
    <form method="POST" enctype="multipart/form-data">
        <h2>Change Profile Image</h2>
        <label for="profile_image">Profile Image:</label>
        <input type="file" name="profile_image" accept="image/*" required>
        
        <button type="submit" name="change_image">Update Image</button>
    </form>

    <p>
        <button onclick="window.location.href='profile.php';">Go Back to Profile</button>
    </p>

    <script>
        // Function to close the modal
        function closeModal() {
            document.getElementById('error-modal').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }
    </script>
</body>
</html>
