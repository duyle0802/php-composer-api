<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <div id="product-detail">
                <div class="text-center">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Thông tin đơn hàng</h5>
                    <div id="order-summary">
                        <p>Đang tải...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');
    
    if (productId) {
        loadProductDetail(productId);
    }
});

function loadProductDetail(productId) {
    fetch(API_URL + '/products/detail?id=' + productId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayProductDetail(data.product);
            } else {
                document.getElementById('product-detail').innerHTML = '<div class="alert alert-danger">Sản phẩm không tìm thấy</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('product-detail').innerHTML = '<div class="alert alert-danger">Lỗi khi tải sản phẩm</div>';
        });
}

function displayProductDetail(product) {
    const outOfStock = product.quantity_in_stock <= 0;
    
    const html = `
        <div class="row">
            <div class="col-md-6">
                <img src="${product.image || 'https://via.placeholder.com/500x500?text=No+Image'}" class="img-fluid rounded" alt="${product.name}">
            </div>
            <div class="col-md-6">
                <h1>${product.name}</h1>
                <div class="mb-3">
                    <span class="badge bg-info">${product.category_name}</span>
                </div>
                <h3 class="text-danger mb-3">${formatPrice(product.price)}</h3>
                <p class="text-muted mb-3">Số lượng còn lại: <strong>${product.quantity_in_stock > 0 ? product.quantity_in_stock : 'Hết hàng'}</strong></p>
                
                <div class="mb-4">
                    <label for="quantity" class="form-label">Số lượng</label>
                    <div class="quantity-selector">
                        <button class="quantity-btn" type="button" onclick="decreaseQuantity()" ${outOfStock ? 'disabled' : ''}>-</button>
                        <input type="number" id="quantity" class="quantity-input-custom" value="1" min="1" max="${product.quantity_in_stock}" ${outOfStock ? 'disabled' : ''} onchange="validateQuantity(this, ${product.quantity_in_stock})">
                        <button class="quantity-btn" type="button" onclick="increaseQuantity(${product.quantity_in_stock})" ${outOfStock ? 'disabled' : ''}>+</button>
                    </div>
                    <div id="quantity-warning" class="text-danger mt-2 small fw-bold" style="display: none;"></div>
                </div>
                
                <div class="d-flex gap-2 mb-4">
                    <button class="btn btn-danger btn-lg flex-grow-1" ${outOfStock ? 'disabled' : ''} onclick="checkoutNow(${product.id})">
                        <i class="fas fa-credit-card"></i> Mua ngay
                    </button>
                    <button class="btn btn-primary btn-lg flex-grow-1" ${outOfStock ? 'disabled' : ''} onclick="addToCartDetail(${product.id})">
                        <i class="fas fa-shopping-cart"></i> Giỏ hàng
                    </button>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h5>Mô tả sản phẩm</h5>
                        <p>${product.description || 'Không có mô tả'}</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('product-detail').innerHTML = html;
    updateOrderSummary(product);
}

function updateOrderSummary(product) {
    const quantity = document.getElementById('quantity')?.value || 1;
    const total = product.price * quantity;
    
    const html = `
        <p><strong>${product.name}</strong></p>
        <p>Giá: ${formatPrice(product.price)}</p>
        <p>Số lượng: ${quantity}</p>
        <hr>
        <p><strong>Tổng cộng: ${formatPrice(total)}</strong></p>
    `;
    
    document.getElementById('order-summary').innerHTML = html;
}

function addToCartDetail(productId) {
    const quantity = parseInt(document.getElementById('quantity').value) || 1;
    addToCart(productId, quantity);
}

function checkoutNow(productId) {
    const quantity = parseInt(document.getElementById('quantity').value) || 1;
    
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
            window.location.href = '<?php echo BASE_URL; ?>/?page=checkout';
        } else {
            showAlert(data.message || 'Lỗi khi thêm vào giỏ hàng', 'danger');
        }
    });
}

function increaseQuantity(maxStock) {
    const input = document.getElementById('quantity');
    let value = parseInt(input.value) || 0;
    if (value < maxStock) {
        input.value = value + 1;
        triggerQuantityChange();
        hideWarning();
    } else {
        showWarning('Đã đạt giới hạn số lượng trong kho!');
    }
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    let value = parseInt(input.value) || 0;
    if (value > 1) {
        input.value = value - 1;
        triggerQuantityChange();
        hideWarning();
    }
}

function validateQuantity(input, maxStock) {
    let value = parseInt(input.value);
    
    if (isNaN(value) || value < 1) {
        value = 1;
        hideWarning();
    } else if (value > maxStock) {
        value = maxStock;
        showWarning('Đã đạt giới hạn số lượng trong kho!');
    } else {
        hideWarning();
    }
    
    input.value = value;
    triggerQuantityChange();
}

function showWarning(msg) {
    const el = document.getElementById('quantity-warning');
    if (el) {
        el.textContent = msg;
        el.style.display = 'block';
        setTimeout(() => {
            el.style.display = 'none';
        }, 3000);
    }
}

function hideWarning() {
    const el = document.getElementById('quantity-warning');
    if (el) el.style.display = 'none';
}

function triggerQuantityChange() {
    const input = document.getElementById('quantity');
    const event = new Event('change', { bubbles: true });
    input.dispatchEvent(event);
}

// Update order summary when quantity changes
document.addEventListener('change', function(e) {
    if (e.target.id === 'quantity') {
        const productId = new URLSearchParams(window.location.search).get('id');
        fetch(API_URL + '/products/detail?id=' + productId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateOrderSummary(data.product);
                }
            });
    }
});
</script>
