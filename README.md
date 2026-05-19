# Beauty Store

Beauty Store is a PHP and MySQL ecommerce demo for a skincare shop. It includes a customer storefront, product catalog, session cart, checkout flow, account order history, and an admin dashboard for managing products and orders.

## Features

- Customer storefront with homepage collections, product details, search, category filters, price filters, and sale filters
- Session-based shopping cart with AJAX add, update, and remove actions
- Checkout flow with guest and logged-in customer support
- Customer accounts with order history
- Admin dashboard with product CRUD, image uploads, order list, order detail view, and status updates
- MySQL schema with users, categories, products, orders, and order items
- Responsive PHP templates with shared header, footer, product card, and admin layout

## Requirements

- PHP 8.0 or newer
- MySQL or MariaDB
- Apache through XAMPP, MAMP, Laragon, or a similar local stack
- PDO MySQL extension enabled

## Project Structure

```text
.
|-- beauty-store/
|   |-- admin/              Admin dashboard pages
|   |-- api/                JSON endpoints
|   |-- assets/             CSS, JavaScript, and images
|   |-- config/             Database configuration
|   |-- database/           Clean schema and seed data
|   |-- includes/           Shared PHP helpers and templates
|   |-- install.php         Browser database installer
|   |-- install-cli.php     CLI database installer
|   `-- index.php           Storefront homepage
`-- database.sql            Root copy of the database schema
```

## Setup

1. Copy or keep the repository in your local web root. With XAMPP on Windows, a common path is `C:\xampp\htdocs\ecom_proj`.
2. Start Apache and MySQL.
3. Check the database credentials in `beauty-store/config/database.php`.
4. Create and seed the database with one of these options:

```bash
cd beauty-store
php install-cli.php
```

Or open this page in the browser and click the install button:

```text
http://localhost/ecom_proj/beauty-store/install.php
```

You can also import `beauty-store/database/schema.sql` manually in phpMyAdmin.

## Running The App

Storefront:

```text
http://localhost/ecom_proj/beauty-store/
```

Admin dashboard:

```text
http://localhost/ecom_proj/beauty-store/admin/
```

Default admin account:

```text
Email: admin@beauty.com
Password: password
```

## Configuration

Database settings live in `beauty-store/config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'beauty_store');
define('DB_USER', 'root');
define('DB_PASS', '');
```

For default XAMPP installs, `root` with an empty password is usually correct. Change these values if your MySQL user, password, host, or database name is different.

## Cart API

All cart actions use `POST beauty-store/api/cart.php` with form data.

```text
action=add&product_id=1&quantity=1
action=update&product_id=1&quantity=3
action=remove&product_id=1
```

Responses are JSON objects containing `ok`, cart `count`, and either updated totals or an error message.

## Notes

- Uploaded product images are stored in `beauty-store/assets/images/products/`.
- The app uses PHP sessions for cart state and login state.
- The installer resets and recreates the demo database, so do not run it against data you want to keep.
