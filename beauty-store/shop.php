<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = 'Shop - Beauty Shop';

require __DIR__ . '/includes/db-error.php';
require __DIR__ . '/includes/header.php';

$categories = $db->query('SELECT id, name, slug FROM categories ORDER BY name')->fetchAll();

$q = trim($_GET['q'] ?? '');
$categorySlug = trim($_GET['category'] ?? '');
$priceMin = $_GET['price_min'] ?? '';
$priceMax = $_GET['price_max'] ?? '';
$onSale = isset($_GET['on_sale']) && $_GET['on_sale'] !== '' ? (int)$_GET['on_sale'] : null;
$sort = $_GET['sort'] ?? 'name';

$sql = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug
        FROM products p
        LEFT JOIN categories c ON c.id = p.category_id
        WHERE 1=1';
$params = [];

if ($q !== '') {
    $sql .= ' AND (p.name LIKE ? OR p.description LIKE ?)';
    $params[] = '%' . $q . '%';
    $params[] = '%' . $q . '%';
}
if ($categorySlug !== '') {
    $sql .= ' AND c.slug = ?';
    $params[] = $categorySlug;
}
if ($priceMin !== '' && is_numeric($priceMin)) {
    $sql .= ' AND COALESCE(NULLIF(p.sale_price, 0), p.price) >= ?';
    $params[] = (float)$priceMin;
}
if ($priceMax !== '' && is_numeric($priceMax)) {
    $sql .= ' AND COALESCE(NULLIF(p.sale_price, 0), p.price) <= ?';
    $params[] = (float)$priceMax;
}
if ($onSale === 1) {
    $sql .= ' AND p.on_sale = 1';
}

switch ($sort) {
    case 'price_asc':
        $sql .= ' ORDER BY COALESCE(p.sale_price, p.price) ASC';
        break;
    case 'price_desc':
        $sql .= ' ORDER BY COALESCE(p.sale_price, p.price) DESC';
        break;
    case 'featured':
        $sql .= ' ORDER BY p.featured DESC, p.name ASC';
        break;
    case 'newest':
        $sql .= ' ORDER BY p.created_at DESC';
        break;
    default:
        $sql .= ' ORDER BY p.name ASC';
}

$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<div class="page-header">
    <h1>Beauty Shop</h1>
    <p><?= count($products) ?> Premium Product<?= count($products) === 1 ? '' : 's' ?></p>
</div>

<div class="shop-layout">
    <aside class="shop-filters">
        <form method="get" class="filter-form">
            <h3>Refine by</h3>
            
            <label>Search</label>
            <input type="search" name="q" value="<?= e($q) ?>" placeholder="Keywords...">

            <label>Category</label>
            <select name="category">
                <option value="">All categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= e($cat['slug']) ?>" <?= $categorySlug === $cat['slug'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label>Min ($)</label>
                    <input type="number" name="price_min" step="0.01" min="0" value="<?= e((string)$priceMin) ?>" placeholder="0">
                </div>
                <div>
                    <label>Max ($)</label>
                    <input type="number" name="price_max" step="0.01" min="0" value="<?= e((string)$priceMax) ?>" placeholder="99">
                </div>
            </div>

            <label>Availability</label>
            <select name="on_sale">
                <option value="">All items</option>
                <option value="1" <?= $onSale === 1 ? 'selected' : '' ?>>On sale</option>
            </select>

            <label>Sort order</label>
            <select name="sort">
                <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Alphabetical</option>
                <option value="featured" <?= $sort === 'featured' ? 'selected' : '' ?>>Bestselling</option>
                <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest arrivals</option>
            </select>

            <button type="submit" class="btn-explore" style="width: 100%; border:none; margin-bottom: 1rem; cursor:pointer;">Apply filters</button>
            <a href="<?= e(baseUrl('shop.php')) ?>" class="btn-explore" style="width: 100%; background: #64748b; text-align: center;">Reset</a>
        </form>
    </aside>

    <div class="shop-results">
        <?php if (empty($products)): ?>
            <div class="empty-state" style="padding: 10rem 0; text-align: center;">
                <h2 style="font-size: 2.5rem; font-weight: 900; margin-bottom: 2rem;">No items found</h2>
                <p style="font-size: 1.1rem; color: var(--text-muted); margin-bottom: 4rem;">Try adjusting your filters or browsing our full collection.</p>
                <a href="<?= e(baseUrl('shop.php')) ?>" class="btn-explore">Reset all filters</a>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <?php include __DIR__ . '/includes/product-card.php'; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
