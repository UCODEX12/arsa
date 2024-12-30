document.addEventListener('DOMContentLoaded', () => {
    const cartButtons = document.querySelectorAll('.add-to-cart');

    cartButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            const productId = e.target.closest('.product-item').getAttribute('data-product-id');

            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartCount(data.cart_count);
                    showSuccessAlert('Item added to cart successfully!');
                } else {
                    console.error(data.message);
                    alert('Failed to add to cart: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    function updateCartCount(count) {
        const cartCountElement = document.querySelector('.cart-count');
        cartCountElement.textContent = count;
    }

    function showSuccessAlert(message) {
        alert(message); // You can replace this with a custom alert if needed
    }
});


