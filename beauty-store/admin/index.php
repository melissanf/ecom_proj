<?php
require_once __DIR__ . '/../includes/init.php';
$pageTitle = 'Dashboard';

require __DIR__ . '/includes/admin-header.php';

if (!isset($db)) {
    echo '<div class="db-error-banner"><h2>Database not connected</h2><p>Import schema.sql and configure database.php.</p></div>';
    require __DIR__ . '/includes/admin-footer.php';
    exit;
}

$products = (int)$db->query('SELECT COUNT(*) FROM products')->fetchColumn();
$orders = (int)$db->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$customers = (int)$db->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$revenue = (float)$db->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
$pending = (int)$db->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
?>

<div class="admin-stats">
    <div class="stat-card">
        <div class="stat-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg></div>
        <div class="stat-info">
            <h3>Total Products</h3>
            <p class="stat-value"><?= $products ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg></div>
        <div class="stat-info">
            <h3>Orders</h3>
            <p class="stat-value"><?= $orders ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg></div>
        <div class="stat-info">
            <h3>Customers</h3>
            <p class="stat-value"><?= $customers ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></div>
        <div class="stat-info">
            <h3>Revenue</h3>
            <p class="stat-value"><?= formatPrice($revenue) ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></div>
        <div class="stat-info">
            <h3>Pending Orders</h3>
            <p class="stat-value"><?= $pending ?></p>
        </div>
    </div>
</div>



<?php require __DIR__ . '/includes/admin-footer.php'; ?>
