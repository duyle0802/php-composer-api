<div class="container py-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h2 class="mb-3">Đặt hàng thành công!</h2>
                    <p class="mb-4">Cảm ơn bạn đã mua sắm tại BrightShop. Đơn hàng của bạn đã được xác nhận.</p>
                    
                    <div id="order-details" class="text-start mb-4">
                        <div class="spinner"></div>
                    </div>

                    <div class="d-flex gap-2 justify-content-center">
                        <a href="<?php echo BASE_URL; ?>/?page=home" class="btn btn-primary">Quay lại trang chủ</a>
                        <a href="<?php echo BASE_URL; ?>/?page=products" class="btn btn-secondary">Tiếp tục mua sắm</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const orderId = urlParams.get('order_id');
    
    if (orderId) {
        loadOrderDetails(orderId);
    }
});

function loadOrderDetails(orderId) {
    fetch(API_URL + '/orders/detail?order_id=' + orderId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayOrderDetails(data.order, data.items);
            }
        });
}

function displayOrderDetails(order, items) {
    let itemsHtml = '';
    items.forEach(item => {
        itemsHtml += `
            <tr>
                <td>${item.name}</td>
                <td>${formatPrice(item.price)}</td>
                <td>${item.quantity}</td>
                <td>${formatPrice(item.price * item.quantity)}</td>
            </tr>
        `;
    });

    const html = `
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Thông tin đơn hàng</h5>
                <p><strong>Mã đơn hàng:</strong> #${order.id}</p>
                <p><strong>Ngày đặt:</strong> ${new Date(order.created_at).toLocaleDateString('vi-VN')}</p>
                <p><strong>Trạng thái:</strong> <span class="badge bg-info">${order.status}</span></p>
            </div>
            <div class="col-md-6">
                <h5>Địa chỉ giao hàng</h5>
                <p>${order.shipping_address}</p>
                <p><strong>Khoảng cách:</strong> ${order.shipping_distance} km</p>
            </div>
        </div>

        <h5>Sản phẩm thanh toán</h5>
        <table class="table mb-4">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Tổng cộng</th>
                </tr>
            </thead>
            <tbody>
                ${itemsHtml}
            </tbody>
        </table>

        <div class="row">
            <div class="col-md-6">
                <p><strong>Tổng tiền hàng:</strong> ${formatPrice(order.total_amount)}</p>
                <p><strong>Phí giao hàng:</strong> ${formatPrice(order.shipping_cost)}</p>
                <p><strong>Giảm giá:</strong> ${formatPrice(order.discount_amount)}</p>
            </div>
            <div class="col-md-6">
                <h5><strong>Tổng cộng:</strong> ${formatPrice(order.total_amount + order.shipping_cost - order.discount_amount)}</h5>
            </div>
        </div>
    `;

    document.getElementById('order-details').innerHTML = html;
}
</script>
