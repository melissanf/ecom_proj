<?php
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$action = $_POST['action'] ?? '';
$productId = (int)($_POST['product_id'] ?? 0);
$quantity = max(1, (int)($_POST['quantity'] ?? 1));

function jsonResponse(array $data, int $code = 200): void
{
    http_response_code($code);
    echo json_encode($data);
    exit;
}

if (!isset($db)) {
    jsonResponse(['ok' => false, 'error' => 'Database not available'], 503);
}

switch ($action) {
    case 'add':
        if ($productId < 1) {
            jsonResponse(['ok' => false, 'error' => 'Invalid product'], 400);
        }
        $stmt = $db->prepare('SELECT id FROM products WHERE id = ?');
        $stmt->execute([$productId]);
        if (!$stmt->fetch()) {
            jsonResponse(['ok' => false, 'error' => 'Product not found'], 404);
        }
        $cart = getCart();
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = ['quantity' => $quantity];
        }
        $_SESSION['cart'] = $cart;
        jsonResponse(['ok' => true, 'count' => cartCount(), 'message' => 'Added to cart']);

    case 'update':
        if ($productId < 1) {
            jsonResponse(['ok' => false, 'error' => 'Invalid product'], 400);
        }
        $cart = getCart();
        if (!isset($cart[$productId])) {
            jsonResponse(['ok' => false, 'error' => 'Item not in cart'], 404);
        }
        $cart[$productId]['quantity'] = $quantity;
        $_SESSION['cart'] = $cart;
        jsonResponse(['ok' => true, 'count' => cartCount(), 'subtotal' => cartTotal($db)]);

    case 'remove':
        if ($productId < 1) {
            jsonResponse(['ok' => false, 'error' => 'Invalid product'], 400);
        }
        $cart = getCart();
        unset($cart[$productId]);
        $_SESSION['cart'] = $cart;
        jsonResponse(['ok' => true, 'count' => cartCount(), 'subtotal' => cartTotal($db)]);

    default:
        jsonResponse(['ok' => false, 'error' => 'Unknown action'], 400);
}
