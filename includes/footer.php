<footer class="site-footer">
    <div class="footer-grid">
        <div class="footer-brand">
            <span class="logo" style="color: var(--white);">BEAUTY SHOP</span>
            <p style="opacity: 0.6;">Clinical formulations with integrity. Transparent pricing, proven ingredients.</p>
        </div>
        <div>
            <h4>Shop</h4>
            <a href="<?= e(baseUrl('shop.php')) ?>">All Products</a>
            <a href="<?= e(baseUrl('shop.php?on_sale=1')) ?>">On Sale</a>
            <a href="<?= e(baseUrl('shop.php?sort=featured')) ?>">Bestsellers</a>
        </div>
        <div>
            <h4>Account</h4>
            <a href="<?= e(baseUrl('login.php')) ?>">Login</a>
            <a href="<?= e(baseUrl('signup.php')) ?>">Sign Up</a>
            <a href="<?= e(baseUrl('cart.php')) ?>">Cart</a>
        </div>
        <div>
            <h4>Support</h4>
            <a href="<?= e(baseUrl('contact.php')) ?>">Contact</a>
            <a href="<?= e(baseUrl('about.php')) ?>">About Us</a>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> Beauty Shop. Professional Edition.</p>
    </div>
</footer>
</main>
<script src="<?= e(baseUrl('assets/js/main.js')) ?>"></script>
<?php if (!empty($extraScripts)): foreach ($extraScripts as $s): ?>
<script src="<?= e(baseUrl($s)) ?>"></script>
<?php endforeach; endif; ?>
</body>
</html>
