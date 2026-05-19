<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = 'About - Beauty Shop';
require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1>About us</h1>
</div>

<section class="content-page">
    <p class="lead">Beauty Shop is a demo beauty store inspired by clinical, ingredient-first skincare.</p>
    <p>We believe in transparency: every product lists its active ingredients and concentration, with honest pricing that reflects formulation cost—not marketing hype.</p>
    <p>Our catalog focuses on serums, moisturizers, cleansers, and sun care essentials designed for everyday routines. Each formula is developed to work alone or layered safely with others.</p>
    <p>This project is a PHP/MySQL ecommerce demonstration. Browse the shop, build a cart, and checkout to see the full flow in action.</p>
    <a href="<?= e(baseUrl('shop.php')) ?>" class="btn btn-pill btn-dark">Shop now</a>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
