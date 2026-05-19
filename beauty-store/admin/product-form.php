<?php
require_once __DIR__ . '/../includes/init.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pageTitle = $id ? 'Edit Product' : 'Add Product';
$errors = [];
$product = [
    'name'        => '',
    'slug'        => '',
    'description' => '',
    'category_id' => '',
    'price'       => '',
    'sale_price'  => '',
    'stock'       => 100,
    'on_sale'     => 0,
    'featured'    => 0,
    'image'       => '',
    'image2'      => '',
    'image3'      => '',
];

require __DIR__ . '/includes/admin-header.php';

if (!isset($db)) {
    echo '<div class="db-error-banner"><h2>Database not connected</h2></div>';
    require __DIR__ . '/includes/admin-footer.php';
    exit;
}

$categories = $db->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();

if ($id) {
    $stmt = $db->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        redirect('admin/products.php');
    }
    $product = $row;
    $product['image2'] = $product['image2'] ?? '';
    $product['image3'] = $product['image3'] ?? '';
}

function handleImageSlot(string $field, string $current, array &$errors): string
{
    if (empty($_FILES[$field]['name'])) {
        return $current;
    }
    $file     = $_FILES[$field];
    $allowed  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $maxBytes = 5 * 1024 * 1024;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Image upload failed for {$field} (error {$file['error']}).";
        return $current;
    }
    if (!in_array($file['type'], $allowed)) {
        $errors[] = "Only JPG, PNG, WEBP or GIF allowed ({$field}).";
        return $current;
    }
    if ($file['size'] > $maxBytes) {
        $errors[] = "Image {$field} must be smaller than 5 MB.";
        return $current;
    }

    $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $fname   = uniqid('prod_', true) . '.' . $ext;
    $destDir = __DIR__ . '/../assets/images/products/';
    if (!is_dir($destDir)) mkdir($destDir, 0755, true);

    if (!move_uploaded_file($file['tmp_name'], $destDir . $fname)) {
        $errors[] = "Could not save uploaded file for {$field}.";
        return $current;
    }

    if ($current && strpos($current, 'products/') === 0) {
        $old = __DIR__ . '/../assets/images/' . $current;
        if (file_exists($old)) @unlink($old);
    }
    return 'products/' . $fname;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product['name']        = trim($_POST['name'] ?? '');
    $product['slug']        = trim($_POST['slug'] ?? '');
    $product['description'] = trim($_POST['description'] ?? '');
    $product['category_id'] = $_POST['category_id'] !== '' ? (int)$_POST['category_id'] : null;
    $product['price']       = $_POST['price'] ?? '';
    $product['sale_price']  = $_POST['sale_price'] !== '' ? $_POST['sale_price'] : null;
    $product['stock']       = (int)($_POST['stock'] ?? 0);
    $product['on_sale']     = !empty($_POST['on_sale'])  ? 1 : 0;
    $product['featured']    = !empty($_POST['featured']) ? 1 : 0;

    if ($product['name'] === '') $errors[] = 'Name is required.';
    if ($product['slug'] === '') {
        $product['slug'] = trim(strtolower(preg_replace('/[^a-z0-9]+/i', '-', $product['name'])), '-');
    }
    if ($product['description'] === '') $errors[] = 'Description is required.';
    if (!is_numeric($product['price']))  $errors[] = 'Valid price is required.';

    $img1 = handleImageSlot('image',  (string)($product['image']  ?? ''), $errors);
    $img2 = handleImageSlot('image2', (string)($product['image2'] ?? ''), $errors);
    $img3 = handleImageSlot('image3', (string)($product['image3'] ?? ''), $errors);

    if (empty($errors)) {
        if ($id) {
            $stmt = $db->prepare(
                'UPDATE products SET category_id=?, name=?, slug=?, description=?, price=?, sale_price=?,
                 stock=?, on_sale=?, featured=?, image=?, image2=?, image3=? WHERE id=?'
            );
            $stmt->execute([
                $product['category_id'], $product['name'],   $product['slug'],
                $product['description'], $product['price'],  $product['sale_price'],
                $product['stock'],       $product['on_sale'], $product['featured'],
                $img1, $img2, $img3, $id,
            ]);
        } else {
            $stmt = $db->prepare(
                'INSERT INTO products (category_id, name, slug, description, price, sale_price,
                 stock, on_sale, featured, image, image2, image3)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $product['category_id'], $product['name'],   $product['slug'],
                $product['description'], $product['price'],  $product['sale_price'],
                $product['stock'],       $product['on_sale'], $product['featured'],
                $img1, $img2, $img3,
            ]);
        }
        redirect('admin/products.php');
    }
}

function imgPreviewBlock(string $field, string $label, string $current, int $n): string
{
    $src = $current ? e(baseUrl('assets/images/' . $current)) : '';
    $display = $src ? 'block' : 'none';
    return <<<HTML
<div class="img-slot" style="flex:1; min-width:160px; text-align:center;">
    <p style="font-size:0.75rem; font-weight:800; text-transform:uppercase; letter-spacing:.1em; margin-bottom:.8rem; color:var(--text-muted);">{$label}</p>
    <img id="preview-{$field}"
         src="{$src}"
         alt="Preview {$n}"
         style="width:100%; max-width:160px; height:160px; object-fit:contain;
                background:#f8fafc; border-radius:12px; border:2px dashed #e2e8f0;
                display:{$display}; margin-bottom:.8rem;">
    <input type="file" id="{$field}" name="{$field}"
           accept="image/jpeg,image/png,image/webp,image/gif"
           style="font-size:0.78rem; width:100%;"
           onchange="previewSlot(this,'preview-{$field}')">
    <p style="font-size:.7rem; color:var(--text-muted); margin-top:.4rem;">Max 5 MB · JPG/PNG/WEBP</p>
</div>
HTML;
}
?>

<h1><?= $id ? 'Edit' : 'Add' ?> product</h1>

<?php if ($errors): ?>
    <div class="alert alert-error"><ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="admin-form">

    <div style="margin-bottom:4rem;">
        <label for="name">Product Name</label>
        <input type="text" id="name" name="name" value="<?= e($product['name']) ?>" placeholder="e.g. Daily Face Wash" required>

        <label for="slug">URL Slug</label>
        <input type="text" id="slug" name="slug" value="<?= e($product['slug']) ?>" placeholder="auto-generated-if-empty">

        <label for="category_id">Collection / Category</label>
        <select id="category_id" name="category_id">
            <option value="">No Collection</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['id'] ?>" <?= (string)$product['category_id'] === (string)$cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="description">Product Description</label>
        <textarea id="description" name="description" rows="8" placeholder="Tell the story of this product..." required><?= e($product['description']) ?></textarea>

        <label style="margin-bottom:1.2rem; display:block;">Product Images <span style="font-weight:500; text-transform:none; letter-spacing:0; opacity:.6;">(up to 3 — first is the main image)</span></label>
        <div style="display:flex; gap:2rem; flex-wrap:wrap; margin-bottom:2rem; padding:2rem; background:#f8fafc; border-radius:var(--radius-md);">
            <?= imgPreviewBlock('image',  'Main Image',  (string)($product['image']  ?? ''), 1) ?>
            <?= imgPreviewBlock('image2', 'Image 2',     (string)($product['image2'] ?? ''), 2) ?>
            <?= imgPreviewBlock('image3', 'Image 3',     (string)($product['image3'] ?? ''), 3) ?>
        </div>
    </div>

    <div class="form-row">
        <div>
            <label for="price">Standard Price ($)</label>
            <input type="number" step="0.01" id="price" name="price" value="<?= e((string)$product['price']) ?>" required>
        </div>
        <div>
            <label for="sale_price">Promotional Price ($)</label>
            <input type="number" step="0.01" id="sale_price" name="sale_price" value="<?= e((string)($product['sale_price'] ?? '')) ?>" placeholder="Leave blank for no sale">
        </div>
    </div>

    <div class="form-row" style="margin-bottom:4rem;">
        <div>
            <label for="stock">Inventory Level</label>
            <input type="number" id="stock" name="stock" value="<?= (int)$product['stock'] ?>">
        </div>
        <div style="padding-top:3rem; display:flex; flex-direction:column; gap:1rem;">
            <label class="checkbox-label">
                <input type="checkbox" name="on_sale" value="1" <?= $product['on_sale'] ? 'checked' : '' ?>> Activate Sale
            </label>
            <label class="checkbox-label">
                <input type="checkbox" name="featured" value="1" <?= $product['featured'] ? 'checked' : '' ?>> Feature on Homepage
            </label>
        </div>
    </div>

    <div style="display:flex; gap:2rem;">
        <button type="submit" class="btn-dark"><?= $id ? 'Update Product' : 'Save Product' ?></button>
        <a href="<?= e(baseUrl('admin/products.php')) ?>" class="btn-outline">Discard Changes</a>
    </div>
</form>

<script>
function previewSlot(input, previewId) {
    const file = input.files[0];
    if (!file) return;
    const img = document.getElementById(previewId);
    img.src = URL.createObjectURL(file);
    img.style.display = 'block';
}
</script>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
