<div class="container py-5">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <h2 class="mb-4">Đơn hàng của tôi</h2>
            
            <div id="orders-list" class="row">
                <div class="col-12 text-center">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadUserOrders();
});

function loadUserOrders() {
    fetch(API_URL + '/orders/user')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayOrders(data.orders);
            } else {
                document.getElementById('orders-list').innerHTML = '<div class="col-12 alert alert-info">Bạn chưa có đơn hàng nào</div>';
            }
        });
}

function displayOrders(orders) {
    if (orders.length === 0) {
        document.getElementById('orders-list').innerHTML = '<div class="col-12 alert alert-info">Bạn chưa có đơn hàng nào</div>';
        return;
    }

    let html = '';
    orders.forEach(order => {
        const statusBadgeClass = {
            'pending': 'warning',
            'confirmed': 'info',
            'completed': 'success',
            'cancelled': 'danger'
        }[order.status] || 'secondary';

        html += `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Đơn hàng #${order.id}</h5>
                        <p class="card-text">
                            <strong>Ngày đặt:</strong> ${new Date(order.created_at).toLocaleDateString('vi-VN')}<br>
                            <strong>Tổng tiền:</strong> ${formatPrice(order.total_amount + order.shipping_cost - order.discount_amount)}<br>
                            <strong>Trạng thái:</strong> <span class="badge bg-${statusBadgeClass}">${order.status}</span>
                        </p>
                        <a href="<?php echo BASE_URL; ?>/?page=order-confirmation&order_id=${order.id}" class="btn btn-sm btn-primary">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        `;
    });

    document.getElementById('orders-list').innerHTML = html;
}
</script>
