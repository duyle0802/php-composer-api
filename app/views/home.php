<div class="container">
    <!-- Banner Carousel -->
    <div id="bannerCarousel" class="carousel slide banner mb-5" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="2"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="<?php echo BASE_URL; ?>/public/placeholder.php?banner=1" class="d-block w-100" alt="Banner 1">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Công Nghệ Hàng Đầu</h5>
                    <p>Khám phá những sản phẩm công nghệ mới nhất</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="<?php echo BASE_URL; ?>/public/placeholder.php?banner=2" class="d-block w-100" alt="Banner 2">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Giá Cạnh Tranh</h5>
                    <p>Mua sắm với giá tốt nhất tại BrightShop</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="<?php echo BASE_URL; ?>/public/placeholder.php?banner=3" class="d-block w-100" alt="Banner 3">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Chất Lượng Đảm Bảo</h5>
                    <p>Sản phẩm chính hãng, bảo hành toàn quốc</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <!-- Featured Products Section -->
    <section class="featured-section mb-5">
        <h2 class="mb-4">Sản Phẩm Nổi Bật</h2>
        <div class="row" id="featured-products">
            <div class="text-center col-12">
                <div class="spinner"></div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section mb-5">
        <h2 class="mb-4">Danh Mục Sản Phẩm</h2>
        <div class="row" id="categories">
            <div class="text-center col-12">
                <div class="spinner"></div>
            </div>
        </div>
    </section>

    <!-- New Arrivals -->
    <section class="new-arrivals-section mb-5">
        <h2 class="mb-4">Sản Phẩm Mới Nhất</h2>
        <div class="row" id="new-arrivals">
            <div class="text-center col-12">
                <div class="spinner"></div>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load featured products
    loadFeaturedProducts();
    
    // Load categories
    loadCategories();
    
    // Load new arrivals
    loadNewArrivals();
});

function loadFeaturedProducts() {
    fetch(API_URL + '/products/featured?limit=6')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayProducts(data.products, 'featured-products');
            }
        })
        .catch(error => console.error('Error:', error));
}

function loadCategories() {
    fetch(API_URL + '/categories')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayCategories(data.categories);
            }
        })
        .catch(error => console.error('Error:', error));
}

function loadNewArrivals() {
    fetch(API_URL + '/products?page=1')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayProducts(data.products.slice(0, 6), 'new-arrivals');
            }
        })
        .catch(error => console.error('Error:', error));
}

function displayProducts(products, containerId) {
    const container = document.getElementById(containerId);
    
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

function displayCategories(categories) {
    const container = document.getElementById('categories');
    
    let html = '';
    categories.forEach(category => {
        html += `
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="category-card" onclick="goToCategory(${category.id})">
                    <div class="category-icon">
                        <i class="fas fa-cube"></i>
                    </div>
                    <h5>${category.name}</h5>
                    <p class="text-muted">${category.description || 'Xem sản phẩm'}</p>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function goToCategory(categoryId) {
    window.location.href = '<?php echo BASE_URL; ?>/?page=products&category_id=' + categoryId;
}

function buyNow(productId) {
    window.location.href = '<?php echo BASE_URL; ?>/?page=product-detail&id=' + productId;
}
</script>
