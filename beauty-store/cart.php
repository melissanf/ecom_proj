<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = 'Cart - Beauty Shop';

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/db-error.php';

$cart = getCart();
$items = [];
$subtotal = 0;

if (!empty($cart)) {
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $db->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = [];
    foreach ($stmt->fetchAll() as $p) {
        $products[$p['id']] = $p;
    }

    foreach ($cart as $id => $row) {
        if (!isset($products[$id])) continue;
        $p = $products[$id];
        $unit = productPrice($p);
        $qty = (int)$row['quantity'];
        $line = $unit * $qty;
        $subtotal += $line;
        $items[] = [
            'product' => $p,
            'quantity' => $qty,
            'unit_price' => $unit,
            'line_total' => $line,
        ];
    }
}
?>

<div class="page-header">
    <h1>Shopping Bag</h1>
</div>

<?php if (empty($items)): ?>
    <div class="empty-state" style="text-align: center; padding: 4rem 0;">
        <p style="font-size: 1.2rem; margin-bottom: 2rem;">Your shopping bag is currently empty.</p>
        <a href="<?= e(baseUrl('shop.php')) ?>" class="btn-explore">Continue Browsing</a>
    </div>
<?php else: ?>
    <div class="cart-layout">
        <div class="cart-items">
            <?php foreach ($items as $item): ?>
                <?php $p = $item['product']; ?>
                <div class="cart-item" data-id="<?= (int)$p['id'] ?>">
                    <a href="<?= e(baseUrl('product.php?slug=' . urlencode($p['slug']))) ?>" class="cart-item-image">
                        <img src="<?= e(productImageUrl($p)) ?>" alt="<?= e($p['name']) ?>" style="width: 70%; max-height: 70%; object-fit: contain; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.1));">
                    </a>
                    <div class="cart-item-info">
                        <h3><a href="<?= e(baseUrl('product.php?slug=' . urlencode($p['slug']))) ?>"><?= e($p['name']) ?></a></h3>
                        <p class="cart-item-price"><?= formatPrice($item['unit_price']) ?></p>
                    </div>
                    <div class="cart-item-qty">
                        <input type="number" class="cart-qty-input" data-id="<?= (int)$p['id'] ?>" value="<?= (int)$item['quantity'] ?>" min="1" max="99" style="width: 60px; padding: 0.5rem; border-radius: 999px; border: 1px solid var(--glass-border); text-align: center; font-weight: 700;">
                    </div>
                    <p class="cart-item-total" style="font-weight: 800;"><?= formatPrice($item['line_total']) ?></p>
                    <button type="button" class="cart-remove" data-id="<?= (int)$p['id'] ?>" aria-label="Remove" style="font-size: 1.5rem; background: none; border: none; cursor: pointer; opacity: 0.3; transition: var(--transition);">&times;</button>
                </div>
            <?php endforeach; ?>
        </div>
        
        <aside class="cart-summary">
            <h2>Order Summary</h2>
            <div class="summary-row">
                <span>Subtotal</span>
                <span id="cartSubtotal"><?= formatPrice($subtotal) ?></span>
            </div>
            <div class="summary-row" style="font-size: 0.9rem; opacity: 0.6; font-weight: 500;">
                <span>Shipping</span>
                <span>Calculated at next step</span>
            </div>
            <div style="margin-top: 3rem;">
                <a href="<?= e(baseUrl('checkout.php')) ?>" class="btn-explore" style="width: 100%; text-align: center; margin-bottom: 1rem;">Go to Checkout</a>
                <a href="<?= e(baseUrl('shop.php')) ?>" class="btn-explore" style="width: 100%; text-align: center; background: transparent; color: var(--text-main); border: 2px solid var(--text-main);">Continue Shopping</a>
            </div>
        </aside>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
