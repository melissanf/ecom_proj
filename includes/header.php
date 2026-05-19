<?php
if (!isset($pageTitle)) $pageTitle = 'BEAUTY SHOP - Clinical Skincare';
$cartCount = cartCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(baseUrl('assets/css/style.css')) ?>">
</head>
<body>
<main class="site-main">
<header class="site-header">
    <div class="header-inner">
        <a href="<?= e(baseUrl('index.php')) ?>" class="logo">Beauty Shop</a>
        <nav class="main-nav" id="mainNav">
            <a href="<?= e(baseUrl('index.php')) ?>" <?= basename($_SERVER['SCRIPT_NAME']) === 'index.php' ? 'class="active"' : '' ?>>Home</a>
            <a href="<?= e(baseUrl('about.php')) ?>" <?= basename($_SERVER['SCRIPT_NAME']) === 'about.php' ? 'class="active"' : '' ?>>About</a>
            <a href="<?= e(baseUrl('shop.php')) ?>" <?= basename($_SERVER['SCRIPT_NAME']) === 'shop.php' ? 'class="active"' : '' ?>>Shop</a>
            <a href="<?= e(baseUrl('contact.php')) ?>" <?= basename($_SERVER['SCRIPT_NAME']) === 'contact.php' ? 'class="active"' : '' ?>>Contact</a>
        </nav>
        <div class="header-actions">
            <?php if (isLoggedIn()): ?>
            <a href="<?= e(isAdmin() ? baseUrl('admin/index.php') : baseUrl('account.php')) ?>" class="icon-btn" title="Account">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </a>
            <?php else: ?>
            <a href="<?= e(baseUrl('login.php')) ?>" class="btn-login">Login</a>
            <?php endif; ?>
            <a href="<?= e(baseUrl('cart.php')) ?>" class="icon-btn cart-btn" title="Cart">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                <?php if ($cartCount > 0): ?><span class="cart-badge"><?= $cartCount ?></span><?php endif; ?>
            </a>
            <button class="mobile-toggle" id="mobileToggle" type="button" aria-label="Menu"><span></span><span></span><span></span></button>
        </div>
    </div>
</header>
