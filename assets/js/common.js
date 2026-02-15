// API Base URL - dynamically determined based on page location
// Get the current path (e.g., "/" or "/PHPCom_APIver/")
const basePath = window.location.pathname.includes('/PHPCom_APIver/')
    ? '/PHPCom_APIver/public/api.php'
    : '/public/api.php';
const API_URL = basePath;

// Fetch cart count
function updateCartCount() {
    fetch(API_URL + '/cart/count')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.count > 0) {
                document.getElementById('cart-count').textContent = data.count;
                document.getElementById('cart-count').style.display = 'inline-block';
            } else {
                document.getElementById('cart-count').style.display = 'none';
            }
        })
        .catch(error => console.error('Error:', error));
}

// Logout function
function logout() {
    fetch(API_URL + '/auth/logout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Use the current pathname (e.g., /PHPCom_APIver/public/index.php or /) 
                // and append the query string
                const currentPath = window.location.pathname;
                window.location.href = currentPath + '?page=home';
            }
        });
}

// Add to cart
function addToCart(productId, quantity = 1) {
    fetch(API_URL + '/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount();
                showAlert('Sản phẩm đã được thêm vào giỏ hàng!', 'success');
            } else {
                showAlert(data.message || 'Lỗi khi thêm vào giỏ hàng', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Lỗi khi thêm vào giỏ hàng', 'danger');
        });
}

// Show alert message
function showAlert(message, type = 'info') {
    const alertContainer = document.getElementById('alert-container');
    if (!alertContainer) {
        const container = document.createElement('div');
        container.id = 'alert-container';
        container.style.cssText = 'position: fixed; top: 80px; right: 20px; z-index: 1000; max-width: 400px;';
        document.body.appendChild(container);
    }

    const alertId = 'alert-' + Date.now();
    const alertHTML = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    document.getElementById('alert-container').innerHTML += alertHTML;

    setTimeout(() => {
        const alert = document.getElementById(alertId);
        if (alert) {
            alert.remove();
        }
    }, 5000);
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

// Format price
function formatPrice(price) {
    return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + ' ₫';
}

// Calculate shipping cost
