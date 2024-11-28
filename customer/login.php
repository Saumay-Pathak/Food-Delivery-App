<?php
// Include database configuration
include('../config.php');
session_start();

// Set a timeout period in seconds (15 minutes = 900 seconds)
$timeout_duration = 900;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    // Last request was more than 15 minutes ago
    session_unset(); // Unset session variables
    session_destroy(); // Destroy the session
    header("Location: login.php?message=Session expired, please log in again."); // Redirect to login
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if email and password are entered
    if (!empty($email) && !empty($password)) {
        
        // If the password is numeric, take the absolute value
        if (is_numeric($password)) {
            $password = abs((int)$password);
        }

        // Prepare SQL statement to select customer with provided email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'customer'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verify password by comparing with the password stored in the database
        if ($user && $password == $user['password']) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header('Location: dashboard.php'); // Redirect to customer dashboard
            exit;
        } else {
            $error_message = "Invalid email or password!";
        }
    } else {
        $error_message = "Please fill in all fields!";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login</title>
    <link rel="stylesheet" href="../css/customer_dashboard_login.css"> <!-- Link to external CSS for styling -->
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <!-- Logo, similar to Zomato's login page -->
            <div class="logo-container">
            <img src="../images/logo.webp" alt="Piggy Image">
            </div>
            
            <h2>Login</h2>
            
            <!-- Display error message -->
            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="login.php">
                <div class="input-group" style="position: relative;">
                    <input type="email" name="email" id="email" placeholder="Enter your email" required>
                </div>
                <div class="input-group" style="position: relative;">
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                    <span id="toggle-password" class="toggle-password">&#128065;</span> <!-- Eye icon -->
                </div>
                <button type="submit" class="login-button">Login</button>
            </form>
            
            <div class="links">
                <p>Don't have an account? <a href="register.php">Sign up</a></p>
                <p><a href="reset_password.php">Forgot Password?</a></p>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Toggle password visibility
        document.getElementById('toggle-password').addEventListener('click', function () {
            const passwordField = document.getElementById('password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.textContent = type === 'password' ? '👁️' : '🙈'; // Toggle icon
        });

        // Automatic logout after 15 minutes of inactivity
        let timeout;

        function resetTimer() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                alert("You have been logged out due to inactivity.");
                window.location.href = "logout.php"; // Redirect to logout
            }, 15 * 60 * 1000); // 15 minutes
        }

        window.onload = resetTimer;
        document.onmousemove = resetTimer;
        document.onkeypress = resetTimer;
    </script>
</body>
</html>
