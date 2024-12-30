document.addEventListener('DOMContentLoaded', function () {
  const searchBox = document.getElementById('search-box');
  const searchButton = document.getElementById('search-button');
  const productSection = document.getElementById('product-section');
  let searchTerm = '';

  // Function to render products
  function renderProducts(products) {
    productSection.innerHTML = ''; // Clear current products

    if (products.length > 0) {
      products.forEach(product => {
        const productBox = document.createElement('div');
        productBox.classList.add('product-box');
        productBox.setAttribute('data-product-name', product.product_name.toLowerCase());

        productBox.innerHTML = `
          <img src="${product.image_url}" alt="${product.product_name}">
          <h3>${product.product_name}</h3>
          <div class="price">
            <span class="original-price">Rs. ${parseFloat(product.original_price).toFixed(2)}</span>
            <span class="discounted-price">Rs. ${parseFloat(product.discounted_price).toFixed(2)}</span>
          </div>
          <div class="product-buttons">
            <button class="btn-cart">Add to Cart</button>
            <button class="btn-buy">Buy Now</button>
          </div>
        `;
        productSection.appendChild(productBox);
      });
    } else {
      productSection.innerHTML = '<p>No products found.</p>';
    }
  }

  // Function to fetch products
  function fetchProducts() {
    const formData = new FormData();
    formData.append('searchTerm', searchTerm); // Pass search term

    fetch('fetch_products.php', {
      method: 'POST',
      body: formData
    })
      .then(response => response.json())
      .then(data => {
        renderProducts(data); // Render the products
      })
      .catch(error => console.error('Error fetching products:', error));
  }

  // Handle search button click
  searchButton.addEventListener('click', function () {
    searchTerm = searchBox.value.trim();
    fetchProducts(); // Fetch products based on search term
  });

  // Initial load: Fetch all products when page loads
  fetchProducts();
});


 // Initialize Glide.js
  new Glide('.glide').mount();

  // Update cart count
  function updateCartCount() {
    const cartCount = localStorage.getItem('cartCount') || 0;
    document.querySelector('.cart-count').textContent = cartCount;
  }

  // Add to Cart Functionality
  function addToCart(id, name, image, originalPrice, discountedPrice) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const existingProductIndex = cart.findIndex(item => item.id === id);

    if (existingProductIndex > -1) {
      cart[existingProductIndex].quantity += 1;
    } else {
      cart.push({
        id,
        name,
        image,
        originalPrice,
        discountedPrice,
        quantity: 1
      });
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    localStorage.setItem('cartCount', cart.length);
    updateCartCount();

    // Show alert
    alert(`${name} has been added to your cart successfully!`);
  }

  // Initialize cart count on page load
  document.addEventListener('DOMContentLoaded', function () {
    updateCartCount();
  });

  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.add-to-favorite').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.closest('.product-item').dataset.productId;

            // Check if user is logged in
            fetch('check_login.php')
                .then(response => response.json())
                .then(data => {
                    if (data.loggedIn) {
                        // User is logged in, proceed with adding to favorites
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
                    } else {
                        // User is not logged in, prompt for registration
                        if (confirm('You need to register first. Click OK to register.')) {
                            window.location.href = 'register.php';
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });
});


