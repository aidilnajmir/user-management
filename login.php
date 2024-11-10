<?php
include 'db.php'; // Include database connection

$message = ''; // Initialize message variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and trim user input
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validate inputs on the server-side
    if (empty($username) || empty($password)) {
        $message = "Username and password are required.";
    } else {
        // Prepare and execute the query to find the user by username
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch user data

        // Verify the password and start a session if valid
        if ($user && password_verify($password, $user['password'])) {
            session_start(); // Start a new session
            $_SESSION['user_id'] = $user['id']; // Store user ID in session
            $_SESSION['username'] = $user['username']; // Store username in session
            header("Location: profile.php"); // Redirect to the user's profile page
            exit;
        } else {
            $message = "Invalid username or password."; // Set error message for invalid login
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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
            const password = document.getElementById('password').value;

            // Check if both fields are filled
            if (!username || !password) {
                showModal("Both username and password are required.");
                return false; // Prevent form submission
            }
            return true; // Return true if all validations pass
        }
    </script>
</head>
<body>
    <h1>Login</h1>
    <form method="POST" onsubmit="return validateForm()">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>

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
