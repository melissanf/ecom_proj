<?php
require_once __DIR__ . '/includes/init.php';

$slug = trim($_GET['slug'] ?? '');
if ($slug === '') {
    redirect('shop.php');
}

if (!isset($db)) {
    $pageTitle = 'Product - Beauty Shop';
    require __DIR__ . '/includes/header.php';
    require __DIR__ . '/includes/db-error.php';
}

$stmt = $db->prepare('SELECT p.*, c.name AS category_name, c.slug AS category_slug
    FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.slug = ?');
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Product not found';
    require __DIR__ . '/includes/header.php';
    echo '<div class="page-header"><h1>Product not found</h1><a href="' . e(baseUrl('shop.php')) . '" class="btn btn-pill btn-dark">Back to shop</a></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $product['name'] . ' - Beauty Shop';
$price = productPrice($product);
$hasSale = $product['on_sale'] && $product['sale_price'];

require __DIR__ . '/includes/header.php';
?>

<article class="product-detail reveal">
    <div class="product-detail-visual reveal-left" style="background: transparent; padding: 0; display: flex; flex-direction: column; gap: 1.5rem;">
        <?php 
            $images = [];
            if (!empty($product['image']) && $product['image'] !== 'placeholder.svg') {
                $images[] = baseUrl('assets/images/' . $product['image']);
            }
            if (!empty($product['image2'])) {
                $images[] = baseUrl('assets/images/' . $product['image2']);
            }
            if (!empty($product['image3'])) {
                $images[] = baseUrl('assets/images/' . $product['image3']);
            }
            if (empty($images)) {
                $images[] = baseUrl('assets/images/bottle1.png');
            }
        ?>
        <div style="background: #f8fafc; border-radius: var(--radius-xl); aspect-ratio: 1; display: flex; align-items: center; justify-content: center; padding: 4rem;">
            <img id="main-product-image" src="<?= e($images[0]) ?>"
                 alt="<?= e($product['name']) ?>"
                 style="max-width:340px; max-height:420px; object-fit:contain; filter:drop-shadow(0 30px 50px rgba(0,0,0,0.12)); transition: var(--transition);">
        </div>
        
        <?php if (count($images) > 1): ?>
            <div class="thumbnail-gallery" style="display: flex; gap: 1rem; justify-content: center;">
                <?php foreach ($images as $idx => $img): ?>
                    <button type="button" class="thumb-btn" onclick="document.getElementById('main-product-image').src='<?= e($img) ?>'" style="background: none; border: 2px solid <?= $idx === 0 ? 'var(--text-main)' : 'transparent' ?>; border-radius: 12px; padding: 0.5rem; cursor: pointer; transition: var(--transition);">
                        <img src="<?= e($img) ?>" alt="Thumbnail" style="width: 80px; height: 80px; object-fit: contain; border-radius: 8px; background: #f8fafc;">
                    </button>
                <?php endforeach; ?>
            </div>
            <script>
                document.querySelectorAll('.thumb-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        document.querySelectorAll('.thumb-btn').forEach(b => b.style.borderColor = 'transparent');
                        this.style.borderColor = 'var(--text-main)';
                    });
                });
            </script>
        <?php endif; ?>
    </div>
    
    <div class="product-detail-info reveal-right">
        <?php if ($product['category_name']): ?>
            <span class="product-category"><?= e($product['category_name']) ?></span>
        <?php endif; ?>
        
        <h1><?= e($product['name']) ?></h1>
        
        <div class="product-prices product-prices-lg">
            <span class="price-current"><?= formatPrice($price) ?></span>
            <?php if ($hasSale): ?>
                <span class="price-was" style="margin-left: 1rem; opacity: 0.5; text-decoration: line-through;"><?= formatPrice((float)$product['price']) ?></span>
            <?php endif; ?>
        </div>
        
        <div class="product-detail-desc">
            <?= nl2br(e($product['description'])) ?>
        </div>

        <p class="stock-info" style="font-weight: 700; color: <?= (int)$product['stock'] > 0 ? 'var(--text-main)' : '#ff4757' ?>;">
            — <?= (int)$product['stock'] > 0 ? 'Freshly in stock' : 'Out of stock' ?>
        </p>

        <form class="add-to-cart-form" data-id="<?= (int)$product['id'] ?>" style="margin-top: 3rem;">
            <label style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase;">Quantity</label>
            <div class="qty-row" style="display: flex; gap: 1rem; align-items: center; margin-top: 0.5rem;">
                <input type="number" name="quantity" value="1" min="1" max="<?= max(1, (int)$product['stock']) ?>" style="width: 80px; padding: 1.2rem; border-radius: 999px; border: 2px solid var(--glass-border); text-align: center; font-weight: 700; font-size: 1rem; outline: none; transition: var(--transition);">
                <button type="submit" class="btn-explore" style="flex: 1; border:none; cursor:pointer;" <?= (int)$product['stock'] < 1 ? 'disabled' : '' ?>>
                    <?= (int)$product['stock'] < 1 ? 'Sold Out' : 'Add to Shopping Bag' ?>
                </button>
            </div>
        </form>

        <div style="margin-top: 2rem;">
            <a href="<?= e(baseUrl('cart.php')) ?>" class="btn-outline" style="display: block; width: 100%; text-align: center;">Review your bag</a>
        </div>
    </div>
</article>

<?php require __DIR__ . '/includes/footer.php'; ?>
