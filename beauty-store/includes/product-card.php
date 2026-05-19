<?php
$price = productPrice($product);
$original = (float)$product['price'];
$hasSale = $product['on_sale'] && $product['sale_price'];
?>
<article class="product-card">
    <a href="<?= e(baseUrl('product.php?slug=' . urlencode($product['slug']))) ?>" style="text-decoration: none;">
        <div class="product-image-wrap" Style="padding-bottom: 3rem;">
            <div class="product-image product-bottle" data-variant="<?= (int)$product['id'] % 4 ?>">
                <img src="<?= e(productImageUrl($product)) ?>" alt="<?= e($product['name']) ?>" style="width: 130%;">
            </div>
        </div>
        <div class="product-info">
            <div style="padding-right: 4rem;">
                <h3 style="font-size: 1.1rem; font-weight: 800; letter-spacing: -0.01em; margin-bottom: 0.8rem; color: var(--text-main);"><?= e($product['name']) ?></h3>
                <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0.8rem; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical;"><?= e($product['description']) ?></p>
                <div class="product-prices">
                    <span class="price-current" style="font-weight: 900; color: var(--text-main);"><?= formatPrice($price) ?></span>
                    <?php if ($hasSale): ?>
                        <span class="price-was" style="font-size: 0.85rem; opacity: 0.3; text-decoration: line-through; margin-left: 0.8rem; font-weight: 600;"><?= formatPrice($original) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </a>
    <button type="button" class="btn-add-cart" data-id="<?= (int)$product['id'] ?>" aria-label="Add to cart"></button>
</article>
