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

// Fetch all orders for the logged-in customer
$stmt = $pdo->prepare("
    SELECT o.id AS order_id, o.restaurant_id, o.total_price, o.order_status, o.created_at, o.address, o.payment_method, 
           r.name AS restaurant_name, r.banner AS restaurant_banner
    FROM orders o
    JOIN restaurants r ON o.restaurant_id = r.id
    WHERE o.customer_id = ?
    ORDER BY o.created_at DESC
");
$stmt->execute([$customer_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="../css/customer_history.css"> <!-- External CSS -->
</head>
<body>
    <!-- Footer -->
    <?php include '../includes/header.php'; ?>
    <div class="container">
        <h1>Your Orders</h1>
        <?php if (count($orders) > 0): ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-banner" style="background-image: url('<?php echo htmlspecialchars($order['restaurant_banner']); ?>');"></div>
                    <div class="order-info">
                        <h2><?php echo htmlspecialchars($order['restaurant_name']); ?></h2>
                        <p><strong>Order ID:</strong> #<?php echo htmlspecialchars($order['order_id']); ?></p>
                        <p><strong>Total:</strong> ₹<?php echo htmlspecialchars($order['total_price']); ?></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
                        <p><strong>Order Date:</strong> <?php echo htmlspecialchars(date("F j, Y, g:i a", strtotime($order['created_at']))); ?></p>
                        <p><strong>Payment:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                    </div>
                    <button class="toggle-details" onclick="toggleDetails(<?php echo htmlspecialchars($order['order_id']); ?>)">View Details</button>
                    
                    <div class="order-details" id="details-<?php echo htmlspecialchars($order['order_id']); ?>" style="display: none;">
                        <h3>Items Ordered:</h3>
                        <ul>
                            <?php
                            $stmt_details = $pdo->prepare("
                                SELECT od.menu_item_id, od.quantity, od.price, mi.name AS menu_item_name
                                FROM order_details od
                                JOIN menu_items mi ON od.menu_item_id = mi.id
                                WHERE od.order_id = ?
                            ");
                            $stmt_details->execute([$order['order_id']]);
                            $order_details = $stmt_details->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($order_details as $detail): ?>
                                <li>
                                    <span><?php echo htmlspecialchars($detail['menu_item_name']); ?></span> 
                                    <span>x<?php echo htmlspecialchars($detail['quantity']); ?></span> 
                                    <span>₹<?php echo htmlspecialchars($detail['price']); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-orders">You have no orders yet.</p>
        <?php endif; ?>
    </div>

    <script src="../js/customer_history.js"></script> <!-- External JavaScript -->
    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>
</body>
</html>
