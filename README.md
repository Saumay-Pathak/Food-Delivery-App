Food Delivery App

A PHP-based food delivery web application designed to manage restaurant menus, customer orders, and admin operations. This app allows users to browse food options, place orders, and manage deliveries.

Features

Restaurant and menu management.

User authentication for customers and administrators.

Order placement and tracking.

Admin panel for restaurant and order management.

Integration with payment gateways.

Technologies Used

Backend: PHP

Frontend: HTML, CSS, JavaScript

Database: MySQL

Dependency Management: Composer

Folder Structure

Root Directory

index.php: Entry point of the web application.

config.php: Configuration file for database and application settings.

composer.json: PHP Composer configuration file.

composer.lock: Dependency lock file.

Key Directories

admin/: Admin panel features and files.

css/: Stylesheets for the web application.

customer/: Features related to customer operations.

database/: Database schema and related scripts.

food_images/: Repository for food-related images.

images/: General images used in the application.

includes/: PHP include files for modularization.

js/: JavaScript files for interactivity.

restaurant/: Restaurant-related features and modules.

resto_banner/: Banner images for restaurants.

vendor/: Third-party dependencies managed via Composer.

Installation

Prerequisites

PHP 7.4 or higher

MySQL

Composer

A web server like Apache or Nginx

Steps

Clone the repository:

git clone <repository-url>

Navigate to the project directory:

cd Food Delivery App

Install dependencies using Composer:

composer install

Set up the database:

Import the SQL file located in the database/ directory into your MySQL database.

Update the database credentials in config.php.

Start the server:

If using PHP's built-in server:

php -S localhost:8000

Or configure your web server to serve the application.
