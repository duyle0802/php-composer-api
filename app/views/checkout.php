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
                            <label for="shipping-address-id" class="form-label">Chọn địa chỉ giao hàng</label>
                            <select id="shipping-address-id" class="form-select" onchange="calculateShipping()" required>
                                <option value="" disabled selected>-- Chọn địa chỉ --</option>
                            </select>
                            <div class="mt-2">
                                <a href="<?php echo BASE_URL; ?>/?page=add-address" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-plus"></i> Thêm địa chỉ mới
                                </a>
                            </div>
                        </div>

                        <div id="shipping-info" class="alert alert-info d-none">
                            <i class="fas fa-truck"></i> Khoảng cách: <span id="distance-display">0 km</span><br>
                            <strong>Phí vận chuyển: <span id="shipping-cost-display">0 ₫</span></strong>
                        </div>
                        <input type="hidden" id="shipping-cost-value" value="0">

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
let addresses = [];

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const itemsParam = urlParams.get('items');
    
    if (itemsParam) {
        selectedItems = itemsParam.split(',').map(id => parseInt(id));
    }

    loadCheckout();
});

function loadCheckout() {
    // Load Cart Items
    fetch(API_URL + '/cart/items')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cartItems = data.items;
                displayOrderItems();
                updateTotalOnlySubtotal(); 
            }
        });

    // Load Addresses
    fetch(API_URL + '/address/list')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addresses = data.addresses;
                populateAddresses();
            }
        });
}

function populateAddresses() {
    const select = document.getElementById('shipping-address-id');
    // Keep the default option
    select.innerHTML = '<option value="" disabled selected>-- Chọn địa chỉ --</option>';

    if (addresses.length === 0) {
        // Redirect logic handled by checking length, or show message?
        // User requested: "nếu chưa có thì chuyển sang trang để tạo một địa chỉ"
        // But maybe we should just notify/show button? The prompt says "if none, redirect".
        // Let's do a redirect if really 0, or just let them click the button?
        // Current implementation shows "Add Address" button. 
        // Let's force redirect if 0? 
        // "logic khi khách hàng select địa chỉ đã được tạo trước đó, nếu chưa có thì chuyển sang trang để tạo"
        // This implies if they try to select but have none.
        
        // I'll stick to showing the button prominently, but maybe auto-redirect is too aggressive if they just want to check cart?
        // "nếu chưa có thì chuyển sang trang" -> "If not have, switch to page". 
        // I will redirect if address list is empty.
        window.location.href = '<?php echo BASE_URL; ?>/?page=add-address';
        return;
    }

    addresses.forEach(addr => {
        const option = document.createElement('option');
        option.value = addr.id;
        option.textContent = addr.address_line + (addr.is_default == 1 ? ' (Mặc định)' : '');
        select.appendChild(option);

        // Auto selection if default
        if (addr.is_default == 1) {
            select.value = addr.id;
        }
    });
    
    // If we have a selected value (default), calculate shipping
    if (select.value) {
        calculateShipping();
    }
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

function updateTotalOnlySubtotal() {
    const subtotal = calculateSubtotal();
    document.getElementById('summary-subtotal').textContent = formatPrice(subtotal);
    updateFinalTotal();
}

function calculateShipping() {
    const addressId = document.getElementById('shipping-address-id').value;
    if (!addressId) return;

    fetch(API_URL + '/orders/calculate-shipping', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ address_id: addressId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('distance-display').textContent = data.distance + ' km';
            document.getElementById('shipping-cost-display').textContent = data.formatted_shipping_cost;
            document.getElementById('shipping-cost-value').value = data.shipping_cost;
            
            document.getElementById('shipping-info').classList.remove('d-none');
            
            document.getElementById('summary-shipping').textContent = data.formatted_shipping_cost;
            
            updateFinalTotal();
        } else {
            showAlert('Lỗi tính phí vận chuyển', 'danger');
        }
    });
}

function updateFinalTotal() {
    const subtotal = calculateSubtotal();
    const shippingCost = parseFloat(document.getElementById('shipping-cost-value').value) || 0;
    
    const discountElement = document.getElementById('summary-discount');
    const discount = discountElement ? parseFloat(discountElement.textContent.replace(/[^0-9]/g, '')) : 0;
    
    const total = subtotal - discount + shippingCost;

    document.getElementById('summary-subtotal').textContent = formatPrice(subtotal);
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
    updateFinalTotal();
    showAlert('Áp dụng voucher thành công!', 'success');
}

function confirmCheckout() {
    const addressId = document.getElementById('shipping-address-id').value;
    const voucherCode = document.getElementById('voucher-code').value;

    if (!addressId) {
        showAlert('Vui lòng chọn địa chỉ giao hàng', 'warning');
        return;
    }

    let itemsToOrder = selectedItems.length > 0 ? selectedItems : cartItems.map(item => item.id);

    fetch(API_URL + '/orders/create', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            address_id: addressId,
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
