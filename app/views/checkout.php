<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <h2 class="mb-4">Thanh toán</h2>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Thông tin giao hàng</h5>
                </div>
                <div class="card-body">
                    <form id="checkout-form">
                        <div class="mb-3">
                            <label for="shipping-address" class="form-label">Địa chỉ giao hàng</label>
                            <textarea id="shipping-address" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="shipping-distance" class="form-label">Khoảng cách giao hàng (km)</label>
                            <input type="number" id="shipping-distance" class="form-control" value="0" min="0" onchange="updateTotal()" required>
                        </div>

                        <div class="mb-3">
                            <label for="voucher-code" class="form-label">Mã voucher (nếu có)</label>
                            <div class="input-group">
                                <input type="text" id="voucher-code" class="form-control" placeholder="Nhập mã voucher">
                                <button class="btn btn-outline-secondary" type="button" onclick="applyVoucher()">Áp dụng</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Sản phẩm thanh toán</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Tổng cộng</th>
                            </tr>
                        </thead>
                        <tbody id="order-items">
                            <tr>
                                <td colspan="4" class="text-center">
                                    <div class="spinner"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="cart-summary">
                <h5 class="mb-3">Tóm tắt đơn hàng</h5>
                
                <div class="summary-item">
                    <span>Tổng tiền hàng:</span>
                    <strong id="summary-subtotal">0 ₫</strong>
                </div>

                <div class="summary-item">
                    <span>Giảm giá:</span>
                    <strong id="summary-discount" class="text-danger">0 ₫</strong>
                </div>

                <div class="summary-item">
                    <span>Phí giao hàng:</span>
                    <strong id="summary-shipping">0 ₫</strong>
                </div>

                <div class="summary-item">
                    <span class="fs-5"><strong>Tổng cộng:</strong></span>
                    <strong id="summary-total" class="fs-5 text-danger">0 ₫</strong>
                </div>

                <button class="btn btn-danger btn-lg w-100 mt-3" onclick="confirmCheckout()">Xác nhận thanh toán</button>
            </div>
        </div>
    </div>
</div>

<script>
let cartItems = [];
let selectedItems = [];

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const itemsParam = urlParams.get('items');
    
    if (itemsParam) {
        selectedItems = itemsParam.split(',').map(id => parseInt(id));
    }

    loadCheckout();
});

function loadCheckout() {
    fetch(API_URL + '/cart/items')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cartItems = data.items;
                displayOrderItems();
                updateTotal();
            }
        });
}

function displayOrderItems() {
    let html = '';
    let itemsToShow = selectedItems.length > 0 
        ? cartItems.filter(item => selectedItems.includes(item.id))
        : cartItems;

    if (itemsToShow.length === 0) {
        html = '<tr><td colspan="4" class="text-center text-danger">Giỏ hàng trống</td></tr>';
    } else {
        itemsToShow.forEach(item => {
            const total = item.price * item.quantity;
            html += `
                <tr>
                    <td>${item.name}</td>
                    <td>${formatPrice(item.price)}</td>
                    <td>${item.quantity}</td>
                    <td>${formatPrice(total)}</td>
                </tr>
            `;
        });
    }

    document.getElementById('order-items').innerHTML = html;
}

function calculateSubtotal() {
    let subtotal = 0;
    let itemsToCount = selectedItems.length > 0 
        ? cartItems.filter(item => selectedItems.includes(item.id))
        : cartItems;

    itemsToCount.forEach(item => {
        subtotal += item.price * item.quantity;
    });

    return subtotal;
}

function updateTotal() {
    const subtotal = calculateSubtotal();
    const distance = parseFloat(document.getElementById('shipping-distance').value) || 0;
    
    const FREE_DISTANCE = 25;
    const COST_PER_25KM = 20000;
    
    let shippingCost = 0;
    if (distance > FREE_DISTANCE) {
        const kmOver = distance - FREE_DISTANCE;
        shippingCost = Math.ceil(kmOver / 25) * COST_PER_25KM;
    }

    const discountElement = document.getElementById('summary-discount');
    const discount = discountElement ? parseFloat(discountElement.textContent.replace(/[^0-9]/g, '')) : 0;
    
    const total = subtotal - discount + shippingCost;

    document.getElementById('summary-subtotal').textContent = formatPrice(subtotal);
    document.getElementById('summary-shipping').textContent = formatPrice(shippingCost);
    document.getElementById('summary-total').textContent = formatPrice(Math.max(0, total));
}

function applyVoucher() {
    const code = document.getElementById('voucher-code').value;
    if (!code) {
        showAlert('Vui lòng nhập mã voucher', 'warning');
        return;
    }

    // Simple 10% discount for demo
    const subtotal = calculateSubtotal();
    const discount = Math.round(subtotal * 0.1);
    
    document.getElementById('summary-discount').textContent = formatPrice(discount);
    updateTotal();
    showAlert('Áp dụng voucher thành công!', 'success');
}

function confirmCheckout() {
    const shippingAddress = document.getElementById('shipping-address').value;
    const shippingDistance = parseFloat(document.getElementById('shipping-distance').value) || 0;
    const voucherCode = document.getElementById('voucher-code').value;

    if (!shippingAddress.trim()) {
        showAlert('Vui lòng nhập địa chỉ giao hàng', 'warning');
        return;
    }

    let itemsToOrder = selectedItems.length > 0 ? selectedItems : cartItems.map(item => item.id);

    fetch(API_URL + '/orders/create', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            shipping_address: shippingAddress,
            shipping_distance: shippingDistance,
            voucher_code: voucherCode,
            selected_items: itemsToOrder
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount();
            window.location.href = '<?php echo BASE_URL; ?>/?page=order-confirmation&order_id=' + data.order_id;
        } else {
            showAlert(data.message || 'Lỗi khi tạo đơn hàng', 'danger');
        }
    });
}
</script>
