<?php
include 'db.php'; // Include database connection

$message = ''; // Initialize message variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and trim user input
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password']; // New field for password confirmation
    $profile_image = ''; // Initialize profile image variable

    // Input validation in PHP
    if (strlen($username) < 3 || strlen($username) > 15) {
        $message = "Username must be between 3 and 15 characters.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } elseif (strlen($password) < 10 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $message = "Password must be at least 10 characters long, including numbers, lowercase, and uppercase letters.";
    } elseif ($password !== $confirm_password) { // Check if passwords match
        $message = "Passwords do not match.";
    } else {
        // Check if username or email already exists in the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmt->execute([':username' => $username, ':email' => $email]);
        if ($stmt->rowCount() > 0) {
            $message = "Username or email is already taken.";
        } else {
            // Optional profile image upload
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/'; // Directory for uploads
                $tmp_name = $_FILES['profile_image']['tmp_name']; // Temporary file path
                $name = basename($_FILES['profile_image']['name']); // Original file name
                $profile_image = $upload_dir . uniqid() . '_' . $name; // Create a unique file name
                move_uploaded_file($tmp_name, $profile_image); // Move the uploaded file
            }

            // Hash the password and insert the new user into the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, profile_image) VALUES (:username, :email, :password, :profile_image)");
            $stmt->execute([':username' => $username, ':email' => $email, ':password' => $hashed_password, ':profile_image' => $profile_image]);
            header("Location: login.php"); // Redirect to login page after successful registration
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external stylesheet -->
    <script>
        // Function to show modal with error message
        function showModal(message) {
            document.getElementById('modal-message').textContent = message;
            document.getElementById('error-modal').classList.add('active');
            document.getElementById('overlay').classList.add('active');
        }

        // Function to close the modal
        function closeModal() {
            document.getElementById('error-modal').classList.remove('active');
            document.getElementById('overlay').classList.remove('active');
        }

        // Function to validate the form before submission
        function validateForm() {
            const username = document.getElementById('username').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value; // Get the confirmation password
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Email validation pattern
            const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{10,}$/; // Password validation pattern

            // Validate username length
            if (username.length < 3 || username.length > 15) {
                showModal("Username must be between 3 and 15 characters.");
                return false;
            }
            // Validate email format
            if (!emailPattern.test(email)) {
                showModal("Please enter a valid email address.");
                return false;
            }
            // Validate password format
            if (!passwordPattern.test(password)) {
                showModal("Password must be at least 10 characters, with uppercase, lowercase, and numbers.");
                return false;
            }
            // Check if passwords match
            if (password !== confirmPassword) { 
                showModal("Passwords do not match.");
                return false;
            }
            return true; // Return true if all validations pass
        }
    </script>
</head>
<body>
    <h1>Register</h1>
    <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br> <!-- New field for confirming the password -->

        <label for="profile_image">Profile Image (optional):</label>
        <input type="file" name="profile_image" accept="image/*"><br>

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>

    <!-- Modal for displaying errors -->
    <div id="error-modal" class="modal">
        <div class="modal-header">Error</div>
        <div id="modal-message" class="modal-content"></div>
        <div class="modal-footer"><button onclick="closeModal()">Close</button></div>
    </div>
    <div id="overlay" class="overlay"></div>

    <?php if ($message): ?>
        <script>
            showModal("<?php echo $message; ?>"); // Show modal if there's a message
        </script>
    <?php endif; ?>
</body>
</html>
