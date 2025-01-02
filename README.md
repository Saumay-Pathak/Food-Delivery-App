
# Food Delivery App

A PHP-based food delivery web application designed to manage restaurant menus, customer orders, and admin operations. This app allows users to browse food options, place orders, and manage deliveries.

---

## Features

- **Restaurant and menu management**
- **User authentication** for customers and administrators
- **Order placement and tracking**
- **Admin panel** for restaurant and order management

---

## Technologies Used

- **Backend:** PHP
- **Frontend:** HTML, CSS, JavaScript
- **Database:** MySQL
- **Dependency Management:** Composer

---

## Folder Structure

### Root Directory

- **`customer/login.php`**: Entry point of the Customers.
- **`restaurant/login.php`**: Entry point of the Restaurent Owners.
- **`admin/login.php`**: Entry point of the Admin.
- **`config.php`**: Configuration file for database.
- **`composer.json`**: PHP Composer configuration file.
- **`composer.lock`**: Dependency lock file.

### Key Directories

- **`admin/`**: Admin panel features and files.
- **`css/`**: Stylesheets for the web application.
- **`customer/`**: Features related to customer operations.
- **`database/`**: Database schema and related scripts.
- **`food_images/`**: Repository for food-related images.
- **`images/`**: General images used in the application.
- **`includes/`**: PHP include files for modularization.
- **`js/`**: JavaScript files for interactivity.
- **`restaurant/`**: Restaurant-related features and modules.
- **`resto_banner/`**: Banner images for restaurants.
- **`vendor/`**: Third-party dependencies managed via Composer.

---

## Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL
- Composer
- A web server like Apache or Nginx

### Steps

1. **Clone the repository:**
   ```bash
   git clone https://github.com/Saumay-Pathak/Food-Delivery-App
   ```

2. **Navigate to the project directory:**
   ```bash
   cd Food Delivery App
   ```

3. **Install dependencies using Composer:**
   ```bash
   composer install
   ```

4. **Set up the database:**

   - Import the SQL file located in the `database/` directory into your MySQL database.
   - Update the database credentials in `config.php`.

5. **Start the server:**

   - If using PHP's built-in server:
     ```bash
     php -S localhost:8000
     ```
   - Or configure your web server to serve the application.

---

## Contribution Guidelines

1. **Fork the repository.**
2. **Create a new branch:**
   ```bash
   git checkout -b feature-name
   ```
3. **Commit your changes:**
   ```bash
   git commit -m 'Add some feature'
   ```
4. **Push to the branch:**
   ```bash
   git push origin feature-name
   ```
5. **Create a pull request.**

---

## Contact

For questions or collaboration, please reach out to Rajgkp2932002@gmail.com
