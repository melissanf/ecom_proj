<?php
session_start();
require_once __DIR__ . '/../config/database.php';

function baseUrl(string $path = ''): string
{
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    if (basename($base) === 'admin' || basename($base) === 'api') {
        $base = dirname($base);
    }
    return $base . '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    header('Location: ' . baseUrl($path));
    exit;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirect('login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    }
}

function requireAdmin(): void
{
    if (!isAdmin()) {
        redirect('login.php');
    }
}

function getCart(): array
{
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    return $_SESSION['cart'];
}

function cartCount(): int
{
    $count = 0;
    foreach (getCart() as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

function cartTotal(PDO $db): float
{
    $cart = getCart();
    if (empty($cart)) return 0;
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $db->prepare("SELECT id, price, sale_price, on_sale FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_UNIQUE);
    $total = 0;
    foreach ($cart as $id => $item) {
        if (!isset($products[$id])) continue;
        $p = $products[$id];
        $price = ($p['on_sale'] && $p['sale_price']) ? (float)$p['sale_price'] : (float)$p['price'];
        $total += $price * $item['quantity'];
    }
    return $total;
}

function productPrice(array $p): float
{
    return ($p['on_sale'] && $p['sale_price']) ? (float)$p['sale_price'] : (float)$p['price'];
}

function formatPrice(float $price): string
{
    return number_format($price, 2) . ' DZD';
}

function productImageUrl(array $product): string
{
    $img = $product['image'] ?? '';
    if ($img !== '' && $img !== 'placeholder.svg') {
        return baseUrl('assets/images/' . $img);
    }
    // fallback to hero bottle
    return baseUrl('assets/images/bottle1.png');
}

function e(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function setRememberCookie(int $userId, string $email): void
{
    $token = bin2hex(random_bytes(32));
    $expires = time() + (30 * 24 * 60 * 60);
    setcookie('remember_token', $token, $expires, '/', '', false, true);
    setcookie('remember_user', (string)$userId, $expires, '/', '', false, true);
    $_SESSION['remember_token'] = $token;
    $_SESSION['remember_email'] = $email;
}

function clearRememberCookie(): void
{
    setcookie('remember_token', '', time() - 3600, '/');
    setcookie('remember_user', '', time() - 3600, '/');
    unset($_SESSION['remember_token'], $_SESSION['remember_email']);
}

function tryRememberLogin(PDO $db): void
{
    if (isLoggedIn()) return;
    if (empty($_COOKIE['remember_user']) || empty($_COOKIE['remember_token'])) return;
    if (empty($_SESSION['remember_token']) || $_COOKIE['remember_token'] !== $_SESSION['remember_token']) return;

    $stmt = $db->prepare('SELECT id, name, email, role FROM users WHERE id = ?');
    $stmt->execute([(int)$_COOKIE['remember_user']]);
    $user = $stmt->fetch();
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
    }
}

try {
    $db = getDB();
    tryRememberLogin($db);
} catch (PDOException $e) {
    // DB not ready — pages will show setup hint
}
