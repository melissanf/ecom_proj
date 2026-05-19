<?php
require_once __DIR__ . '/includes/init.php';

if (isLoggedIn()) {
    redirect(isAdmin() ? 'admin/index.php' : 'account.php');
}

$pageTitle = 'Login - Beauty Shop';
$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require __DIR__ . '/includes/db-error.php';

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = !empty($_POST['remember']);

    if ($email === '' || $password === '') {
        $errors[] = 'Email and password are required.';
    } else {
        $stmt = $db->prepare('SELECT id, name, email, password, role FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            if ($remember) {
                setRememberCookie((int)$user['id'], $user['email']);
            } else {
                clearRememberCookie();
            }

            $redirect = $_GET['redirect'] ?? '';
            if ($redirect && str_starts_with($redirect, '/')) {
                header('Location: ' . $redirect);
                exit;
            }
            redirect($user['role'] === 'admin' ? 'admin/index.php' : 'account.php');
        } else {
            $errors[] = 'Invalid email or password.';
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
        <h1>Welcome Back</h1>
        <?php if ($errors): ?>
            <div class="alert alert-error"><ul><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
        <form method="post" class="auth-form">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" placeholder="your@email.com" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>

            <label class="checkbox-label" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem; cursor: pointer;">
                <input type="checkbox" name="remember" value="1" style="width: auto; margin: 0;"> 
                <span style="font-size: 0.85rem; font-weight: 600; text-transform: none;">Keep me signed in</span>
            </label>

            <button type="submit" class="btn-explore" style="width: 100%; border: none; cursor: pointer;">Sign In</button>
        </form>
        <p class="auth-switch">New to Beauty Shop? <a href="<?= e(baseUrl('signup.php')) ?>">Create an account</a></p>
        <p class="auth-hint" style="text-align: center; margin-top: 2rem; font-size: 0.75rem; opacity: 0.5;">Admin: admin@beauty.com / password</p>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
