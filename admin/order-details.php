<?php
require_once __DIR__ . '/../includes/init.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    redirect('admin/orders.php');
}

$stmt = $db->prepare('SELECT * FROM orders WHERE id = ?');
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    redirect('admin/orders.php');
}

$pageTitle = 'Order #' . $order['id'];
require __DIR__ . '/includes/admin-header.php';

$stmt = $db->prepare('SELECT * FROM order_items WHERE order_id = ?');
$stmt->execute([$id]);
$items = $stmt->fetchAll();
?>

<div class="admin-toolbar">
    <h2>Order #<?= (int)$order['id'] ?> Details</h2>
    <a href="<?= e(baseUrl('admin/orders.php')) ?>" class="btn-outline" style="padding: 0.7rem 1.8rem; font-size: 0.85rem;">Back to Orders</a>
</div>

<div class="order-info-grid">
    <div class="info-card">
        <h3>Customer Details</h3>
        <div class="info-row"><span class="info-label">Name</span> <span class="info-val"><?= e($order['customer_name']) ?></span></div>
        <div class="info-row"><span class="info-label">Email</span> <span class="info-val"><?= e($order['customer_email']) ?></span></div>
    </div>
    <div class="info-card">
        <h3>Order Status</h3>
        <div class="info-row"><span class="info-label">Date</span> <span class="info-val" style="opacity: 0.8;"><?= e(date('M j, Y, g:i a', strtotime($order['created_at']))) ?></span></div>
        <div class="info-row"><span class="info-label">Status</span> <span class="info-val"><span class="status-badge status-<?= e($order['status']) ?>"><?= e(ucfirst($order['status'])) ?></span></span></div>
        <div class="info-row"><span class="info-label">Total</span> <span class="info-val" style="font-size: 1.2rem;"><?= formatPrice((float)$order['total']) ?></span></div>
    </div>
    <div class="info-card">
        <h3>Shipping Address</h3>
        <div style="line-height: 1.8; font-weight: 600; color: var(--text-muted); margin-top: 0.5rem;">
            <?= nl2br(e($order['shipping_address'])) ?>
        </div>
    </div>
</div>

<div class="admin-table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Line Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td style="font-weight: 700;"><?= e($item['product_name']) ?></td>
                    <td><?= (int)$item['quantity'] ?></td>
                    <td><?= formatPrice((float)$item['unit_price']) ?></td>
                    <td style="font-weight: 800;"><?= formatPrice((float)$item['unit_price'] * (int)$item['quantity']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
