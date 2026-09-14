<?php
// api/cart.php
// GET               -> list the logged-in customer's cart
// POST action=add    { product_id, quantity }
// POST action=update  { cart_id, quantity }
// POST action=remove  { cart_id }
require_once 'bootstrap.php';

$customer_id = require_auth();

function fetch_cart($pdo, $customer_id) {
    $stmt = $pdo->prepare("SELECT c.id as cart_id, c.quantity, p.id as product_id, p.product_name,
                                  p.retail_price, p.image, p.stock
                           FROM cart c
                           JOIN products p ON c.product_id = p.id
                           WHERE c.customer_id = ?
                           ORDER BY c.id DESC");
    $stmt->execute([$customer_id]);
    $rows = $stmt->fetchAll();

    $items = [];
    $total = 0;
    foreach ($rows as $r) {
        $subtotal = (float)$r['retail_price'] * (int)$r['quantity'];
        $total += $subtotal;
        $items[] = [
            'cart_id' => (int)$r['cart_id'],
            'product_id' => (int)$r['product_id'],
            'name' => $r['product_name'],
            'price' => (float)$r['retail_price'],
            'quantity' => (int)$r['quantity'],
            'stock' => (int)$r['stock'],
            'subtotal' => $subtotal,
            'image' => image_url('products', $r['image']),
        ];
    }
    return ['items' => $items, 'total' => $total];
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    json_response(fetch_cart($pdo, $customer_id));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = get_body();
    $action = $body['action'] ?? '';

    if ($action === 'add') {
        $product_id = $body['product_id'] ?? null;
        $quantity = max(1, (int)($body['quantity'] ?? 1));
        if (!$product_id) json_error('product_id is required.');

        $prod_stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ? AND status = 'Active'");
        $prod_stmt->execute([$product_id]);
        $prod = $prod_stmt->fetch();
        if (!$prod) json_error('Product not found.', 404);

        $check = $pdo->prepare("SELECT id, quantity FROM cart WHERE customer_id = ? AND product_id = ?");
        $check->execute([$customer_id, $product_id]);
        $existing = $check->fetch();

        if ($existing) {
            $new_qty = min($prod['stock'], $existing['quantity'] + $quantity);
            $update = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $update->execute([$new_qty, $existing['id']]);
        } else {
            $quantity = min($quantity, max(1, (int)$prod['stock']));
            $insert = $pdo->prepare("INSERT INTO cart (customer_id, product_id, quantity) VALUES (?, ?, ?)");
            $insert->execute([$customer_id, $product_id, $quantity]);
        }
        json_response(fetch_cart($pdo, $customer_id));

    } elseif ($action === 'update') {
        $cart_id = $body['cart_id'] ?? null;
        $quantity = (int)($body['quantity'] ?? 0);
        if (!$cart_id) json_error('cart_id is required.');

        if ($quantity > 0) {
            $update = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND customer_id = ?");
            $update->execute([$quantity, $cart_id, $customer_id]);
        } else {
            $delete = $pdo->prepare("DELETE FROM cart WHERE id = ? AND customer_id = ?");
            $delete->execute([$cart_id, $customer_id]);
        }
        json_response(fetch_cart($pdo, $customer_id));

    } elseif ($action === 'remove') {
        $cart_id = $body['cart_id'] ?? null;
        if (!$cart_id) json_error('cart_id is required.');
        $delete = $pdo->prepare("DELETE FROM cart WHERE id = ? AND customer_id = ?");
        $delete->execute([$cart_id, $customer_id]);
        json_response(fetch_cart($pdo, $customer_id));

    } else {
        json_error('Unknown action.');
    }
}

json_error('Method not allowed', 405);
