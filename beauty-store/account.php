<?php
require_once __DIR__ . '/includes/init.php';
requireLogin();
$pageTitle = 'My Account - Beauty Shop';

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/db-error.php';

$stmt = $db->prepare('SELECT * FROM orders WHERE user_id = ? OR customer_email = ? ORDER BY created_at DESC');
$stmt->execute([(int)$_SESSION['user_id'], $_SESSION['user_email']]);
$orders = $stmt->fetchAll();
?>

<div class="page-header">
    <h1>My account</h1>
    <p>Welcome, <?= e($_SESSION['user_name']) ?></p>
</div>

<section class="account-section">
    <h2>Order history</h2>
    <?php if (empty($orders)): ?>
        <p class="empty-state">You have not placed any orders yet.</p>
        <a href="<?= e(baseUrl('shop.php')) ?>" class="btn btn-pill btn-dark">Start shopping</a>
    <?php else: ?>
        <div class="orders-table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?= (int)$order['id'] ?></td>
                            <td><?= e(date('M j, Y', strtotime($order['created_at']))) ?></td>
                            <td><?= formatPrice((float)$order['total']) ?></td>
                            <td><span class="status-badge status-<?= e($order['status']) ?>"><?= e(ucfirst($order['status'])) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <div style="margin-top: 3rem; display: flex; justify-content: flex-end;">
        <a href="<?= e(baseUrl('logout.php')) ?>" class="btn-outline" style="color: var(--error); border-color: var(--error); padding: 0.8rem 2rem;">Log out</a>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
