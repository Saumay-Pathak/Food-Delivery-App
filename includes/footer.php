<?php
echo <<<HTML
<footer style="background-color: transparent; color: #333; padding: 20px 0; font-family: 'Arial', sans-serif;">
    <div style="max-width: 1200px; margin: auto; padding: 0 15px;">
        <div style="display: flex; justify-content: space-between; flex-wrap: wrap; align-items: center;">
            <!-- Company Info -->
            <div style="flex: 1; min-width: 250px; text-align: left; padding: 10px;">
                <h3 style="margin-bottom: 5px; font-size: 20px; color: #ff5722;">Piggy</h3>
                <p style="font-size: 14px; line-height: 1.4; color: #555;">Your one-stop destination for food delivery from your favorite restaurants.</p>
                <p style="font-size: 12px; margin-top: 10px; color: #777;">&copy; 2024 Piggy. All Rights Reserved.</p>
            </div>
            
            <!-- Navigation Links -->
            <div style="flex: 1; min-width: 250px; text-align: center; padding: 10px;">
                <h4 style="margin-bottom: 5px; font-size: 16px; color: #ff5722;">Quick Links</h4>
                <ul style="list-style: none; padding: 0; margin: 0; font-size: 14px; line-height: 1.6;">
                    <li><a href="/about" style="color: #444; text-decoration: none; transition: color 0.3s;">About Us</a></li>
                    <li><a href="/contact" style="color: #444; text-decoration: none; transition: color 0.3s;">Contact</a></li>
                    <li><a href="/faq" style="color: #444; text-decoration: none; transition: color 0.3s;">FAQ</a></li>
                    <li><a href="/terms" style="color: #444; text-decoration: none; transition: color 0.3s;">Terms & Conditions</a></li>
                </ul>
            </div>
            
            <!-- Social Media Links with Icons -->
            <div style="flex: 1; min-width: 250px; text-align: right; padding: 10px;">
                <h4 style="margin-bottom: 5px; font-size: 16px; color: #ff5722;">Follow Us</h4>
                <div>
                    <a href="https://facebook.com" style="color: #444; text-decoration: none; margin-right: 10px; font-size: 20px; transition: color 0.3s;">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="https://twitter.com" style="color: #444; text-decoration: none; margin-right: 10px; font-size: 20px; transition: color 0.3s;">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://instagram.com" style="color: #444; text-decoration: none; margin-right: 10px; font-size: 20px; transition: color 0.3s;">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://youtube.com" style="color: #444; text-decoration: none; margin-right: 10px; font-size: 20px; transition: color 0.3s;">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Subscription Form -->
        <div style="margin-top: 20px; text-align: center;">
            <h4 style="margin-bottom: 5px; font-size: 16px; color: #ff5722;">Stay Updated with Our Latest Offers</h4>
            <form action="/subscribe" method="POST" style="display: inline-block;">
                <input type="email" name="email" placeholder="Enter your email" 
                       style="padding: 8px; font-size: 14px; border: 1px solid #aaa; border-radius: 30px; width: 200px; max-width: 100%; margin-right: 10px; background-color: #f9f9f9; color: #333;">
                <button type="submit" style="padding: 8px 15px; font-size: 14px; background-color: #ff5722; color: #fff; border: none; border-radius: 30px; cursor: pointer;">
                    Subscribe
                </button>
            </form>
        </div>
    </div>
</footer>

<!-- Font Awesome (for icons) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
HTML;
?>
