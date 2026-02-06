<div class="container py-5">
    <div class="row">
        <div class="col-md-8">
            <h2 class="mb-4">Giỏ hàng của tôi</h2>
            <div id="cart-items">
                <div class="text-center">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="cart-summary">
                <h5 class="mb-3">Tóm tắt đơn hàng</h5>
                
                <div class="summary-item">
                    <span>Tổng sản phẩm:</span>
                    <strong id="total-items">0</strong>
                </div>

                <div class="summary-item">
                    <span>Tổng tiền hàng:</span>
                    <strong id="subtotal">0 ₫</strong>
                </div>





                <div class="summary-item">
                    <span class="fs-5"><strong>Tổng cộng:</strong></span>
                    <strong id="final-total" class="fs-5 text-danger">0 ₫</strong>
                </div>
                
                <div class="alert alert-light mt-2 text-center" style="font-size: 0.9em;">
                    <i class="fas fa-info-circle"></i> Phí giao hàng sẽ được tính tại bước thanh toán
                </div>

                <button class="btn btn-danger btn-lg w-100 mt-3" onclick="goToCheckout()">Tiến hành thanh toán</button>
            </div>
        </div>
    </div>
</div>

<script>
let cartItems = [];

document.addEventListener('DOMContentLoaded', function() {
    loadCart();
});

function loadCart() {
    fetch(API_URL + '/cart/items')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cartItems = data.items;
                displayCartItems(data.items);
                calculateTotals();
            } else {
                document.getElementById('cart-items').innerHTML = '<div class="alert alert-info">Giỏ hàng của bạn trống</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('cart-items').innerHTML = '<div class="alert alert-danger">Lỗi khi tải giỏ hàng</div>';
        });
}

function displayCartItems(items) {
    if (items.length === 0) {
        document.getElementById('cart-items').innerHTML = '<div class="alert alert-info">Giỏ hàng của bạn trống</div>';
        return;
    }

    let html = '';
    items.forEach(item => {
        const checked = item.quantity_in_stock > 0 ? 'checked' : 'disabled';
        html += `
            <div class="cart-item">
                <input type="checkbox" class="form-check-input cart-checkbox" value="${item.id}" ${checked} onchange="calculateTotals()">
                <div class="cart-item-image">
                    <img src="${item.image || 'https://via.placeholder.com/120x120?text=No+Image'}" alt="${item.name}">
                </div>
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">Giá: ${formatPrice(item.price)}</div>
                    <div class="mb-2">
                        <label>Số lượng:</label>
                        <input type="number" value="${item.quantity}" min="1" max="${item.quantity_in_stock}" class="quantity-input" 
                            onchange="updateQuantity(${item.id}, this.value)">
                    </div>
                    ${item.quantity_in_stock <= 0 ? '<div class="text-danger"><strong>Hết hàng</strong></div>' : ''}
                </div>
                <div>
                    <strong>${formatPrice(item.price * item.quantity)}</strong>
                    <button class="btn btn-sm btn-danger mt-2" onclick="removeItem(${item.id})">Xóa</button>
                </div>
            </div>
        `;
    });

    document.getElementById('cart-items').innerHTML = html;
}

function updateQuantity(cartId, quantity) {
    quantity = parseInt(quantity);
    if (quantity < 1) return;

    fetch(API_URL + '/cart/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            cart_id: cartId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadCart();
        } else {
            showAlert('Lỗi khi cập nhật giỏ hàng', 'danger');
        }
    });
}

function removeItem(cartId) {
    if (confirm('Bạn chắc chắn muốn xóa sản phẩm này?')) {
        fetch(API_URL + '/cart/remove?cart_id=' + cartId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadCart();
                    updateCartCount();
                } else {
                    showAlert('Lỗi khi xóa sản phẩm', 'danger');
                }
            });
    }
}

function calculateTotals() {
    let subtotal = 0;
    let totalItems = 0;

    document.querySelectorAll('.cart-checkbox:checked').forEach(checkbox => {
        const cartId = checkbox.value;
        const item = cartItems.find(i => i.id == cartId);
        if (item) {
            subtotal += item.price * item.quantity;
            totalItems += item.quantity;
        }
    });

    document.getElementById('total-items').textContent = totalItems;
    document.getElementById('subtotal').textContent = formatPrice(subtotal);

    updateFinalTotal();
}



function updateFinalTotal() {
    const subtotal = parseFloat(document.getElementById('subtotal').textContent.replace(/[^0-9]/g, '')) || 0;
    
    document.getElementById('final-total').textContent = formatPrice(Math.max(0, subtotal));
}

function goToCheckout() {
    const selectedItems = [];
    document.querySelectorAll('.cart-checkbox:checked').forEach(checkbox => {
        selectedItems.push(checkbox.value);
    });

    if (selectedItems.length === 0) {
        showAlert('Vui lòng chọn ít nhất một sản phẩm', 'warning');
        return;
    }

    window.location.href = '<?php echo BASE_URL; ?>/?page=checkout&items=' + selectedItems.join(',');
}
</script>
