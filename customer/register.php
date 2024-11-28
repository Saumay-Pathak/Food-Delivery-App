<?php
include('../config.php');
session_start();
session_regenerate_id(true); // Regenerate session ID for security

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'send_otp') {
        $email = trim($_POST['email']);
        $name = trim($_POST['name']);
        $password = trim($_POST['password']);

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'error' => 'Invalid email address']);
            exit;
        }

        $otp = random_int(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_email'] = $email;
        $_SESSION['signup_name'] = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $_SESSION['signup_password'] = $password; // Storing plaintext password (to match your request)

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'raj2932002@gmail.com'; // Replace with your email
            $mail->Password = 'rrvs kkzt cdpd kpsx';  // Replace with your app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('raj2932002@gmail.com', 'Piggy');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Your OTP for Registration';
            $mail->Body = "
                                <html>
                                <head>
                                    <style>
                                        body {
                                            font-family: Arial, sans-serif;
                                            color: #333;
                                            background-color: #f4f4f4;
                                            padding: 20px;
                                        }
                                        .email-container {
                                            background-color: #ffffff;
                                            border-radius: 8px;
                                            padding: 30px;
                                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                                            max-width: 600px;
                                            margin: 0 auto;
                                        }
                                        .header {
                                            font-size: 24px;
                                            color: #ff5c5c;
                                            text-align: center;
                                            margin-bottom: 20px;
                                        }
                                        .content {
                                            font-size: 16px;
                                            line-height: 1.6;
                                        }
                                        .otp {
                                            font-size: 20px;
                                            font-weight: bold;
                                            color: #ff5c5c;
                                            padding: 10px;
                                            background-color: #f0f0f0;
                                            border-radius: 5px;
                                        }
                                        .footer {
                                            font-size: 12px;
                                            text-align: center;
                                            color: #777;
                                            margin-top: 20px;
                                        }
                                    </style>
                                </head>
                                <body>
                                    <div class='email-container'>
                                        <div class='header'>Welcome to Piggy!</div>
                                        <div class='content'>
                                            <p>Hello <strong>$name</strong>,</p>
                                            <p>Thank you for registering with us. To complete your sign-up, please use the One-Time Password (OTP) below:</p>
                                            <div class='otp'>$otp</div>
                                            <p>This OTP is valid for a limited time. Please use it soon!</p>
                                        </div>
                                        <div class='footer'>
                                            <p>Thank you for choosing Piggy. We look forward to serving you!</p>
                                            <p>If you didn't request this OTP, please ignore this email.</p>
                                        </div>
                                    </div>
                                </body>
                                </html>
                            ";
            $mail->send();
            echo json_encode(['success' => true, 'message' => 'OTP sent to your email.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Failed to send OTP. Please try again later.']);
        }
        exit;
    }

    if ($action === 'verify_and_register') {
        $otp = $_POST['otp'];

        if (isset($_SESSION['otp']) && $_SESSION['otp'] == $otp) {
            $name = $_SESSION['signup_name'];
            $email = $_SESSION['otp_email'];
            $password = $_SESSION['signup_password']; // Plaintext password (not recommended)

            // Insert data into the database
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
            $stmt->execute([$name, $email, $password]);

            // Clear sensitive data from the session
            unset($_SESSION['otp'], $_SESSION['otp_email'], $_SESSION['signup_name'], $_SESSION['signup_password']);

            echo json_encode([
                'success' => true,
                'message' => 'Registration successful. Redirecting you to the login page...',
                'redirect' => 'login.php'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Invalid OTP. Please try again.'
            ]);
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Piggy</title>
    <link rel="stylesheet" href="../css/customer_dashboard_signup.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            padding: 20px;
            width: 100%;
        }

        h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        input {
            width: 94%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 16px;
        }

        button {
            background: #ff4d4d;
            margin-top: 10px;
            border-radius: 20px !important;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background: #e84343;
        }

        #feedback-message {
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
        }

        #otp-section {
            display: none;
        }

        #loading-icon {
            display: none;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <form id="signup-form">
        <h2>Create Your Account</h2>
        <div id="user-details">
            <input type="text" id="name" placeholder="Full Name" required>
            <input type="email" id="email" placeholder="Email Address" required>
            <input type="password" id="password" placeholder="Password" required>
        </div>
        <div id="otp-section">
            <input type="text" id="otp" placeholder="Enter OTP" required>
            <button type="button" id="submit-register-button">Submit and Register</button>
        </div>
        <button type="button" id="send-otp-button">Send OTP</button>
        <div id="loading-icon">
            <img src="https://i.imgur.com/llF5iyg.gif" alt="Loading..." width="50">
        </div>
        <div id="feedback-message"></div>
    </form>

    <script>
        const form = document.getElementById('signup-form');
        const feedback = document.getElementById('feedback-message');
        const otpSection = document.getElementById('otp-section');
        const userDetails = document.getElementById('user-details');

        const showFeedback = (message) => {
            feedback.textContent = message;
        };

        document.getElementById('send-otp-button').addEventListener('click', () => {
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            // Show loading icon before the request
            document.getElementById('loading-icon').style.display = 'block';

            fetch('register.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'send_otp',
                        name,
                        email,
                        password
                    })
                })
                .then(res => res.json())
                .then(data => {
                    // Hide loading icon when the response is received
                    document.getElementById('loading-icon').style.display = 'none';

                    if (data.success) {
                        otpSection.style.display = 'block';
                        userDetails.style.display = 'none';
                        showFeedback(data.message);
                    } else {
                        showFeedback(data.error);
                    }
                })
                .catch(() => {
                    // Hide loading icon in case of error
                    document.getElementById('loading-icon').style.display = 'none';
                    showFeedback("An error occurred. Please try again later.");
                });
        });

        document.getElementById('submit-register-button').addEventListener('click', () => {
            const otp = document.getElementById('otp').value;

            fetch('register.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'verify_and_register',
                        otp
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        showFeedback(data.message);
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 2000);
                    } else {
                        showFeedback(data.error);
                    }
                });
        });
    </script>
</body>

</html>
