<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = 'Contact - Beauty Shop';

$sent = false;
$errors = [];
$name = '';
$email = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '') $errors[] = 'Name is required.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if ($message === '') $errors[] = 'Message is required.';

    if (empty($errors)) {
        $sent = true;
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <h1>Contact</h1>
    <p>Questions about products or orders? Send us a message.</p>
</div>

<section class="content-page contact-page">
    <?php if ($sent): ?>
        <div class="alert alert-success">
            <p>Thank you, <?= e($name) ?>. We will get back to you at <?= e($email) ?>.</p>
        </div>
    <?php else: ?>
        <?php if ($errors): ?>
            <div class="alert alert-error">
                <ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>
        <form method="post" class="contact-form">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?= e($name) ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" required>

            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" required><?= e($message) ?></textarea>

            <button type="submit" class="btn btn-pill btn-dark">Send message</button>
        </form>
    <?php endif; ?>

    <div class="contact-info">
        <h3>Store</h3>
        <p>support@beauty.com</p>
        <p>Mon–Fri, 9am–6pm EST</p>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
