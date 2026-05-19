<?php
require_once __DIR__ . '/config/database.php';
header('Content-Type: text/html; charset=utf-8');

$host = DB_HOST;
$user = DB_USER;
$pass = DB_PASS;
$dbName = DB_NAME;
$messages = array();
$ok = false;
$error = false;

function runInstall($pdo, $dbName) {
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
        array(1, 'Niacinamide 10 + Zinc 1', 'niacinamide-10-zinc-1', 'Vitamin and mineral blemish formula.', 12.90, 9.90, 1, 1),
        array(1, 'Hyaluronic Acid 2 + B5', 'hyaluronic-acid-2-b5', 'Hydrating hyaluronic acid formula.', 14.90, 11.90, 1, 1),
        array(1, 'Caffeine Solution 5 + EGCG', 'caffeine-solution-5-egcg', 'Reduces eye contour pigmentation.', 9.80, 7.80, 1, 1),
        array(2, 'Natural Moisturizing Factors + HA', 'nmnf-ha', 'Surface hydration with amino acids.', 11.50, null, 0, 1),
        array(2, 'Squalane Cleanser', 'squalane-cleanser', 'Gentle cleanser that removes makeup.', 10.90, 8.90, 1, 0),
        array(3, 'Glycolic Acid 7 Toning Solution', 'glycolic-acid-7-toning', 'Exfoliating toner for radiance.', 10.80, null, 0, 0),
        array(4, 'Mineral UV Filters SPF 30', 'mineral-uv-spf30', 'Broad-spectrum mineral sunscreen.', 18.90, 15.90, 1, 0),
        array(1, 'Retinol 0.5 in Squalane', 'retinol-05-squalane', 'Water-free retinol for smoother skin.', 16.90, 13.90, 1, 0),
    );

    $ins = $pdo->prepare('INSERT INTO products (category_id, name, slug, description, price, sale_price, on_sale, featured) VALUES (?,?,?,?,?,?,?,?)');
    foreach ($products as $p) {
        $ins->execute($p);
    }

    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    return (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        $count = runInstall($pdo, $dbName);
        $messages[] = 'Success! Database installed with ' . $count . ' products.';
        $ok = true;
    } catch (Throwable $e) {
        $messages[] = $e->getMessage();
        $error = true;
    }
} else {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4", $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        $count = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
        if ($count > 0) {
            $messages[] = 'Database OK (' . $count . ' products).';
            $ok = true;
        } else {
            $messages[] = 'Click Install below.';
        }
    } catch (Throwable $e) {
        $messages[] = 'MySQL must be running in XAMPP. Then click Install.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Install Beauty Store</title>
<style>
body{font-family:Segoe UI,sans-serif;max-width:520px;margin:50px auto;padding:20px}
.msg{padding:12px;border-radius:8px;margin:12px 0}
.ok{background:#e8f5e9}.err{background:#ffebee}.info{background:#e3f2fd}
button{background:#111;color:#fff;border:0;padding:12px 24px;border-radius:999px;cursor:pointer;font-size:1rem}
</style>
</head>
<body>
<h1>Beauty Store Setup</h1>
<?php foreach ($messages as $m): ?>
<div class="msg <?php echo $error ? 'err' : ($ok ? 'ok' : 'info'); ?>"><?php echo htmlspecialchars($m); ?></div>
<?php endforeach; ?>
<?php if ($ok): ?>
<p><a href="index.php">Open store</a> | <a href="admin/">Admin</a></p>
<p>Login: admin@beauty.com / password</p>
<?php else: ?>
<form method="post"><button type="submit">Install database now</button></form>
<?php endif; ?>
</body>
</html>
