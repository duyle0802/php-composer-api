<div class="container py-5">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <h2 class="mb-4 fw-bold">Đơn hàng của tôi</h2>
            
            <!-- Status Filter Tabs -->
            <ul class="nav nav-pills mb-4" id="orderStatusTabs">
                <li class="nav-item">
                    <button class="nav-link active" onclick="filterOrders('all')">Tất cả</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" onclick="filterOrders('pending')">Chờ xử lý</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" onclick="filterOrders('confirmed')">Đã xác nhận</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" onclick="filterOrders('shipping')">Đang giao</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" onclick="filterOrders('completed')">Hoàn thành</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" onclick="filterOrders('cancelled')">Đã hủy</button>
                </li>
            </ul>

            <div id="orders-list">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            
            <!-- Context for filtering -->
            <input type="hidden" id="current-filter" value="all">
        </div>
    </div>
</div>

<style>
.order-card {
    transition: all 0.2s;
    border: 1px solid #eee;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.order-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.nav-pills .nav-link {
    color: #555;
    background-color: #f8f9fa;
    margin-right: 10px;
    margin-bottom: 10px;
    border-radius: 20px;
    padding: 8px 20px;
}
.nav-pills .nav-link.active {
    background-color: #0d6efd;
    color: white;
}
.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}
</style>

<script>
let allOrders = [];

document.addEventListener('DOMContentLoaded', function() {
    loadUserOrders();
});

function loadUserOrders() {
    fetch(API_URL + '/orders/user')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allOrders = data.orders; // Store globally
                filterOrders('all'); // Initial display
            } else {
                displayOrders([]);
            }
        })
        .catch(err => {
            console.error(err);
            document.getElementById('orders-list').innerHTML = '<div class="alert alert-danger">Lỗi kết nối máy chủ</div>';
        });
}

function filterOrders(status) {
    // Update active tab styling
    document.querySelectorAll('#orderStatusTabs .nav-link').forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('onclick').includes(status)) {
            btn.classList.add('active');
        }
    });

    // Determine orders to show
    let showingOrders = [];
    if (status === 'all') {
        showingOrders = allOrders;
    } else {
        showingOrders = allOrders.filter(o => o.status === status);
    }
    
    displayOrders(showingOrders);
}

function displayOrders(orders) {
    if (orders.length === 0) {
        document.getElementById('orders-list').innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">Không tìm thấy đơn hàng nào</p>
                <a href="/?page=products" class="btn btn-outline-primary">Mua sắm ngay</a>
            </div>
        `;
        return;
    }

    let html = '';
    orders.forEach(order => {
        // Safe number parsing to fix NaN issue
        const total = parseFloat(order.total_amount || 0);
        const shipping = parseFloat(order.shipping_cost || 0);
        const discount = parseFloat(order.discount_amount || 0);
        const finalTotal = total + shipping - discount;

        // Status mapping for colors and labels
        const statusConfig = {
            'pending': { label: 'Chờ xử lý', color: 'warning', icon: 'fa-clock' },
            'confirmed': { label: 'Đã xác nhận', color: 'info', icon: 'fa-clipboard-check' },
            'shipping': { label: 'Đang giao', color: 'primary', icon: 'fa-truck' },
            'completed': { label: 'Hoàn thành', color: 'success', icon: 'fa-check-circle' },
            'cancelled': { label: 'Đã hủy', color: 'danger', icon: 'fa-times-circle' }
        };

        const config = statusConfig[order.status] || { label: order.status, color: 'secondary', icon: 'fa-info-circle' };

        html += `
            <div class="card order-card mb-3 border-0">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold fs-5">#${order.id}</span>
                            <span class="text-muted ms-2 small">${new Date(order.created_at).toLocaleDateString('vi-VN')}</span>
                        </div>
                        <span class="badge bg-${config.color} bg-opacity-10 text-${config.color} status-badge">
                            <i class="fas ${config.icon} me-1"></i> ${config.label}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <p class="mb-1 text-muted small">Địa chỉ giao hàng</p>
                            <p class="mb-0 text-truncate"><i class="fas fa-map-marker-alt text-danger me-2"></i>${order.shipping_address || 'N/A'}</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <p class="mb-1 text-muted small">Tổng thanh toán</p>
                            <h5 class="mb-0 text-primary fw-bold">${formatPrice(finalTotal)}</h5>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white py-3 border-top-0 d-flex justify-content-end gap-2">
                    ${order.status === 'pending' ? `<button class="btn btn-outline-danger btn-sm" onclick="cancelOrder(${order.id})">Hủy đơn</button>` : ''}
                    <a href="<?php echo BASE_URL; ?>/?page=order-confirmation&order_id=${order.id}" class="btn btn-primary btn-sm px-4 rounded-pill">
                        Xem chi tiết
                    </a>
                </div>
            </div>
        `;
    });

    document.getElementById('orders-list').innerHTML = html;
}
</script>
