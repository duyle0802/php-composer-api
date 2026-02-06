<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 admin-sidebar">
            <ul class="list-unstyled">
                <li><a href="#" onclick="showTab('dashboard')" class="admin-nav-link active" data-tab="dashboard"><i class="fas fa-dashboard"></i> Bảng điều khiển</a></li>
                <li><a href="#" onclick="showTab('products')" class="admin-nav-link" data-tab="products"><i class="fas fa-box"></i> Quản lý sản phẩm</a></li>
                <li><a href="#" onclick="showTab('categories')" class="admin-nav-link" data-tab="categories"><i class="fas fa-list"></i> Quản lý danh mục</a></li>
                <li><a href="#" onclick="showTab('users')" class="admin-nav-link" data-tab="users"><i class="fas fa-users"></i> Quản lý người dùng</a></li>
                <li><a href="#" onclick="showTab('orders')" class="admin-nav-link" data-tab="orders"><i class="fas fa-shopping-cart"></i> Quản lý đơn hàng</a></li>
                <li><hr></li>
                <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Dashboard Tab -->
            <div id="dashboard-tab" class="admin-tab">
                <h2 class="mb-4">Bảng điều khiển</h2>
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-uppercase mb-1">Người dùng Active</h6>
                                        <h2 id="active-users-stat" class="mb-0">0</h2>
                                    </div>
                                    <i class="fas fa-users fa-2x opacity-50"></i>
                                </div>
                                <small>On/Off status</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-uppercase mb-1">Tổng sản phẩm</h6>
                                        <h2 id="total-products-stat" class="mb-0">0</h2>
                                    </div>
                                    <i class="fas fa-box fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-uppercase mb-1">Tổng danh mục</h6>
                                        <h2 id="total-categories-stat" class="mb-0">0</h2>
                                    </div>
                                    <i class="fas fa-list fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row">
                    <!-- Order Status Pie Chart -->
                    <div class="col-md-5 mb-4">
                        <div class="card h-100">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Trạng thái đơn hàng (30 ngày)</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="orderStatusChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Bar Chart -->
                    <div class="col-md-7 mb-4">
                        <div class="card h-100">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Doanh thu (30 ngày)</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Tab -->
            <div id="products-tab" class="admin-tab" style="display: none;">
                <h2>Quản lý sản phẩm</h2>
                <div class="d-flex justify-content-between mb-3">
                    <button class="btn btn-primary" onclick="openProductModal()">Thêm sản phẩm</button>
                    <div class="d-flex gap-2">
                        <select id="product-filter-category" class="form-select" style="width: 200px;" onchange="loadProducts(1)">
                            <option value="">Tất cả danh mục</option>
                        </select>
                    </div>
                </div>
                
                <div class="admin-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên</th>
                                <th>Danh mục</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="products-list">
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Product pagination">
                    <ul class="pagination justify-content-center" id="products-pagination">
                        <!-- Pagination items will be injected here -->
                    </ul>
                </nav>
            </div>

            <!-- Categories Tab -->
            <div id="categories-tab" class="admin-tab" style="display: none;">
                <h2>Quản lý danh mục</h2>
                <button class="btn btn-primary mb-3" onclick="openCategoryModal()">Thêm danh mục</button>
                <div class="admin-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên</th>
                                <th>Mô tả</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="categories-list">
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Category pagination">
                    <ul class="pagination justify-content-center" id="categories-pagination">
                    </ul>
                </nav>
            </div>

            <!-- Users Tab -->
            <div id="users-tab" class="admin-tab" style="display: none;">
                <h2>Quản lý người dùng</h2>
                <div class="d-flex gap-3 mb-3">
                    <select id="user-filter-role" class="form-select" style="width: 200px;" onchange="loadUsers()">
                        <option value="">Tất cả vai trò</option>
                        <option value="admin">Quản trị viên</option>
                        <option value="user">Người dùng</option>
                    </select>
                    <select id="user-filter-status" class="form-select" style="width: 200px;" onchange="loadUsers()">
                        <option value="">Tất cả trạng thái</option>
                        <option value="0">Hoạt động</option>
                        <option value="1">Bị khóa</option>
                    </select>
                </div>
                <div class="admin-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên người dùng</th>
                                <th>Email</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="users-list">
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Orders Tab -->
            <div id="orders-tab" class="admin-tab" style="display: none;">
                <h2>Quản lý đơn hàng</h2>
                <div class="mb-3">
                    <select id="order-filter-status" class="form-select" style="width: 200px;" onchange="loadOrders()">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending">Chờ xử lý</option>
                        <option value="processing">Đang xử lý</option>
                        <option value="completed">Hoàn thành</option>
                        <option value="cancelled">Đã hủy</option>
                    </select>
                </div>
                <div class="admin-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Người dùng</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Ngày đặt</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="orders-list">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quản lý sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="product-form">
                    <input type="hidden" id="product-id">
                    
                    <div class="mb-3">
                        <label for="product-name" class="form-label">Tên sản phẩm</label>
                        <input type="text" class="form-control" id="product-name" required>
                    </div>

                    <div class="mb-3">
                        <label for="product-category" class="form-label">Danh mục</label>
                        <select class="form-control" id="product-category" required>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="product-price" class="form-label">Giá</label>
                        <input type="number" class="form-control" id="product-price" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label for="product-quantity" class="form-label">Số lượng</label>
                        <input type="number" class="form-control" id="product-quantity" required>
                    </div>

                    <div class="mb-3">
                        <label for="product-description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="product-description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="product-image" class="form-label">Hình ảnh URL</label>
                        <input type="text" class="form-control" id="product-image">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Lưu sản phẩm</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quản lý danh mục</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="category-form">
                    <input type="hidden" id="category-id">
                    
                    <div class="mb-3">
                        <label for="category-name" class="form-label">Tên danh mục</label>
                        <input type="text" class="form-control" id="category-name" required>
                    </div>

                    <div class="mb-3">
                        <label for="category-description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="category-description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="category-image" class="form-label">Hình ảnh URL</label>
                        <input type="text" class="form-control" id="category-image">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Lưu danh mục</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadAdminData();
    
    document.getElementById('product-form').addEventListener('submit', saveProduct);
    document.getElementById('category-form').addEventListener('submit', saveCategory);
});

function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.admin-tab').forEach(tab => {
        tab.style.display = 'none';
    });

    // Remove active class
    document.querySelectorAll('.admin-nav-link').forEach(link => {
        link.classList.remove('active');
    });

    // Show selected tab
    document.getElementById(tabName + '-tab').style.display = 'block';
    
    // Add active class
    document.querySelector('[data-tab="' + tabName + '"]').classList.add('active');

    // Load tab data
    if (tabName === 'products') loadProducts();
    if (tabName === 'categories') loadCategories();
    if (tabName === 'users') loadUsers();
    if (tabName === 'orders') loadOrders();
}

function loadAdminData() {
    // 1. Load Stats Counters
    fetch(API_URL + '/admin/stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('active-users-stat').textContent = data.stats.active_users;
                document.getElementById('total-products-stat').textContent = data.stats.total_products;
                document.getElementById('total-categories-stat').textContent = data.stats.total_categories;
            }
        });

    // Load charts
    fetch(API_URL + '/admin/charts')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderOrderStatusChart(data.charts.order_status);
                renderRevenueChart(data.charts.revenue);
            }
        });
        
    loadCategoryFilter();
}

function loadCategoryFilter() {
    fetch(API_URL + '/categories')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('product-filter-category');
                // Keep the first option (All Categories)
                select.innerHTML = '<option value="">Tất cả danh mục</option>';
                data.categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    select.appendChild(option);
                });
            }
        });
}

function renderOrderStatusChart(data) {
    const ctx = document.getElementById('orderStatusChart').getContext('2d');
    
    // Process data
    const labels = data.map(item => item.payment_status.toUpperCase());
    const values = data.map(item => item.count);
    const colors = {
        'PAID': '#28a745',
        'PENDING': '#ffc107',
        'FAILED': '#dc3545',
        'COD': '#17a2b8'
    };
    const bgColors = labels.map(label => colors[label] || '#6c757d');

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: bgColors,
                hoverOffset: 4
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function renderRevenueChart(data) {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Process data - ensure continuous dates if needed, for now just plot raw
    // In production, we should fill missing dates with 0
    const labels = data.map(item => new Date(item.date).toLocaleDateString('vi-VN'));
    const values = data.map(item => item.revenue);

    new Chart(ctx, {
        type: 'bar', // Mixed chart support
        data: {
            labels: labels,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: values,
                backgroundColor: 'rgba(78, 115, 223, 0.5)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1,
                barPercentage: 0.5
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
                        }
                    }
                }
            }
        }
    });
}

function loadProducts(page = 1) {
    const categoryId = document.getElementById('product-filter-category').value;
    let url = API_URL + '/products?limit=7&page=' + page;
    if (categoryId) {
        url += '&category_id=' + categoryId;
    }

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayProducts(data.products);
                renderPagination('products-pagination', data.pagination, loadProducts);
            }
        });
}

function renderPagination(elementId, pagination, callback) {
    const container = document.getElementById(elementId);
    let html = '';
    
    // Prev
    const prevDisabled = pagination.current_page === 1 ? 'disabled' : '';
    html += `<li class="page-item ${prevDisabled}">
                <a class="page-link" href="#" onclick="${callback.name}(${pagination.current_page - 1})">Trước</a>
             </li>`;

    // Numbers
    for (let i = 1; i <= pagination.total_pages; i++) {
        const active = i === pagination.current_page ? 'active' : '';
        html += `<li class="page-item ${active}">
                    <a class="page-link" href="#" onclick="${callback.name}(${i})">${i}</a>
                 </li>`;
    }

    // Next
    const nextDisabled = pagination.current_page === pagination.total_pages ? 'disabled' : '';
    html += `<li class="page-item ${nextDisabled}">
                <a class="page-link" href="#" onclick="${callback.name}(${pagination.current_page + 1})">Sau</a>
             </li>`;

    container.innerHTML = html;
}

function displayProducts(products) {
    let html = '';
    products.forEach(product => {
        html += `
            <tr>
                <td>${product.id}</td>
                <td>${product.name}</td>
                <td>${product.category_name}</td>
                <td>${formatPrice(product.price)}</td>
                <td>${product.quantity_in_stock}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="editProduct(${product.id})">Sửa</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.id})">Xóa</button>
                </td>
            </tr>
        `;
    });
    document.getElementById('products-list').innerHTML = html;
}

function loadCategories(page = 1) {
    fetch(API_URL + '/categories?limit=7&page=' + page)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayCategories(data.categories);
                renderPagination('categories-pagination', data.pagination, loadCategories);
            }
        });
}

function displayCategories(categories) {
    let html = '';
    categories.forEach(category => {
        html += `
            <tr>
                <td>${category.id}</td>
                <td>${category.name}</td>
                <td>${category.description || '-'}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="editCategory(${category.id})">Sửa</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteCategory(${category.id})">Xóa</button>
                </td>
            </tr>
        `;
    });
    document.getElementById('categories-list').innerHTML = html;
}

function loadUsers() {
    const role = document.getElementById('user-filter-role').value;
    const status = document.getElementById('user-filter-status').value;
    
    let url = API_URL + '/users/all';
    const params = [];
    if (role) params.push('role=' + role);
    if (status !== '') params.push('is_banned=' + status);
    
    if (params.length > 0) {
        url += '?' + params.join('&');
    }

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayUsers(data.users);
            }
        });
}

function displayUsers(users) {
    let html = '';
    users.forEach(user => {
        const statusBadge = user.is_banned ? '<span class="badge bg-danger">Bị khóa</span>' : '<span class="badge bg-success">Hoạt động</span>';
        html += `
            <tr>
                <td>${user.id}</td>
                <td>${user.username}</td>
                <td>${user.email}</td>
                <td>${user.role}</td>
                <td>${statusBadge}</td>
                <td>
                    ${user.is_banned ? `<button class="btn btn-sm btn-success" onclick="unbanUser(${user.id})">Mở khóa</button>` : `<button class="btn btn-sm btn-warning" onclick="banUser(${user.id})">Khóa</button>`}
                    <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">Xóa</button>
                </td>
            </tr>
        `;
    });
    document.getElementById('users-list').innerHTML = html;
}

function loadOrders() {
    const status = document.getElementById('order-filter-status').value;
    let url = API_URL + '/orders/all';
    if (status) {
        url += '?status=' + status;
    }

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayOrders(data.orders);
            }
        });
}

function displayOrders(orders) {
    let html = '';
    orders.forEach(order => {
        // Determine allowed next statuses based on current status
        let nextStatusOptions = '';
        const statusMap = {
            'pending': { color: 'warning', label: 'Chờ xử lý', next: ['confirmed', 'cancelled'] },
            'confirmed': { color: 'info', label: 'Đã xác nhận', next: ['shipping', 'cancelled'] },
            'shipping': { color: 'primary', label: 'Đang giao', next: ['completed'] },
            'completed': { color: 'success', label: 'Hoàn thành', next: [] },
            'cancelled': { color: 'danger', label: 'Đã hủy', next: [] }
        };

        const currentConfig = statusMap[order.status] || { color: 'secondary', label: order.status, next: [] };
        
        if (currentConfig.next.length > 0) {
            nextStatusOptions = `<div class="btn-group">
                <button type="button" class="btn btn-sm btn-${currentConfig.color} dropdown-toggle" data-bs-toggle="dropdown">
                    ${currentConfig.label}
                </button>
                <ul class="dropdown-menu">`;
            
            currentConfig.next.forEach(status => {
                const nextLabel = statusMap[status]?.label || status;
                nextStatusOptions += `<li><a class="dropdown-item" href="#" onclick="promptStatusChange(${order.id}, '${status}')">Chuyển sang: ${nextLabel}</a></li>`;
            });

            nextStatusOptions += `</ul></div>`;
        } else {
            nextStatusOptions = `<span class="badge bg-${currentConfig.color}">${currentConfig.label}</span>`;
        }

        html += `
            <tr>
                <td>#${order.id}</td>
                <td>${order.username || order.user_id}</td>
                <td>${formatPrice(order.total_amount)}</td>
                <td>${nextStatusOptions}</td>
                <td>${new Date(order.created_at).toLocaleDateString('vi-VN')}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="viewOrder(${order.id})">Xem</button>
                </td>
            </tr>
        `;
    });
    document.getElementById('orders-list').innerHTML = html;
}

function openProductModal() {
    document.getElementById('product-form').reset();
    document.getElementById('product-id').value = '';
    
    // Load categories
    fetch(API_URL + '/categories')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('product-category');
                select.innerHTML = '';
                data.categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    select.appendChild(option);
                });
            }
        });

    new bootstrap.Modal(document.getElementById('productModal')).show();
}

function openCategoryModal() {
    document.getElementById('category-form').reset();
    document.getElementById('category-id').value = '';
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}

function saveProduct(e) {
    e.preventDefault();

    const id = document.getElementById('product-id').value;
    const data = {
        name: document.getElementById('product-name').value,
        category_id: document.getElementById('product-category').value,
        price: document.getElementById('product-price').value,
        quantity_in_stock: document.getElementById('product-quantity').value,
        description: document.getElementById('product-description').value,
        image: document.getElementById('product-image').value
    };

    const endpoint = id ? API_URL + '/products/update' : API_URL + '/products/create';
    
    if (id) data.id = id;

    fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Sản phẩm đã được lưu!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
            loadProducts();
        } else {
            showAlert(data.message || 'Lỗi khi lưu sản phẩm', 'danger');
        }
    });
}

function saveCategory(e) {
    e.preventDefault();

    const id = document.getElementById('category-id').value;
    const data = {
        name: document.getElementById('category-name').value,
        description: document.getElementById('category-description').value,
        image: document.getElementById('category-image').value
    };

    const endpoint = id ? API_URL + '/categories/update' : API_URL + '/categories/create';
    
    if (id) data.id = id;

    fetch(endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Danh mục đã được lưu!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
            loadCategories();
        } else {
            showAlert(data.message || 'Lỗi khi lưu danh mục', 'danger');
        }
    });
}

function editProduct(id) {
    // 1. Reset form
    document.getElementById('product-form').reset();
    document.getElementById('product-id').value = id;

    // 2. Load categories first
    fetch(API_URL + '/categories')
        .then(response => response.json())
        .then(catData => {
            if (catData.success) {
                const select = document.getElementById('product-category');
                select.innerHTML = '';
                catData.categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    select.appendChild(option);
                });

                // 3. Load product details
                fetch(API_URL + '/products/detail?id=' + id)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const p = data.product;
                            document.getElementById('product-name').value = p.name;
                            document.getElementById('product-category').value = p.category_id;
                            document.getElementById('product-price').value = p.price;
                            document.getElementById('product-quantity').value = p.quantity_in_stock;
                            document.getElementById('product-description').value = p.description || '';
                            document.getElementById('product-image').value = p.image || '';
                            
                            // 4. Show modal
                            new bootstrap.Modal(document.getElementById('productModal')).show();
                        } else {
                            showAlert('Không thể tải thông tin sản phẩm', 'danger');
                        }
                    });
            }
        });
}

function editCategory(id) {
    document.getElementById('category-form').reset();
    document.getElementById('category-id').value = id;

    fetch(API_URL + '/categories/detail?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const c = data.category;
                document.getElementById('category-name').value = c.name;
                document.getElementById('category-description').value = c.description || '';
                document.getElementById('category-image').value = c.image || '';
                
                new bootstrap.Modal(document.getElementById('categoryModal')).show();
            } else {
                showAlert('Không thể tải thông tin danh mục', 'danger');
            }
        });
}

function deleteProduct(id) {
    if (confirm('Bạn chắc chắn muốn xóa sản phẩm này?')) {
        fetch(API_URL + '/products/delete?id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Sản phẩm đã được xóa!', 'success');
                    loadProducts();
                } else {
                    showAlert(data.message || 'Lỗi khi xóa sản phẩm', 'danger');
                }
            });
    }
}

function deleteCategory(id) {
    if (confirm('Bạn chắc chắn muốn xóa danh mục này?')) {
        fetch(API_URL + '/categories/delete?id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Danh mục đã được xóa!', 'success');
                    loadCategories();
                } else {
                    showAlert(data.message || 'Lỗi khi xóa danh mục', 'danger');
                }
            });
    }
}

function banUser(id) {
    if (confirm('Bạn chắc chắn muốn khóa người dùng này?')) {
        fetch(API_URL + '/users/ban?id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Người dùng đã được khóa!', 'success');
                    loadUsers();
                } else {
                    showAlert(data.message || 'Lỗi khi khóa người dùng', 'danger');
                }
            });
    }
}

function unbanUser(id) {
    if (confirm('Bạn chắc chắn muốn mở khóa người dùng này?')) {
        fetch(API_URL + '/users/unban?id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Người dùng đã được mở khóa!', 'success');
                    loadUsers();
                } else {
                    showAlert(data.message || 'Lỗi khi mở khóa người dùng', 'danger');
                }
            });
    }
}

function deleteUser(id) {
    if (confirm('Bạn chắc chắn muốn xóa người dùng này?')) {
        fetch(API_URL + '/users/delete?id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Người dùng đã được xóa!', 'success');
                    loadUsers();
                } else {
                    showAlert(data.message || 'Lỗi khi xóa người dùng', 'danger');
                }
            });
    }
}

function viewOrder(id) {
    window.location.href = '<?php echo BASE_URL; ?>/?page=order-confirmation&order_id=' + id;
}

// Order Status & PIN Logic
let currentOrderId = null;
let targetStatus = null;

function promptStatusChange(orderId, newStatus) {
    currentOrderId = orderId;
    targetStatus = newStatus;
    
    // Reset PIN input
    document.getElementById('pin-input').value = '';
    
    // Show Modal
    new bootstrap.Modal(document.getElementById('pinModal')).show();
}

function confirmStatusChange() {
    const pin = document.getElementById('pin-input').value;
    
    if (!pin) {
        alert('Vui lòng nhập mã PIN');
        return;
    }

    fetch(API_URL + '/orders/status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            order_id: currentOrderId,
            status: targetStatus,
            pin: pin
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Cập nhật trạng thái thành công!', 'success');
            bootstrap.Modal.getInstance(document.getElementById('pinModal')).hide();
            loadOrders();
        } else {
            alert(data.message || 'Lỗi cập nhật trạng thái');
        }
    });
}
</script>

<!-- PIN Verification Modal -->
<div class="modal fade" id="pinModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác thực PIN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Nhập mã PIN để xác nhận thay đổi trạng thái:</p>
                <input type="password" id="pin-input" class="form-control text-center text-primary fw-bold" maxlength="4" placeholder="****" style="font-size: 24px; letter-spacing: 4px;">
                <div class="text-center mt-2 text-muted small">Mặc định: 7777</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="confirmStatusChange()">Xác nhận</button>
            </div>
        </div>
    </div>
</div>
