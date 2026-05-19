# Beauty Shop

A full-featured e-commerce platform built with PHP and MySQL. This project demonstrates a complete online store implementation inspired by clinical, ingredient-focused skincare brands. The store features product browsing, user authentication, shopping cart functionality, and an admin dashboard for order and inventory management.

## Quick Start

### Prerequisites

- XAMPP (or any local development environment with Apache and MySQL)
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Installation

1. **Copy the project folder**
   - Place the project folder in `C:\xampp\htdocs\beauty-store`

2. **Start XAMPP services**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services

3. **Create and import database**
   - Open phpMyAdmin: `http://localhost/phpmyadmin/`
   - Create a new database named `beauty_store`
   - Select the database and click the "Import" tab
   - Choose the file `database/database.sql` from your project folder
   - Click "Go" to import the database schema and sample data

4. **Verify database connection**
   - Database credentials are configured in `config/database.php`
   - Default XAMPP settings: user `root`, no password, host `localhost`
   - Adjust if your setup differs

5. **Access the store**
   - Main site: `http://localhost/beauty-store/`
   - Admin panel: `http://localhost/beauty-store/admin/`

### Default Admin Account

- Email: `admin@beauty.com`
- Password: `password`

## Project Structure

```
beauty-store/
├── admin/                          # Admin dashboard
│   ├── index.php                  # Dashboard overview with stats
│   ├── products.php               # Product management
│   ├── orders.php                 # Order management
│   ├── order-details.php          # Individual order details
│   ├── product-form.php           # Product creation/editing
│   └── includes/
│       ├── admin-header.php       # Admin header template
│       └── admin-footer.php       # Admin footer template
├── api/
│   └── cart.php                   # Cart API endpoints (add, update, remove)
├── assets/
│   ├── css/
│   │   └── style.css              # Main stylesheet (layout, components, animations)
│   ├── images/
│   │   └── products/              # Product image directory
│   └── js/
│       └── main.js                # Client-side functionality (cart, animations, effects)
├── config/
│   └── database.php               # Database connection configuration
├── database/
│   └── database.sql                 # Database schema (tables, initial data)
├── includes/
│   ├── init.php                   # Global functions and session setup
│   ├── db-error.php               # Database error handling
│   ├── header.php                 # Page header template
│   ├── footer.php                 # Page footer template
│   └── product-card.php           # Product card component
├── index.php                       # Homepage with featured products
├── about.php                       # About page
├── shop.php                        # Product catalog with filtering
├── product.php                     # Individual product detail page
├── cart.php                        # Shopping cart page
├── checkout.php                    # Order checkout and placement
├── account.php                     # User account and order history
├── login.php                       # User login page
├── signup.php                      # User registration page
├── logout.php                      # User logout endpoint
├── contact.php                     # Contact form page
└── README.md                       # This file
```

## Features

### Customer Features

- **Product Browsing**: Browse products by category with detailed descriptions and pricing
- **Search & Filter**: Search products by keyword, filter by category and price range
- **Product Details**: View multiple images, descriptions, and stock availability for each product
- **Shopping Cart**: Add items to cart, adjust quantities, and view order summary
- **Checkout**: Complete purchases with shipping information
- **User Accounts**: Create account, log in, and view order history
- **Remember Me**: Option to stay signed in across sessions
- **Sales & Pricing**: Support for both regular and sale pricing on products

### Admin Features

- **Dashboard**: View key metrics including total products, orders, customers, revenue, and pending orders
- **Product Management**: Create, edit, and delete products with category assignment and image support
- **Order Management**: View all orders with customer details, totals, and status tracking
- **Status Updates**: Update order status (pending → processing → shipped → delivered or cancelled)
- **Order Details**: View individual order contents and customer information

### Technical Features

- **Responsive Design**: Modern, clean interface that works on desktop and mobile
- **Session Management**: Persistent login with remember-me functionality
- **Transaction Safety**: Database transactions for reliable order processing
- **Input Sanitization**: HTML escaping and prepared statements prevent injection attacks
- **Error Handling**: Graceful error messages and database connection checks
- **API-based Cart**: RESTful JSON API for cart operations

## Database Schema

### Users Table

Stores customer and admin account information with role-based access control.

### Products Table

Contains product listings with pricing, inventory, sale status, and featured status.

### Categories Table

Product categories for organization and filtering.

### Orders Table

Completed orders with customer information, totals, and status tracking.

### Order Items Table

Line items within each order linked to products.

## How It Works

### User Flow

1. Customer visits the homepage and browses featured products or navigates to shop
2. Uses search and filters to find specific products
3. Views product details and adds items to cart
4. Proceeds to checkout, enters shipping information
5. Order is created and stored in the database
6. Customer can create account or checkout as guest
7. User can log in to view order history on account page

### Admin Flow

1. Admin logs in with credentials
2. Accesses admin dashboard to view business metrics
3. Manages products (create, edit, delete)
4. Processes incoming orders by updating their status
5. Views detailed order information

### Technical Flow

- Pages use `includes/init.php` for global functions and session setup
- Database queries use prepared statements with PDO for security
- Cart data stored in PHP session (could be extended to database for persistence)
- Admin pages check authorization with `requireAdmin()` function
- API endpoint handles cart operations asynchronously
- Product filtering uses dynamic SQL based on user inputs

## Configuration

### Database Connection

Edit `config/database.php` to configure database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'beauty_store');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Session Handling

Session management and login state handled in `includes/init.php`:

- `isLoggedIn()` - Check if user is authenticated
- `isAdmin()` - Check if user has admin role
- `requireLogin()` - Redirect to login if not authenticated
- `requireAdmin()` - Redirect if not admin
- `tryRememberLogin()` - Auto-login using remember token

## Styling & Design

The project uses a modern, minimalist design with:

- Glass-morphism UI components with backdrop blur effects
- Smooth animations and transitions using CSS and Intersection Observer API
- Responsive grid layouts for product display
- Mobile navigation with hamburger menu toggle
- Custom CSS variables for easy theming

## API Endpoints

### Cart API (`api/cart.php`)

**Add to Cart**

```
POST api/cart.php
action=add&product_id=1&quantity=1
Response: {ok: true, count: 2, message: "Added to cart"}
```

**Update Cart**

```
POST api/cart.php
action=update&product_id=1&quantity=3
Response: {ok: true, count: 3, subtotal: 45.50}
```

**Remove from Cart**

```
POST api/cart.php
action=remove&product_id=1
Response: {ok: true, count: 1, subtotal: 15.00}
```

## Key Functions

### Core Functions (includes/init.php)

- `baseUrl()` - Generate proper URLs for the application
- `getDB()` - Get or create PDO database connection
- `isLoggedIn()` - Check authentication status
- `isAdmin()` - Check admin privileges
- `getCart()` - Retrieve current session cart
- `cartCount()` - Get total items in cart
- `cartTotal()` - Calculate cart subtotal
- `productPrice()` - Get effective price (sale or regular)
- `formatPrice()` - Format prices with currency
- `productImageUrl()` - Get product image URL with fallback
- `e()` - HTML escape output
- `setRememberCookie()` - Enable remember-me functionality
- `clearRememberCookie()` - Clear remember tokens

## Security Features

- **Prepared Statements**: All database queries use parameterized statements to prevent SQL injection
- **Password Hashing**: User passwords hashed with `PASSWORD_BCRYPT`
- **Input Validation**: Email validation, required field checks
- **HTML Escaping**: All output escaped with `htmlspecialchars()`
- **Session Security**: Remember tokens stored securely with `random_bytes()`
- **Role-Based Access**: Admin functions protected by role checks
- **HTTPS Ready**: Secure cookie flags in place

## Sample Data

- 1 admin user (<admin@beauty.com> / password)
- 4 product categories (Serums, Moisturizers, Cleansers, Sun Care)
- 7 sample products with pricing, descriptions, and sale status

## Extension Ideas

- Add product reviews and ratings
- Implement email notifications for orders
- Add discount codes and coupon system
- Integrate payment gateway (Stripe, PayPal)
- Product recommendation engine
- Wishlist functionality
- Customer support/ticket system
- Image upload for products
- Multi-currency support
- Advanced analytics dashboard

## Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance Considerations

- Database queries optimized with indexes on foreign keys
- Session-based cart reduces server load (alternatively could use database)
- CSS variables for efficient theming
- Minimal external dependencies (no heavy frameworks)
- Intersection Observer for efficient scroll animations

## License

This is a demonstration project for educational purposes.
