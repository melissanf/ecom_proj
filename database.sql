SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL,
    slug VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE products (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE orders (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE order_items (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@beauty.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

INSERT INTO categories (name, slug) VALUES
('Serums', 'serums'),
('Moisturizers', 'moisturizers'),
('Cleansers', 'cleansers'),
('Sun Care', 'sun-care');

INSERT INTO products (category_id, name, slug, description, price, sale_price, on_sale, featured) VALUES
(1, 'Niacinamide 10 + Zinc 1', 'niacinamide-10-zinc-1', 'High-strength vitamin and mineral blemish formula.', 12.90, 9.90, 1, 1),
(1, 'Hyaluronic Acid 2 + B5', 'hyaluronic-acid-2-b5', 'Hydrating formula with hyaluronic acid for plumped skin.', 14.90, 11.90, 1, 1),
(1, 'Caffeine Solution 5 + EGCG', 'caffeine-solution-5-egcg', 'Reduces eye contour pigmentation and puffiness.', 9.80, 7.80, 1, 1),
(2, 'Natural Moisturizing Factors + HA', 'nmnf-ha', 'Surface hydration with amino acids and hyaluronic acid.', 11.50, NULL, 0, 1),
(2, 'Squalane Cleanser', 'squalane-cleanser', 'Gentle cleanser that removes makeup.', 10.90, 8.90, 1, 0),
(3, 'Glycolic Acid 7 Toning Solution', 'glycolic-acid-7-toning', 'Exfoliating toner for improved radiance.', 10.80, NULL, 0, 0),
(4, 'Mineral UV Filters SPF 30', 'mineral-uv-spf30', 'Broad-spectrum mineral sunscreen for daily protection.', 18.90, 15.90, 1, 0),
(1, 'Retinol 0.5 in Squalane', 'retinol-05-squalane', 'Water-free retinol for smoother skin texture.', 16.90, 13.90, 1, 0);

SET FOREIGN_KEY_CHECKS = 1;
