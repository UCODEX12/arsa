document.addEventListener('DOMContentLoaded', () => {
    const cartModal = document.getElementById('cart-modal');
    const closeBtn = document.querySelector('.close-btn');
    const cartItemsList = document.getElementById('cart-items');
    const checkoutButton = document.getElementById('checkout-button');

    function updateCart() {
        cartItemsList.innerHTML = '';
        cart.forEach(item => {
            const listItem = document.createElement('li');
            listItem.textContent = `Product ID: ${item.id} - Quantity: ${item.quantity}`;
            cartItemsList.appendChild(listItem);
        });
    }

    document.querySelectorAll('.btn-cart').forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.getAttribute('data-product-id');
            const product = cart.find(item => item.id === productId);
            if (product) {
                product.quantity += 1;
            } else {
                cart.push({ id: productId, quantity: 1 });
            }
            updateCart();
            cartModal.style.display = 'block';
        });
    });

    closeBtn.addEventListener('click', () => {
        cartModal.style.display = 'none';
    });

    window.addEventListener('click', (event) => {
        if (event.target === cartModal) {
            cartModal.style.display = 'none';
        }
    });

    checkoutButton.addEventListener('click', () => {
        // Handle checkout process
        alert('Proceeding to checkout...');
    });

    // Add to Cart
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    let cartCount = 0;

    function updateCartCount() {
        let cartCountElem = document.querySelector('.cart-count');
        if (!cartCountElem) {
            cartCountElem = document.createElement('span');
            cartCountElem.classList.add('cart-count');
            cartIcon.parentNode.appendChild(cartCountElem);
        }
        cartCountElem.textContent = cartCount;
    }

    addToCartButtons.forEach(button => {
        button.addEventListener('click', () => {
            cartCount++;
            updateCartCount();
        });
    });

    updateCartCount();

    // Add to Favorites
    document.querySelectorAll('.add-to-favorite').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.closest('.product-item').dataset.productId;
            fetch('add_to_favorites.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'product_id': productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added to favorites!');
                } else {
                    alert('Failed to add product to favorites.');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // Navigation Toggle
    const navToggle = document.getElementById('nav-toggle');
    const navMenu = document.getElementById('nav-menu');

    navToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });
});


document.addEventListener('DOMContentLoaded', () => {
    // Update favorite count dynamically if needed
    const updateFavoriteCount = (count) => {
        const favoriteCountElem = document.querySelector('.nav-icon .fa-heart + .cart-count');
        if (favoriteCountElem) {
            favoriteCountElem.textContent = count;
        }
    };

    // Example of updating count (replace with your logic)
    updateFavoriteCount(5); // Replace with actual favorite count
});

