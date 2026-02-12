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
                            <label class="form-label fw-bold">Địa chỉ giao hàng</label>
                            <div class="card p-3 bg-light border-0" id="selected-address-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1" id="selected-address-text">Chưa chọn địa chỉ</h6>
                                        <small class="text-muted" id="selected-distance-text">--</small>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="toggleAddressSelect()">
                                        <i class="fas fa-edit"></i> Thay đổi
                                    </button>
                                </div>
                            </div>

                            <div id="address-select-container" class="mt-3 d-none">
                                <select id="shipping-address-id" class="form-select" onchange="onAddressChange()" required>
                                    <option value="" disabled selected>-- Chọn địa chỉ --</option>
                                </select>
                                <div class="mt-2 text-end">
                                    <a href="<?php echo BASE_URL; ?>/?page=add-address" class="btn btn-link btn-sm text-decoration-none">
                                        <i class="fas fa-plus-circle"></i> Thêm địa chỉ mới
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div id="shipping-info" class="alert alert-info d-none mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-route"></i> Khoảng cách: <strong id="distance-display">0 km</strong></span>
                                <span>Phí vận chuyển: <strong id="shipping-cost-display" class="text-primary fs-5">0 ₫</strong></span>
                            </div>
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
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Phương thức thanh toán</h5>
                </div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="paymentCod" value="cod" checked>
                        <label class="form-check-label" for="paymentCod">
                            <i class="fas fa-money-bill-wave text-success"></i> Thanh toán khi nhận hàng (COD)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="paymentMethod" id="paymentMomo" value="momo">
                        <label class="form-check-label" for="paymentMomo">
                            <i class="fas fa-qrcode text-pink" style="color: #A50064;"></i> Ví MoMo / Quét mã QR
                        </label>
                    </div>
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
        // No addresses, show default state or redirect? 
        // For now, let's keep the redirect logic but maybe improved?
        // Actually, with the new UI, we can just show "No address" state.
        
        document.getElementById('selected-address-text').textContent = 'Chưa có địa chỉ';
        document.getElementById('selected-address-text').classList.add('text-danger');
        
        // Auto open select container so they see "Add new address" link
        document.getElementById('address-select-container').classList.remove('d-none');
        document.querySelector('button[onclick="toggleAddressSelect()"]').style.display = 'none'; // Hide change button if none
        return;
    }

    let defaultAddressId = null;

    addresses.forEach(addr => {
        const option = document.createElement('option');
        option.value = addr.id;
        option.textContent = addr.address_line + (addr.is_default == 1 ? ' (Mặc định)' : '');
        select.appendChild(option);

        // Auto selection if default
        if (addr.is_default == 1) {
            defaultAddressId = addr.id;
        }
    });
    
    // If no default, pick first
    if (!defaultAddressId && addresses.length > 0) {
        defaultAddressId = addresses[0].id;
    }

    if (defaultAddressId) {
        select.value = defaultAddressId;
        onAddressChange(); // Update UI
    }
}

function toggleAddressSelect() {
    const container = document.getElementById('address-select-container');
    container.classList.toggle('d-none');
}

function onAddressChange() {
    const select = document.getElementById('shipping-address-id');
    const selectedOption = select.options[select.selectedIndex];
    const addressId = select.value;

    if (addressId) {
        // Update Card UI
        const addressText = selectedOption.textContent.replace(' (Mặc định)', '');
        document.getElementById('selected-address-text').textContent = addressText;
        document.getElementById('selected-address-text').classList.remove('text-danger');
        
        // Hide select container again for cleaner look
        document.getElementById('address-select-container').classList.add('d-none');
        
        // Recalculate Shipping
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
    const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;

    if (!addressId) {
        showAlert('Vui lòng chọn địa chỉ giao hàng', 'warning');
        return;
    }

    let itemsToOrder = selectedItems.length > 0 ? selectedItems : cartItems.map(item => item.id);

    // Show loading state
    const checkoutBtn = document.querySelector('button[onclick="confirmCheckout()"]');
    const originalText = checkoutBtn.innerText;
    checkoutBtn.innerText = 'Đang xử lý...';
    checkoutBtn.disabled = true;

    fetch(API_URL + '/orders/create', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            address_id: addressId,
            voucher_code: voucherCode,
            selected_items: itemsToOrder,
            payment_method: paymentMethod
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount();
            if (paymentMethod === 'momo' && data.payment_url) {
                window.location.href = data.payment_url;
            } else {
                window.location.href = '<?php echo BASE_URL; ?>/?page=order-confirmation&order_id=' + data.order_id;
            }
        } else {
            showAlert(data.message || 'Lỗi khi tạo đơn hàng', 'danger');
            checkoutBtn.innerText = originalText;
            checkoutBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Đã có lỗi xảy ra', 'danger');
        checkoutBtn.innerText = originalText;
        checkoutBtn.disabled = false;
    });
}
</script>
