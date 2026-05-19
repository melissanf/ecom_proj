<?php
if (!isset($db)) {
    header('Location: ' . baseUrl('install.php'));
    exit;
}

try {
    $db->query('SELECT 1 FROM products LIMIT 1');
} catch (Throwable $e) {
    header('Location: ' . baseUrl('install.php'));
    exit;
}
