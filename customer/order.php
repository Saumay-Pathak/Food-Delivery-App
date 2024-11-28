<?php
// Include database configuration
include('../config.php');
session_start();

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get the restaurant ID from the query string
$restaurant_id = $_GET['restaurant_id'] ?? null;

if (!$restaurant_id) {
    die("Invalid restaurant ID.");
}

// Fetch the restaurant details
$stmt = $pdo->prepare("SELECT name, address, stars, banner FROM restaurants WHERE id = ?");
$stmt->execute([$restaurant_id]);
$restaurant = $stmt->fetch();

if (!$restaurant) {
    die("Restaurant not found.");
}

// Fetch menu items for the restaurant
$stmt = $pdo->prepare("SELECT * FROM menu_items WHERE restaurant_id = ?");
$stmt->execute([$restaurant_id]);
$menu_items = $stmt->fetchAll();

// Function to dynamically adjust the image path
function getCorrectedPath($path) {
    $adjustedPath = $path;

    // Check if the file exists relative to the current script's location
    if (!file_exists(__DIR__ . $path)) {
        // Add "../" to attempt to correct the path
        $adjustedPath = '../' . ltrim($path, '/');
    }

    return $adjustedPath;
}

// Handle the order submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the order details from POST request
    $items = $_POST['items'] ?? [];

    if (!empty($items)) {
        // Begin transaction to handle order creation
        $pdo->beginTransaction();

        try {
            // Insert the order into the orders table
            $stmt = $pdo->prepare("INSERT INTO orders (customer_id, restaurant_id, total_price, order_status) VALUES (?, ?, ?, 'Pending')");
            $stmt->execute([$_SESSION['user_id'], $restaurant_id, 0]); // Total price will be updated after adding details

            // Get the last inserted order ID
            $order_id = $pdo->lastInsertId();
            $total_price = 0;

            // Insert each item into order_details table and calculate total price
            foreach ($items as $item_id => $quantity) {
                if ($quantity > 0) {
                    // Get product price from the menu_items table
                    $stmt = $pdo->prepare("SELECT price FROM menu_items WHERE id = ?");
                    $stmt->execute([$item_id]);
                    $item = $stmt->fetch();

                    if ($item) {
                        $price = $item['price'];
                        $total_price += $price * $quantity;

                        // Insert into order_details table
                        $stmt = $pdo->prepare("INSERT INTO order_details (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$order_id, $item_id, $quantity, $price]);
                    }
                }
            }

            // Update total price of the order
            $stmt = $pdo->prepare("UPDATE orders SET total_price = ? WHERE id = ?");
            $stmt->execute([$total_price, $order_id]);

            // Commit transaction
            $pdo->commit();

            // Redirect to a confirmation page or order history
            header('Location: order_confirmation.php?order_id=' . $order_id);
            exit();
        } catch (Exception $e) {
            // Rollback in case of error
            $pdo->rollBack();
            die("Error processing the order: " . $e->getMessage());
        }
    } else {
        $error_message = "Please select at least one item.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order from <?php echo htmlspecialchars($restaurant['name']); ?></title>
    <link rel="stylesheet" href="../css/order.css"> <!-- Link to CSS -->
    <script src="../js/customer_order.js" defer></script> <!-- Link to JS -->
</head>

<body>
    <!-- Header -->
    <?php include '../includes/header.php'; ?>
    <div class="container">
        <div class="restaurant-info">
            <h1>Order from <?php echo htmlspecialchars($restaurant['name']); ?></h1>
            <p>Address: <?php echo htmlspecialchars($restaurant['address']); ?></p>
            <p>Rating: <?php echo htmlspecialchars($restaurant['stars']); ?> stars</p>
        </div>

        <!-- Display error message -->
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form method="POST" action="order.php?restaurant_id=<?php echo $restaurant_id; ?>" id="order-form">
            <div class="menu-items">
                <?php if ($menu_items): ?>
                    <?php foreach ($menu_items as $item): ?>
                        <div class="menu-item" data-item-id="<?php echo $item['id']; ?>">
                            <!-- Updated line with dynamic path adjustment -->
                            <img src="<?php echo htmlspecialchars(getCorrectedPath($item['product_images'])); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="product-image">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p><?php echo htmlspecialchars($item['description']); ?></p>
                            <p>Price: ₹ <?php echo htmlspecialchars($item['price']); ?></p>
                            <button type="button" class="add-to-cart-btn" data-item-id="<?php echo $item['id']; ?>">Add to Cart</button>

                            <div class="quantity-section" style="display:none;">
                                <button type="button" class="quantity-btn minus">-</button>
                                <span class="quantity">0</span>
                                <button type="button" class="quantity-btn plus">+</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No menu items available for this restaurant.</p>
                <?php endif; ?>
            </div>

            <!-- Place Order Button -->
            <div class="place-order-container">
                <button type="submit" id="place-order-btn" disabled>Place Order</button>
            </div>
        </form>
    </div>
    <!-- Footer -->
    <?php include '../includes/footer.php'; ?>
</body>

</html>
