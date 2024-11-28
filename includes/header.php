<?php
echo <<<HTML
<header style="background: linear-gradient(to right; padding: 20px 40px; font-family: 'Arial', sans-serif; box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);">
    <div style="max-width: 1200px; margin: auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
        <div class="search-bar-container">
            <input type="text" class="search-bar" placeholder="Search for food, restaurants, or cuisines">
            <button class="search-btn"><i class="fa fa-search"></i></button>
        </div>
    </div>
</header>

<style>
    /* Font Awesome icon inside the button */
    .search-btn {
        background: transparent;
        border: none;
        padding: 10px 15px;
        cursor: pointer;
        color: #ff5722;
        font-size: 20px;
        transition: color 0.3s;
    }

    .search-btn:hover {
        color: #e64a19;
    }

    .search-bar-container {
        display: flex;
        justify-content: center;
        margin-top: 30px;
        width: 100%;
    }

    .search-bar {
        padding: 12px 20px;
        font-size: 16px;
        border: 2px solid #ff5722;
        border-radius: 30px;
        width: 30%;
        max-width: 400px;
        background-color: rgba(255, 255, 255, 0.1);
        color: #ff5722;
        transition: background-color 0.3s, border 0.3s;
    }
</style>

<!-- Add this link in the <head> section of your HTML file -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
HTML;
?>
