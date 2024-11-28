<?php
// Include database configuration
include('../config.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit;
}

// Retrieve user information from session
$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Check if user data was retrieved
if (!$user) {
    // Redirect to login page if user not found
    header('Location: login.php');
    exit;
}

// Fetch orders for the logged-in user (only orders placed within the last 15 minutes)
$stmt = $pdo->prepare("
    SELECT o.*, r.name as restaurant_name 
    FROM orders o
    LEFT JOIN restaurants r ON o.restaurant_id = r.id
    WHERE o.customer_id = ? AND o.created_at >= NOW() - INTERVAL 15 MINUTE
    ORDER BY o.created_at DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/customer_dashboard.css">
</head>

<body>
    <!-- Header -->
    <?php include '../includes/header.php'; ?>

    <!-- Button to Toggle Sidebar -->
    <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>

    <!-- Sidebar Navigation -->
    <div id="sidebar" class="sidebar">
        <div class="sidebar-profile">
            <img src="https://via.placeholder.com/150" alt="Profile Picture">
            <h3><?php echo htmlspecialchars($user['name']); ?></h3>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="history.php">Order History</a></li>
            <li><a href="profile.php">Profile Settings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content Wrapper -->
    <div id="main-content-wrapper" class="main-content-wrapper">
        <h1>Welcome back, <?php echo htmlspecialchars($user['name']); ?>!</h1>

        <!-- Order History Section -->
        <div class="section order-history">
            <h2>Your Recent Order History</h2>
            <?php if (count($orders) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Restaurant</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Order Date</th>
                            <th>Track Order</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['restaurant_name'] ?? 'Unknown'); ?></td>
                                <td>₹ <?php echo number_format($order['total_price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                                <td><?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>There are no recent orders, or your previous orders have expired.</p>
            <?php endif; ?>
        </div>

        <!-- Restaurant Explore Section -->
        <div class="section explore-restaurants">
            <h2>Explore Restaurants</h2>
            <div class="restaurants-list">
                <?php
                // Fetch restaurants from the database to display
                $stmt = $pdo->prepare("SELECT * FROM restaurants");
                $stmt->execute();
                $restaurants = $stmt->fetchAll();

                foreach ($restaurants as $restaurant):
                    $stars = (float)$restaurant['stars']; // Get star rating as a float
                    $filledStars = floor($stars); // Number of fully filled stars
                    $halfStar = ($stars - $filledStars >= 0.5) ? 1 : 0; // Check if there’s a half star
                    $emptyStars = 5 - ($filledStars + $halfStar); // Remaining empty stars
                ?>
                    <div class="restaurant-card">
                        <!-- Dynamically add ../ if needed to ensure the correct image path -->
                        <img src="<?php echo !empty($restaurant['banner']) ? '../' . ltrim(htmlspecialchars($restaurant['banner']), '\\') : 'https://via.placeholder.com/200'; ?>" alt="Restaurant Image">
                        <div class="restaurant-info">
                            <h3><?php echo htmlspecialchars($restaurant['name']); ?></h3>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($restaurant['address']); ?></p>
                            <!-- Display Stars and Total Reviews -->
                            <div class="ratings">
                                <!-- Dynamic star rendering -->
                                <?php
                                for ($i = 0; $i < $filledStars; $i++) {
                                    echo '<span class="star filled">★</span>';
                                }
                                if ($halfStar) {
                                    echo '<span class="star half">★</span>'; // For half star
                                }
                                for ($i = 0; $i < $emptyStars; $i++) {
                                    echo '<span class="star empty">★</span>';
                                }
                                ?>
                                <span class="reviews">(<?php echo (int)$restaurant['total_reviews']; ?>)</span>
                            </div>
                            <a href="order.php?restaurant_id=<?php echo $restaurant['id']; ?>" class="order-btn">Order Now</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>


    </div>


    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>

    <!-- External JS File -->
    <script src="../js/customer_dashboard.js"></script>
</body>

</html>
