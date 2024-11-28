<?php
// Include database configuration
include('../config.php');
session_start();

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get the order ID from the query string
$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    die("Invalid order ID.");
}

// Fetch the order details
$stmt = $pdo->prepare("SELECT o.id, o.total_price, r.name AS restaurant_name, r.address AS restaurant_address
                        FROM orders o
                        JOIN restaurants r ON o.restaurant_id = r.id
                        WHERE o.id = ? AND o.customer_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    die("Order not found or you do not have permission to view it.");
}

// Handle form submission for payment and address
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the address and payment method from the form
    $address = $_POST['address'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';

    // Mock payment logic for demo (order is always confirmed regardless of payment details)
    $stmt = $pdo->prepare("UPDATE orders SET order_status = 'Confirmed', address = ?, payment_method = ? WHERE id = ?");
    $stmt->execute([$address, $payment_method, $order_id]);

    // Set the success message
    $_SESSION['order_confirmed'] = "Order Confirmed! You will be redirected shortly.";

    // Redirect to dashboard after a short delay to show the confirmation message
    echo "<script>
            alert('Your order has been confirmed!');
            window.location.href = 'dashboard.php'; 
          </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            line-height: 1.6;
        }

        .container {
            width: 100%;
            max-width: 960px;
            margin: 40px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            padding: 20px;
            background-color: #f44336;
            color: white;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 700;
        }

        .order-details,
        .address-form,
        .payment-options {
            margin-bottom: 20px;
        }

        .order-details {
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .order-details h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .order-details p {
            font-size: 16px;
            color: #555;
        }

        .order-details .total-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: #e74c3c;
        }

        .address-form textarea {
            width: 100%;
            height: 120px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            resize: none;
            font-size: 16px;
            margin-bottom: 15px;
            outline: none;
        }

        .address-form textarea:focus {
            border-color: #f44336;
        }

        .payment-options h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .payment-options label {
            display: block;
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
        }

        .payment-options input[type="radio"] {
            margin-right: 10px;
        }

        .payment-options button {
            padding: 12px 30px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-options button:hover {
            background-color: #45a049;
        }

        #payment-details {
            margin-top: 20px;
            padding: 20px;
            background-color: #fafafa;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .confirm-payment-btn {
            width: 100%;
            padding: 15px;
            background-color: #ff5722;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 30px;
            transition: background-color 0.3s;
        }

        .confirm-payment-btn:hover {
            background-color: #e64a19;
        }

        .footer {
            text-align: center;
            padding: 15px;
            background-color: #333;
            color: #fff;
            margin-top: 40px;
            border-radius: 8px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .header h1 {
                font-size: 1.75rem;
            }

            .order-details p,
            .order-details .total-price {
                font-size: 14px;
            }

            .confirm-payment-btn {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <div class="container">
        <div class="header">
            <h1>Order Confirmation</h1>
        </div>

        <div class="order-details">
            <h2>Order Details</h2>
            <p><strong>Restaurant:</strong> <?php echo htmlspecialchars($order['restaurant_name']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($order['restaurant_address']); ?></p>
            <p class="total-price">Total Price: ₹<?php echo htmlspecialchars($order['total_price']); ?></p>
        </div>

        <form action="order_confirmation.php?order_id=<?php echo $order_id; ?>" method="POST">
            <div class="address-form">
                <h2>Enter Your Delivery Address</h2>
                <textarea name="address" placeholder="Enter your delivery address here..."></textarea>
            </div>

            <div class="payment-options">
                <h2>Select Payment Method</h2>
                <label>
                    <input type="radio" name="payment_method" value="Credit Card" /> Credit Card
                </label>
                <label>
                    <input type="radio" name="payment_method" value="PayPal" /> PayPal
                </label>
                <label>
                    <input type="radio" name="payment_method" value="Cash on Delivery" /> Cash on Delivery
                </label>
                <label>
                    <input type="radio" name="payment_method" value="UPI" /> UPI
                </label>
            </div>

            <button type="submit" class="confirm-payment-btn">Confirm Order</button>
        </form>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>

</html>
