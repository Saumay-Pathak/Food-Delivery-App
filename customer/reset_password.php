<?php
include('../config.php');
session_start();
session_regenerate_id(true); // Regenerate session ID for security

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    // Send OTP for password reset
    if ($action === 'send_otp') {
        $email = trim($_POST['email']);

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'error' => 'Invalid email address']);
            exit;
        }

        // Check if email exists in the database
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $otp = random_int(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['otp_email'] = $email;

            // Send OTP email
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
                $mail->Subject = 'Your OTP for Password Reset';
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
                                    </style>
                                </head>
                                <body>
                                    <div class='email-container'>
                                        <div class='header'>Password Reset Request</div>
                                        <div class='content'>
                                            <p>Hello,</p>
                                            <p>We received a request to reset your password. Use the OTP below to reset your password:</p>
                                            <div class='otp'>$otp</div>
                                            <p>This OTP is valid for 5 minutes.</p>
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
        } else {
            echo json_encode(['success' => false, 'error' => 'Email not found.']);
        }
        exit;
    }

    // Verify OTP and reset password
    if ($action === 'verify_and_reset_password') {
        $otp = $_POST['otp'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword !== $confirmPassword) {
            echo json_encode(['success' => false, 'error' => 'Passwords do not match.']);
            exit;
        }

        if ($_SESSION['otp'] === (int)$otp) {
            $email = $_SESSION['otp_email'];

            // Update the user's password directly (no hashing, storing plain text)
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->execute([$newPassword, $email]);

            // Clear OTP session data
            unset($_SESSION['otp']);
            unset($_SESSION['otp_email']);

            echo json_encode(['success' => true, 'message' => 'Password has been successfully reset.']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid OTP. Please try again.']);
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
    <title>Password Reset</title>
    <link rel="stylesheet" href="../css/customer_dashboard_signup.css">
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
    <form id="password-reset-form">
        <h2>Reset Your Password</h2>
        <div id="email-section">
            <input type="email" id="email" placeholder="Enter your email" required>
            <button type="button" id="send-otp-button">Send OTP</button>
        </div>
        <div id="otp-section">
            <input type="text" id="otp" placeholder="Enter OTP" required>
            <input type="password" id="new-password" placeholder="Enter new password" required>
            <input type="password" id="confirm-password" placeholder="Confirm new password" required>
            <button type="button" id="reset-password-button">Reset Password</button>
        </div>
        <div id="loading-icon">
            <img src="https://i.imgur.com/llF5iyg.gif" alt="Loading..." width="50">
        </div>
        <div id="feedback-message"></div>
    </form>

    <script>
        const form = document.getElementById('password-reset-form');
        const feedback = document.getElementById('feedback-message');
        const otpSection = document.getElementById('otp-section');
        const emailSection = document.getElementById('email-section');

        const showFeedback = (message) => {
            feedback.textContent = message;
        };

        document.getElementById('send-otp-button').addEventListener('click', () => {
            const email = document.getElementById('email').value;

            // Show loading icon before the request
            document.getElementById('loading-icon').style.display = 'block';

            fetch('reset_password.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'send_otp',
                        email
                    })
                })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('loading-icon').style.display = 'none';
                    if (data.success) {
                        otpSection.style.display = 'block';
                        emailSection.style.display = 'none';
                        showFeedback(data.message);
                    } else {
                        showFeedback(data.error);
                    }
                })
                .catch(() => {
                    document.getElementById('loading-icon').style.display = 'none';
                    showFeedback("An error occurred. Please try again later.");
                });
        });

        document.getElementById('reset-password-button').addEventListener('click', () => {
            const otp = document.getElementById('otp').value;
            const newPassword = document.getElementById('new-password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            fetch('reset_password.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'verify_and_reset_password',
                        otp,
                        new_password: newPassword,
                        confirm_password: confirmPassword
                    })
                })
                .then(res => res.json())
                .then(data => {
                    showFeedback(data.message);
                    if (data.success) {
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 2000);
                    }
                });
        });
    </script>
</body>

</html>