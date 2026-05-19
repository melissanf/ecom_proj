<?php
require_once __DIR__ . '/includes/init.php';

if (isLoggedIn()) {
    redirect('account.php');
}

$pageTitle = 'Sign Up - Beauty Shop';
$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/includes/db-error.php';

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($name === '') $errors[] = 'Name is required.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $email, $hash, 'customer']);
            $id = (int)$db->lastInsertId();

            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['role'] = 'customer';

            redirect('account.php');
        }
    }
}

require __DIR__ . '/includes/header.php';
if (!isset($db)) {
    require __DIR__ . '/includes/db-error.php';
}
?>

<div class="auth-page reveal">
    <div class="auth-card reveal-up">
        <h1>Join Beauty Shop</h1>
        <?php if ($errors): ?>
            <div class="alert alert-error"><ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
        <form method="post" class="auth-form">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?= e($name) ?>" placeholder="Jane Doe" required>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" placeholder="your@email.com" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" minlength="6" placeholder="••••••••" required>

            <label for="confirm">Confirm Password</label>
            <input type="password" id="confirm" name="confirm" minlength="6" placeholder="••••••••" required>

            <button type="submit" class="btn-explore" style="width: 100%; border: none; cursor: pointer; margin-top: 1rem;">Create Account</button>
        </form>
        <p class="auth-switch">Already a member? <a href="<?= e(baseUrl('login.php')) ?>">Login</a></p>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
