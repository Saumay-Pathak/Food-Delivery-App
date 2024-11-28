document.addEventListener("DOMContentLoaded", function() {
    const addToCartBtns = document.querySelectorAll('.add-to-cart-btn');
    const placeOrderBtn = document.getElementById('place-order-btn');
    let itemAdded = false;

    addToCartBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const itemId = this.getAttribute('data-item-id');
            const menuItem = document.querySelector(`.menu-item[data-item-id="${itemId}"]`);
            const quantitySection = menuItem.querySelector('.quantity-section');
            const quantityDisplay = quantitySection.querySelector('.quantity');

            // Show the quantity section if not already visible
            if (quantitySection.style.display === 'none' || quantitySection.style.display === '') {
                quantitySection.style.display = 'flex';
                quantityDisplay.textContent = 1;  // Start with 1 quantity
                menuItem.querySelector('.minus').style.display = 'block';  // Show minus button
            }

            // Ensure the event listeners are only attached once for the "+" and "-" buttons
            const plusButton = menuItem.querySelector('.plus');
            const minusButton = menuItem.querySelector('.minus');

            // Increment quantity by 1 when the "+" button is clicked
            plusButton.removeEventListener('click', incrementQuantity);
            plusButton.addEventListener('click', incrementQuantity);

            // Decrement quantity by 1 when the "-" button is clicked
            minusButton.removeEventListener('click', decrementQuantity);
            minusButton.addEventListener('click', decrementQuantity);

            // Store the item and its quantity for submission
            const form = document.querySelector('form');
            let existingItemInput = form.querySelector(`input[name="items[${itemId}]"]`);

            if (existingItemInput) {
                // If the item already exists, update its quantity
                existingItemInput.value = quantityDisplay.textContent;
            } else {
                // Otherwise, create a new input for this item
                const itemsInput = document.createElement('input');
                itemsInput.type = 'hidden';
                itemsInput.name = `items[${itemId}]`;
                itemsInput.value = quantityDisplay.textContent;
                form.appendChild(itemsInput);
            }

            // Enable "Place Order" button if at least one item is in the cart
            itemAdded = true;
            placeOrderBtn.disabled = !itemAdded;
        });
    });

    // Function to increment quantity by 1
    function incrementQuantity(event) {
        const menuItem = event.target.closest('.menu-item');
        const quantityDisplay = menuItem.querySelector('.quantity');
        let currentQuantity = parseInt(quantityDisplay.textContent);
        quantityDisplay.textContent = currentQuantity + 1;
    }

    // Function to decrement quantity by 1
    function decrementQuantity(event) {
        const menuItem = event.target.closest('.menu-item');
        const quantityDisplay = menuItem.querySelector('.quantity');
        let currentQuantity = parseInt(quantityDisplay.textContent);
        
        if (currentQuantity > 1) {
            quantityDisplay.textContent = currentQuantity - 1;
        } else {
            removeItemFromCart(menuItem);
        }
    }

    // Function to remove item from the cart
    function removeItemFromCart(menuItem) {
        const itemId = menuItem.getAttribute('data-item-id');
        const form = document.querySelector('form');
        let existingItemInput = form.querySelector(`input[name="items[${itemId}]"]`);
        if (existingItemInput) {
            existingItemInput.remove();
        }

        // Hide the quantity section
        const quantitySection = menuItem.querySelector('.quantity-section');
        quantitySection.style.display = 'none';

        // Disable "Place Order" button if no items are in the cart
        itemAdded = false;
        placeOrderBtn.disabled = !itemAdded;
    }
});
