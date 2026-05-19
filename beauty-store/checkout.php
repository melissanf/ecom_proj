<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = 'Checkout - Beauty Shop';

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/db-error.php';

$cart = getCart();
if (empty($cart)) {
    redirect('cart.php');
}

$subtotal = cartTotal($db);
$errors = [];
$success = false;

$name = $_POST['name'] ?? ($_SESSION['user_name'] ?? '');
$email = $_POST['email'] ?? ($_SESSION['user_email'] ?? '');
$address = $_POST['address'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($name);
    $email = trim($email);
    $address = trim($address);

    if ($name === '') $errors[] = 'Name is required.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if ($address === '') $errors[] = 'Shipping address is required.';

    if (empty($errors)) {
        $ids = array_keys($cart);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $db->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $products = [];
        foreach ($stmt->fetchAll() as $p) {
            $products[$p['id']] = $p;
        }

        $db->beginTransaction();
        try {
            $userId = isLoggedIn() ? (int)$_SESSION['user_id'] : null;
            $stmt = $db->prepare('INSERT INTO orders (user_id, customer_name, customer_email, shipping_address, total, status) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$userId, $name, $email, $address, $subtotal, 'pending']);
            $orderId = (int)$db->lastInsertId();

            $itemStmt = $db->prepare('INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price) VALUES (?, ?, ?, ?, ?)');
            foreach ($cart as $id => $row) {
                if (!isset($products[$id])) continue;
                $p = $products[$id];
                $qty = (int)$row['quantity'];
                $unit = productPrice($p);
                $itemStmt->execute([$orderId, $p['id'], $p['name'], $qty, $unit]);

                $stockStmt = $db->prepare('UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?');
                $stockStmt->execute([$qty, $p['id']]);
            }

            $db->commit();
            $_SESSION['cart'] = [];
            $success = true;
        } catch (Exception $ex) {
            $db->rollBack();
            $errors[] = 'Could not place order. Please try again.';
        }
    }
}
?>

<div class="page-header">
    <h1>Checkout</h1>
</div>

<?php if ($success): ?>
    <div class="checkout-success">
        <h2>Order placed</h2>
        <p>Thank you, <?= e($name) ?>. We will send confirmation to <?= e($email) ?>.</p>
        <div style="display: flex; gap: 1rem; justify-content: center;">
            <a href="<?= e(baseUrl('shop.php')) ?>" class="btn-dark">Continue shopping</a>
            <?php if (isLoggedIn()): ?>
                <a href="<?= e(baseUrl('account.php')) ?>" class="btn-outline">View orders</a>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <?php if ($errors): ?>
        <div class="alert alert-error">
            <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <div class="checkout-layout">
        <form method="post" class="checkout-form">
            <h2>Shipping details</h2>
            <label for="name">Full name</label>
            <input type="text" id="name" name="name" value="<?= e($name) ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" required>

            <label for="address">Shipping address</label>
            <textarea id="address" name="address" rows="4" required><?= e($address) ?></textarea>

            <button type="submit" class="btn btn-pill btn-dark">Place order — <?= formatPrice($subtotal) ?></button>
        </form>
        <aside class="cart-summary">
            <h2>Order total</h2>
            <div class="summary-row">
                <span>Subtotal</span>
                <span><?= formatPrice($subtotal) ?></span>
            </div>
        </aside>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
