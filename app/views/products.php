<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="search-filter-section">
                <h5>Tìm kiếm & Lọc</h5>
                
                <div class="filter-group">
                    <label for="search-input">Tìm kiếm</label>
                    <input type="text" id="search-input" class="form-control" placeholder="Nhập tên sản phẩm...">
                </div>

                <div class="filter-group">
                    <label for="category-filter">Danh mục</label>
                    <select id="category-filter" class="form-control">
                        <option value="">Tất cả danh mục</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Giá tiền</label>
                    <div class="d-flex gap-2">
                        <input type="number" id="min-price" class="form-control" placeholder="Giá tối thiểu">
                        <input type="number" id="max-price" class="form-control" placeholder="Giá tối đa">
                    </div>
                </div>

                <button class="btn btn-primary w-100" onclick="applyFilters()">Áp dụng bộ lọc</button>
                <button class="btn btn-secondary w-100 mt-2" onclick="clearFilters()">Xóa bộ lọc</button>
            </div>
        </div>

        <div class="col-md-9">
            <div class="row" id="products">
                <div class="text-center col-12">
                    <div class="spinner"></div>
                </div>
            </div>

            <!-- Pagination -->
            <nav class="d-flex justify-content-center">
                <ul class="pagination" id="pagination">
                </ul>
            </nav>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let currentCategory = new URLSearchParams(window.location.search).get('category_id') || '';

document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    loadProducts(currentPage);
    
    // Set category filter if provided
    if (currentCategory) {
        document.getElementById('category-filter').value = currentCategory;
    }

    // Add Enter key support for search
    const searchInput = document.getElementById('search-input');
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyFilters();
            hideSuggestions();
        }
    });

    // Autocomplete Logic
    let debounceTimer;
    const suggestionsContainer = document.createElement('div');
    suggestionsContainer.className = 'suggestions-dropdown';
    searchInput.parentNode.classList.add('search-container'); // Ensure parent has relative path
    searchInput.parentNode.appendChild(suggestionsContainer);

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();
        
        if (query.length < 2) {
            hideSuggestions();
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(API_URL + '/products/suggest?q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.suggestions.length > 0) {
                        showSuggestions(data.suggestions);
                    } else {
                        hideSuggestions();
                    }
                });
        }, 300);
    });

    // Close suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
            hideSuggestions();
        }
    });

    function showSuggestions(suggestions) {
        let html = '';
        suggestions.forEach(item => {
            html += `
                <div class="suggestion-item" onclick="selectSuggestion('${item.name}')">
                    <img src="${item.image || 'https://via.placeholder.com/40'}" class="suggestion-image" alt="${item.name}">
                    <span class="suggestion-name">${item.name}</span>
                </div>
            `;
        });
        suggestionsContainer.innerHTML = html;
        suggestionsContainer.style.display = 'block';
    }

    window.selectSuggestion = function(name) {
        searchInput.value = name;
        hideSuggestions();
        applyFilters();
    };

    function hideSuggestions() {
        suggestionsContainer.style.display = 'none';
    }
});

function loadCategories() {
    fetch(API_URL + '/categories')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('category-filter');
                data.categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    select.appendChild(option);
                });
            }
        });
}

function loadProducts(page = 1) {
    const searchValue = document.getElementById('search-input')?.value || '';
    const categoryValue = document.getElementById('category-filter')?.value || '';
    const minPrice = document.getElementById('min-price')?.value || '';
    const maxPrice = document.getElementById('max-price')?.value || '';

    let url = API_URL + '/products?page=' + page;

    if (searchValue || categoryValue) {
        url = API_URL + '/products/search?page=' + page;
        if (searchValue) url += '&search=' + encodeURIComponent(searchValue);
        if (categoryValue) url += '&category_id=' + categoryValue;
    } else if (currentCategory) {
        url = API_URL + '/products/search?page=' + page + '&category_id=' + currentCategory;
    }

    if (minPrice) url += '&min_price=' + minPrice;
    if (maxPrice) url += '&max_price=' + maxPrice;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayProducts(data.products);
                displayPagination(data.pagination);
                currentPage = data.pagination.current_page;
            } else {
                document.getElementById('products').innerHTML = '<div class="col-12 text-center">Không tìm thấy sản phẩm</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('products').innerHTML = '<div class="col-12 text-center text-danger">Lỗi khi tải sản phẩm</div>';
        });
}

function displayProducts(products) {
    const container = document.getElementById('products');
    
    if (products.length === 0) {
        container.innerHTML = '<div class="col-12 text-center">Không có sản phẩm</div>';
        return;
    }
    
    let html = '';
    products.forEach(product => {
        const outOfStock = product.quantity_in_stock <= 0;
        const productClass = outOfStock ? 'out-of-stock' : '';
        
        html += `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="product-card ${productClass}">
                    <div class="product-image">
                        <img src="${product.image || 'https://via.placeholder.com/300x300?text=No+Image'}" alt="${product.name}">
                        <div class="out-of-stock-overlay">Hết hàng</div>
                    </div>
                    <div class="product-info">
                        <h5 class="product-name">${product.name}</h5>
                        <p class="product-description">${product.description ? product.description.substring(0, 100) + '...' : 'Không có mô tả'}</p>
                        <div class="product-price">${formatPrice(product.price)}</div>
                        <div class="product-actions">
                            <button class="btn btn-buy-now" ${outOfStock ? 'disabled' : ''} onclick="buyNow(${product.id})">Mua ngay</button>
                            <button class="btn btn-add-cart" ${outOfStock ? 'disabled' : ''} onclick="addToCart(${product.id}, 1)">Giỏ hàng</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function displayPagination(pagination) {
    const container = document.getElementById('pagination');
    let html = '';

    if (pagination.current_page > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadProducts(${pagination.current_page - 1}); return false;">Trước</a></li>`;
    }

    for (let i = 1; i <= pagination.total_pages; i++) {
        const active = i === pagination.current_page ? 'active' : '';
        html += `<li class="page-item ${active}"><a class="page-link" href="#" onclick="loadProducts(${i}); return false;">${i}</a></li>`;
    }

    if (pagination.current_page < pagination.total_pages) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadProducts(${pagination.current_page + 1}); return false;">Tiếp</a></li>`;
    }

    container.innerHTML = html;
}

function applyFilters() {
    currentPage = 1;
    loadProducts(1);
}

function clearFilters() {
    document.getElementById('search-input').value = '';
    document.getElementById('category-filter').value = '';
    document.getElementById('min-price').value = '';
    document.getElementById('max-price').value = '';
    currentPage = 1;
    loadProducts(1);
}

function buyNow(productId) {
    window.location.href = '<?php echo BASE_URL; ?>/?page=product-detail&id=' + productId;
}
</script>
