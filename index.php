<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = 'BEAUTY SHOP - The Future of Beauty';

require __DIR__ . '/includes/db-error.php';
require __DIR__ . '/includes/header.php';

$stmt = $db->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.on_sale = 1 ORDER BY p.created_at DESC");
$saleProducts = $stmt->fetchAll();

$stmt = $db->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.featured = 1 ORDER BY p.created_at DESC LIMIT 4");
$bestsellers = $stmt->fetchAll();

$stmt = $db->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.category_id = 2 ORDER BY p.featured DESC, p.name LIMIT 3");
$creamProducts = $stmt->fetchAll();

$stmt = $db->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.category_id IN (3, 4) OR p.featured = 0 ORDER BY p.name LIMIT 4");
$essentials = $stmt->fetchAll();
?>

<section class="hero reveal">
    <div class="hero-content reveal-left">
        <h1 class="hero-title">Cosmetics that<br> <span class="hero-title-sub">Everyone loves! </span></h1>
        <p class="hero-subtitle">Discover the latest trends in beauty and skincare with our wide range of products.</p>
        <div class="hero-actions">
            <a href="<?= e(baseUrl('shop.php')) ?>" class="btn-explore">Explore Products</a>
        </div>
    </div>
    
    <div class="hero-visual reveal-right">
        <div class="splash-effect"></div>
        <div class="bottles-duo">
            <img src="<?= e(baseUrl('assets/images/bottle1.png')) ?>" alt="Bottle 1" class="bottle-img bottle-img--back">
            <img src="<?= e(baseUrl('assets/images/bottle2.png')) ?>" alt="Bottle 2" class="bottle-img bottle-img--front">
        </div>
    </div>

    <div class="side-social">
        <span>Follow us — Fb. / Tw. / Inst.</span>
    </div>

    <?php /* <div class="scroll-indicator">
        <div class="mouse"></div>
        <span>Scroll Down</span>
    </div>*/ ?>
</section>

<?php if (!empty($saleProducts)): ?>
<section class="section reveal reveal-up">
    <div class="section-header">
        <h2>Exclusive Offers</h2>
        <a href="<?= e(baseUrl('shop.php?on_sale=1')) ?>" class="link-arrow">Browse Sale</a>
    </div>
    <div class="product-grid">
        <?php foreach ($saleProducts as $product): ?>
            <?php include __DIR__ . '/includes/product-card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="section reveal reveal-up" style="background: #f8fafc; border-radius: var(--radius-xl);">
    <div class="cream-layout" style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 8rem; align-items: center;">
        <div class="cream-copy">
            <span class="product-category">Pure Hydration</span>
            <h2 style="font-size: 4rem; font-weight: 900; line-height: 1; margin-bottom: 2rem;">Science meets Skincare</h2>
            <p style="font-size: 1.2rem; color: var(--text-muted); margin-bottom: 4rem;">Multi-depth moisture with amino acids, fatty acids, and hyaluronic acid for lasting barrier support.</p>
            <a href="<?= e(baseUrl('shop.php?category=moisturizers')) ?>" class="btn-explore">Shop Collection</a>
        </div>
        <div class="product-grid product-grid-compact" style="grid-template-columns: 1fr 1fr; gap: 4rem;">
            <?php foreach ($creamProducts as $product): ?>
                <?php include __DIR__ . '/includes/product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section reveal reveal-up">
    <div class="section-header">
        <h2>Bestselling Products</h2>
        <a href="<?= e(baseUrl('shop.php?sort=featured')) ?>" class="link-arrow">View All</a>
    </div>
    <div class="product-grid">
        <?php foreach ($bestsellers as $product): ?>
            <?php include __DIR__ . '/includes/product-card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>

<section class="section reveal reveal-up">
    <div class="section-header">
        <h2>Daily Rituals</h2>
        <a href="<?= e(baseUrl('shop.php')) ?>" class="link-arrow">Shop All</a>
    </div>
    <div class="product-grid">
        <?php foreach ($essentials as $product): ?>
            <?php include __DIR__ . '/includes/product-card.php'; ?>
        <?php endforeach; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
