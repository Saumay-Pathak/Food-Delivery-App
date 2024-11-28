<?php
// Include database configuration and session start
include('../config.php');
session_start();

// Check if customer is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}

// Fetch customer ID from session
$customer_id = $_SESSION['user_id'];

// Fetch user details from the database
$stmt = $pdo->prepare("SELECT name, email, phone, address FROM users WHERE id = ? AND role = 'customer'");
$stmt->execute([$customer_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission for profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Validate inputs
    if (!empty($name) && !empty($email)) {
        $update_stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
        $update_stmt->execute([$name, $email, $phone, $address, $customer_id]);
        $success_message = "Profile updated successfully!";
        // Refresh user data
        $stmt->execute([$customer_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $error_message = "Name and Email are required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link rel="stylesheet" href="../css/customer_profile.css"> <!-- External CSS -->
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <div class="container">
        <h1>My Profile</h1>

        <!-- Display success or error message -->
        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="profile.php">
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <textarea id="address" name="address" rows="4"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="save-button">Save Changes</button>
        </form>
    </div>
    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>
</body>

</html>