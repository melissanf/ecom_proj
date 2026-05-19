<?php
require_once __DIR__ . '/../includes/init.php';
$pageTitle = 'Products';

require __DIR__ . '/includes/admin-header.php';

if (!isset($db)) {
    echo '<div class="db-error-banner"><h2>Database not connected</h2></div>';
    require __DIR__ . '/includes/admin-footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $stmt = $db->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([(int)$_POST['delete_id']]);
    redirect('admin/products.php');
}

$products = $db->query('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.name')->fetchAll();
?>

<div class="admin-toolbar">
    <h2>All Products</h2>
    <a href="<?= e(baseUrl('admin/product-form.php')) ?>" class="btn-explore" style="padding: 0.7rem 1.8rem; font-size: 0.85rem;">+ Add product</a>
</div>

<div class="admin-table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:72px;"></th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Sale</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td style="padding:1rem 1.5rem;">
                        <img src="<?= e(productImageUrl($p)) ?>"
                             alt=""
                             style="width:56px; height:56px; object-fit:contain;
                                    border-radius:8px; background:#f8fafc;">
                    </td>
                    <td><?= e($p['name']) ?></td>
                    <td><?= e($p['category_name'] ?? '-') ?></td>
                    <td><?= formatPrice(productPrice($p)) ?></td>
                    <td><?= (int)$p['stock'] ?></td>
                    <td><?= $p['on_sale'] ? 'Yes' : 'No' ?></td>
                    <td class="admin-actions">
                        <a href="<?= e(baseUrl('admin/product-form.php?id=' . $p['id'])) ?>" class="btn-explore" style="padding: 0.4rem 1.2rem; font-size: 0.75rem; background: #64748b;">Edit</a>
                        <form method="post" style="display:inline" onsubmit="return confirm('Delete this product?');">
                            <input type="hidden" name="delete_id" value="<?= (int)$p['id'] ?>">
                            <button type="submit" class="btn-explore" style="padding: 0.4rem 1.2rem; font-size: 0.75rem; background: #ff4757; border:none; cursor:pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
