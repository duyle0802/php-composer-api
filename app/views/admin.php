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
                <h2>Bảng điều khiển</h2>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5>Tổng sản phẩm</h5>
                                <h2 id="total-products">0</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5>Tổng người dùng</h5>
                                <h2 id="total-users">0</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Tab -->
            <div id="products-tab" class="admin-tab" style="display: none;">
                <h2>Quản lý sản phẩm</h2>
                <button class="btn btn-primary mb-3" onclick="openProductModal()">Thêm sản phẩm</button>
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
            </div>

            <!-- Users Tab -->
            <div id="users-tab" class="admin-tab" style="display: none;">
                <h2>Quản lý người dùng</h2>
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
    // Load total products
    fetch(API_URL + '/products')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('total-products').textContent = data.pagination.total_items;
            }
        });

    // Load total users
    fetch(API_URL + '/users/all')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('total-users').textContent = data.users.length;
            }
        });
}

function loadProducts() {
    fetch(API_URL + '/products?page=1')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayProducts(data.products);
            }
        });
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

function loadCategories() {
    fetch(API_URL + '/categories')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayCategories(data.categories);
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
    fetch(API_URL + '/users/all')
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
    fetch(API_URL + '/orders/all')
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
        html += `
            <tr>
                <td>#${order.id}</td>
                <td>${order.username || order.user_id}</td>
                <td>${formatPrice(order.total_amount)}</td>
                <td>${order.status}</td>
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
</script>
