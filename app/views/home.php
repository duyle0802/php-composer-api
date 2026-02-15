<div class="container">
    <!-- Hero Section -->
    <style>
        .hero-section {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); /* Deep Blue Gradient */
            color: white;
            padding: 80px 0;
            border-radius: 20px;
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            background: linear-gradient(to right, #fff, #a5c2f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            font-weight: 300;
            max-width: 500px;
        }
        
        .hero-btn {
            background: #fff;
            color: #1e3c72;
            padding: 12px 35px;
            font-weight: 700;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            display: inline-block;
            text-decoration: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .hero-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            background: #f8f9fa;
            color: #1e3c72;
        }
        
        .hero-image-container {
            position: absolute;
            right: -5%;
            top: 50%;
            transform: translateY(-50%);
            width: 55%;
            z-index: 1;
        }
        
        .hero-image {
            width: 100%;
            /* Drop shadow for floating effect */
            filter: drop-shadow(0 20px 40px rgba(0,0,0,0.4));
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(1deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        
        /* Shape decorations */
        .decoration-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            z-index: 0;
        }
        
        @media (max-width: 991px) {
            .hero-section {
                text-align: center;
                padding: 60px 20px;
            }
            .hero-title {
                font-size: 2.5rem;
            }
            .hero-subtitle {
                margin: 0 auto 2rem auto;
            }
            .hero-image-container {
                position: relative;
                width: 80%;
                right: auto;
                top: auto;
                transform: none;
                margin: 40px auto 0 auto;
            }
        }
    </style>

    <div class="hero-section">
        <div class="decoration-circle" style="width: 300px; height: 300px; top: -100px; left: -100px;"></div>
        <div class="decoration-circle" style="width: 200px; height: 200px; bottom: 50px; right: 20%;"></div>
        
        <div class="row align-items-center position-relative z-2 px-md-5">
            <div class="col-lg-6 hero-content">
                <h1 class="hero-title">Nâng Tầm Trải Nghiệm Gaming</h1>
                <p class="hero-subtitle">Khám phá bộ sưu tập bàn phím cơ và chuột gaming cao cấp mới nhất. Thiết kế đẳng cấp, hiệu năng vượt trội.</p>
                <a href="<?php echo BASE_URL; ?>/?page=products" class="hero-btn">Mua Ngay <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <!-- Using fixed image path we just confirmed works -->
                <div class="hero-image-container">
                    <img src="<?php echo BASE_URL; ?>/public/images/products_image/bàn-phím-cơ-logitech-g-pro-x.png" class="hero-image" alt="Gaming Gear">
                </div>
            </div>
        </div>
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
    fetch(API_URL + '/categories?limit=4')
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
