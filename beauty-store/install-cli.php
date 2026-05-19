<?php
require_once __DIR__ . '/config/database.php';

$host = DB_HOST;
$user = DB_USER;
$pass = DB_PASS;
$dbName = DB_NAME;

echo "Beauty Store database installer\n";

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ));

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbName`");
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    $pdo->exec('DROP TABLE IF EXISTS order_items');
    $pdo->exec('DROP TABLE IF EXISTS orders');
    $pdo->exec('DROP TABLE IF EXISTS products');
    $pdo->exec('DROP TABLE IF EXISTS categories');
    $pdo->exec('DROP TABLE IF EXISTS users');

    $pdo->exec("CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(80) NOT NULL,
        slug VARCHAR(80) NOT NULL UNIQUE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT DEFAULT NULL,
        name VARCHAR(200) NOT NULL,
        slug VARCHAR(220) NOT NULL UNIQUE,
        description TEXT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        sale_price DECIMAL(10,2) DEFAULT NULL,
        image VARCHAR(255) DEFAULT 'placeholder.svg',
        image2 VARCHAR(255) DEFAULT NULL,
        image3 VARCHAR(255) DEFAULT NULL,
        stock INT NOT NULL DEFAULT 100,
        on_sale TINYINT(1) NOT NULL DEFAULT 0,
        featured TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        KEY idx_category (category_id),
        CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT DEFAULT NULL,
        customer_name VARCHAR(100) NOT NULL,
        customer_email VARCHAR(150) NOT NULL,
        shipping_address TEXT NOT NULL,
        total DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        KEY idx_user (user_id),
        CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        product_name VARCHAR(200) NOT NULL,
        quantity INT NOT NULL,
        unit_price DECIMAL(10,2) NOT NULL,
        KEY idx_order (order_id),
        KEY idx_product (product_id),
        CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        CONSTRAINT fk_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $hash = password_hash('password', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
    $stmt->execute(array('Admin', 'admin@beauty.com', $hash, 'admin'));

    $pdo->exec("INSERT INTO categories (name, slug) VALUES
        ('Serums', 'serums'),
        ('Moisturizers', 'moisturizers'),
        ('Cleansers', 'cleansers'),
        ('Sun Care', 'sun-care')");

    $products = array(
        array(1, 'Niacinamide 10 + Zinc 1', 'niacinamide-10-zinc-1', 'Vitamin blemish formula.', 12.90, 9.90, 1, 1),
        array(1, 'Hyaluronic Acid 2 + B5', 'hyaluronic-acid-2-b5', 'Hydrating formula.', 14.90, 11.90, 1, 1),
        array(1, 'Caffeine Solution 5', 'caffeine-solution-5', 'Eye contour formula.', 9.80, 7.80, 1, 1),
        array(2, 'Natural Moisturizing Factors', 'nmnf-ha', 'Surface hydration.', 11.50, null, 0, 1),
        array(2, 'Squalane Cleanser', 'squalane-cleanser', 'Gentle cleanser.', 10.90, 8.90, 1, 0),
        array(3, 'Glycolic Acid 7 Toner', 'glycolic-acid-7', 'Exfoliating toner.', 10.80, null, 0, 0),
        array(4, 'Mineral UV SPF 30', 'mineral-uv-spf30', 'Mineral sunscreen.', 18.90, 15.90, 1, 0),
        array(1, 'Retinol 0.5 Squalane', 'retinol-05', 'Anti-aging retinol.', 16.90, 13.90, 1, 0),
    );

    $ins = $pdo->prepare('INSERT INTO products (category_id, name, slug, description, price, sale_price, on_sale, featured) VALUES (?,?,?,?,?,?,?,?)');
    foreach ($products as $p) {
        $ins->execute($p);
    }

    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    $count = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
    echo "SUCCESS: $count products installed.\n";
    exit(0);
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
