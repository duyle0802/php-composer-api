<?php
require_once __DIR__ . '/../config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Danh sách sản phẩm (category_name => [products])
    $products = [
        'Bàn phím' => [
            ['Bàn phím Cơ Logitech G Pro X', 'Bàn phím gaming cơ cao cấp với switch nhanh', 2500, 50],
            ['Bàn phím Cơ Corsair K95 Platinum', 'Bàn phím gaming RGB quá tốt với macro keys', 3200, 30],
            ['Bàn phím Razer BlackWidow V3', 'Bàn phím cơ gaming chuyên dụng', 2800, 40],
            ['Bàn phím SteelSeries Apex Pro', 'Bàn phím cơ điều chỉnh được chiều cao', 3500, 25],
            ['Bàn phím Cherry MX Board 1.0', 'Bàn phím cơ chuyên nghiệp', 2100, 45],
            ['Bàn phím Ducky One 2 Mini', 'Bàn phím cơ mini 60% tuyệt đẹp', 2300, 35],
            ['Bàn phím Leopold FC900R', 'Bàn phím cơ cổ điển chất lượng cao', 2000, 50],
            ['Bàn phím Drop Alt Mechanical', 'Bàn phím cơ 65% cấu hình tuyệt', 2600, 28],
            ['Bàn phím Varmillo VA68M', 'Bàn phím cơ êm và yên tĩnh', 2400, 38],
            ['Bàn phím Filco Majestouch 2', 'Bàn phím cơ bền bỉ Made in Japan', 2200, 42],
        ],
        'Chuột' => [
            ['Chuột Gaming Logitech G Pro X Superlight', 'Chuột siêu nhẹ cho game thủ chuyên dụng', 1800, 60],
            ['Chuột Gaming Corsair M65 Elite', 'Chuột gaming 8000 DPI với design tuyệt', 1600, 50],
            ['Chuột Gaming Razer DeathAdder V2', 'Chuột gaming 20000 DPI huyền thoại', 1400, 70],
            ['Chuột Gaming SteelSeries Rival 3', 'Chuột gaming giá tốt 8500 DPI', 900, 80],
            ['Chuột Logitech MX Master 3S', 'Chuột chuyên dùng office cao cấp', 2500, 40],
            ['Chuột Microsoft Sculpt Comfort', 'Chuột ergonomic cho làm việc lâu', 1100, 65],
            ['Chuột Gaming Mad Catz R.A.T. 8+', 'Chuột gaming có thể tùy chỉnh độ dài', 1900, 35],
            ['Chuột Gaming PICTEK Gaming Mouse', 'Chuột gaming giá rẻ 12000 DPI', 600, 100],
            ['Chuột Wireless Logitech M570', 'Chuột không dây ergonomic', 1300, 55],
            ['Chuột Gaming Finalmouse Ultralight 2', 'Chuột siêu nhẹ chuyên competitive', 1700, 30],
        ],
        'Laptop' => [
            ['Laptop Gaming MSI GE76 Raider', 'Laptop gaming Intel i9 RTX 3080 17.3" 240Hz', 45000, 8],
            ['Laptop Gaming ASUS ROG Zephyrus G14', 'Laptop gaming mỏng nhẹ Ryzen 9 RTX 3080', 38000, 10],
            ['Laptop Gaming Razer Blade 15', 'Laptop gaming siêu mỏng Intel i7 RTX 3070', 40000, 9],
            ['Laptop Gaming Alienware m15 R6', 'Laptop gaming cao cấp Dell Intel i9', 42000, 7],
            ['Laptop Ultrabook Dell XPS 13', 'Laptop siêu mỏng nhẹ Intel i7 cấu hình cao', 25000, 15],
            ['Laptop Apple MacBook Pro 16"', 'Laptop chuyên dụng M1 Pro 16GB RAM', 48000, 6],
            ['Laptop Gaming HP OMEN 15', 'Laptop gaming Intel i7 RTX 3060', 28000, 12],
            ['Laptop Workstation Lenovo ThinkPad P15', 'Laptop chuyên dùng Xeon RTX A4000', 35000, 8],
            ['Laptop Ultrabook Asus ZenBook 14', 'Laptop siêu mỏnh Ryzen 7 16GB SSD 512GB', 18000, 20],
            ['Laptop Gaming Acer Nitro 5', 'Laptop gaming entry-level Intel i5 RTX 3050', 16000, 25],
        ],
        'Card đồ họa' => [
            ['Card đồ họa NVIDIA RTX 4090', 'Card gaming/workstation top tier 24GB GDDR6X', 30000, 5],
            ['Card đồ họa NVIDIA RTX 4080', 'Card gaming cao cấp 16GB GDDR6X', 20000, 8],
            ['Card đồ họa NVIDIA RTX 4070 Ti', 'Card gaming/workstation 12GB GDDR6X', 15000, 12],
            ['Card đồ họa NVIDIA RTX 4070', 'Card gaming 12GB GDDR6 tốt giá', 12000, 15],
            ['Card đồ họa NVIDIA RTX 4060 Ti', 'Card gaming entry-high 8GB GDDR6', 8000, 20],
            ['Card đồ họa AMD Radeon RX 7900 XTX', 'Card gaming AMD top tier 24GB GDDR6', 28000, 6],
            ['Card đồ họa AMD Radeon RX 7900 XT', 'Card gaming AMD cao cấp 20GB GDDR6', 18000, 10],
            ['Card đồ họa NVIDIA RTX A6000', 'Card workstation chuyên dụng 48GB GDDR6', 35000, 3],
            ['Card đồ họa Intel Arc A770', 'Card gaming Intel mới 16GB GDDR6', 9000, 18],
            ['Card đồ họa NVIDIA GeForce RTX 3060', 'Card gaming cũ nhưng vẫn tốt 12GB GDDR6', 6000, 25],
        ],
        'Máy trạm' => [
            ['Máy trạm Dell Precision 7960', 'Máy trạm Xeon Platinum 32 cores RAM 128GB', 80000, 2],
            ['Máy trạm HP ZStation G8', 'Máy trạm Xeon W9 56 cores RTX 6000', 95000, 1],
            ['Máy trạm Lenovo ThinkStation P7', 'Máy trạm Xeon W9 48 cores RAM 256GB', 75000, 2],
            ['Máy trạm Apple Mac Studio M2 Max', 'Máy trạm Apple 12 core GPU 64GB SSD 2TB', 55000, 3],
            ['Máy trạm ASUS ProArt Station PA148CTC', 'Máy trạm chuyên dụng Ryzen Threadripper', 45000, 4],
            ['Máy trạm Supermicro SuperWorkstation 7049GP-TNRT', 'Máy trạm server 2x Xeon Platinum', 99999, 1],
            ['Máy trạm MSI Creator P90E', 'Máy trạm gaming/creative Intel i9 RTX 4090', 38000, 5],
            ['Máy trạm Gigabyte Aero Creator', 'Máy trạm creative Intel i9 RTX 4080', 35000, 6],
            ['Máy trạm Corsair One Elite', 'Máy trạm gaming Intel i9 nước mát tích hợp', 28000, 4],
            ['Máy trạm Custom Build Workstation', 'Máy trạm tùy chỉnh 32 cores 128GB RAM', 50000, 3],
        ],
    ];

    foreach ($products as $categoryName => $items) {
        // Lấy category_id từ tên category
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
        $stmt->execute([$categoryName]);
        $catRow = $stmt->fetch();

        if (!$catRow) {
            echo "Creating category '$categoryName'...\n";
            $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            $stmt->execute([$categoryName, "Danh mục tự động cho " . $categoryName]);
            $categoryId = $pdo->lastInsertId();
        } else {
            $categoryId = $catRow['id'];
        }

        // Insert sản phẩm
        foreach ($items as $product) {
            $stmt = $pdo->prepare("
                INSERT INTO products (category_id, name, description, price, quantity_in_stock, image)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $image = strtolower(str_replace(' ', '-', $product[0])) . '.jpg';
            $stmt->execute([$categoryId, $product[0], $product[1], $product[2], $product[3], $image]);
        }

        echo "✓ Thêm 10 sản phẩm trong category: $categoryName\n";
    }

    echo "\n✅ Tạo xong 50 sản phẩm!\n";

} catch (Exception $e) {
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
}
?>
