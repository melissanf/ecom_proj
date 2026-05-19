<?php
require_once __DIR__ . '/../includes/init.php';
$pageTitle = 'Orders';

require __DIR__ . '/includes/admin-header.php';

if (!isset($db)) {
    echo '<div class="db-error-banner"><h2>Database not connected</h2></div>';
    require __DIR__ . '/includes/admin-footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $allowed = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    $status = $_POST['status'];
    if (in_array($status, $allowed, true)) {
        $stmt = $db->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $stmt->execute([$status, (int)$_POST['order_id']]);
    }
    redirect('admin/orders.php');
}

$orders = $db->query('SELECT * FROM orders ORDER BY created_at DESC')->fetchAll();
?>

<h1>Orders</h1>

<div class="admin-table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Reference</th>
                <th>Client Details</th>
                <th>Total Value</th>
                <th>Current Status</th>
                <th>Order Date</th>
                <th>Modify Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><span style="font-weight: 800; color: var(--text-main);">#<?= (int)$order['id'] ?></span></td>
                    <td>
                        <div style="font-weight: 800;"><?= e($order['customer_name']) ?></div>
                        <div style="font-size: 0.8rem; opacity: 0.5; font-weight: 600;"><?= e($order['customer_email']) ?></div>
                    </td>
                    <td><span style="font-size: 1.2rem; font-weight: 800;"><?= formatPrice((float)$order['total']) ?></span></td>
                    <td><span class="status-badge status-<?= e($order['status']) ?>"><?= e(ucfirst($order['status'])) ?></span></td>
                    <td><span style="font-weight: 700; opacity: 0.6;"><?= e(date('M j, Y', strtotime($order['created_at']))) ?></span></td>
                    <td>
                        <form method="post" class="admin-actions" style="display: flex; gap: 1rem; align-items: center;">
                            <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                            <select name="status" style="margin: 0; padding: 0.6rem 1.2rem; font-size: 0.8rem; border-radius: 999px; background: #f1f5f9; border: none; font-weight: 700;">
                                <?php foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn-dark btn-sm" style="padding: 0.7rem 1.5rem;">Update</button>
                            <a href="<?= e(baseUrl('admin/order-details.php?id=' . $order['id'])) ?>" class="btn-outline btn-sm" style="padding: 0.7rem 1.5rem;">View</a>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
