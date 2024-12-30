document.addEventListener('DOMContentLoaded', () => {
    // Add event listeners for Add to Cart and Add to Favourite buttons
    document.querySelectorAll('.btn-add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.closest('.product-box').dataset.productId;
            updateProductStatus(productId, 'cart');
        });
    });

    document.querySelectorAll('.btn-add-to-favourite').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.closest('.product-box').dataset.productId;
            updateProductStatus(productId, 'favourite');
        });
    });

    document.querySelectorAll('.btn-buy-now').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.closest('.product-box').dataset.productId;
            window.location.href = `buy_now.php?product_id=${productId}`;
        });
    });
});

function updateProductStatus(productId, type) {
    fetch('update_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}&type=${type}`,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('cartCount').innerText = data.cart_count;
            document.getElementById('favCount').innerText = data.fav_count;
        } else {
            alert('An error occurred. Please try again.');
        }
    });
}
