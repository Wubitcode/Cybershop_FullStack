<?php
declare(strict_types=1);

require_once __DIR__ . '/../db.php';

/**
 * CREATE ORDER: Saves the main order record
 */
function order_create(int $userId, array $items, string $status = 'pending'): int {
    try {
        $db = db();
        $total = 0.0;
        foreach ($items as $item) {
            $total += (float)$item['unitPrice'] * (int)$item['qty'];
        }

        $sql = "INSERT INTO orders (user_id, total, status, order_date) 
                VALUES (:uid, :total, :status, NOW())";
        
        $stmt = $db->prepare($sql);
        $stmt->execute(['uid' => $userId, 'total' => $total, 'status' => $status]);

        return (int)$db->lastInsertId();
    } catch (Exception $e) {
        error_log("Order Creation Failed: " . $e->getMessage());
        return 0;
    }
}

/**
 * FETCH SINGLE ORDER: This was the missing function!
 */
function order_find(int $orderId): ?array {
    try {
        $db = db();
        $sql = "SELECT o.order_id AS id, o.user_id, o.total, o.status, o.order_date AS createdAt, u.name AS customer_name 
                FROM orders o 
                JOIN users u ON o.user_id = u.user_id 
                WHERE o.order_id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        return $order ?: null;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * FETCH ALL ORDERS (For Admin)
 */
function orders_all(): array {
    $sql = "SELECT o.order_id AS id, o.total, o.status, o.order_date AS createdAt, u.name AS customer_name 
            FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY o.order_date DESC";
    return db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * FETCH USER HISTORY
 */
function orders_by_user(int $userId): array {
    $sql = "SELECT order_id AS id, total, status, order_date AS createdAt 
            FROM orders WHERE user_id = :uid ORDER BY order_date DESC";
    $stmt = db()->prepare($sql);
    $stmt->execute(['uid' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function order_get_items(int $orderId): array {
    try {
        $db = db();
        // Updated to use 'unit_price' and 'quantity'
        $sql = "SELECT 
                    oi.product_id, 
                    oi.quantity, 
                    oi.unit_price, 
                    p.name AS product_name 
                FROM order_items oi
                JOIN products p ON oi.product_id = p.product_id
                WHERE oi.order_id = :oid";
        
        $stmt = $db->prepare($sql);
        $stmt->execute(['oid' => $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Order Items Fetch Error: " . $e->getMessage());
        return [];
    }
}
/**
 * DASHBOARD STAT: Count all orders with 'pending' status
 */
function orders_count_pending(): int {
    try {
        $db = db();
        $sql = "SELECT COUNT(*) FROM orders WHERE status = 'pending'";
        return (int)$db->query($sql)->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * DASHBOARD STAT: Sum of all 'paid' or 'completed' orders
 */
function orders_total_revenue(): float {
    try {
        $db = db();
        $sql = "SELECT SUM(total) FROM orders WHERE status IN ('paid', 'completed', 'confirmed')";
        $val = $db->query($sql)->fetchColumn();
        return (float)($val ?: 0.0);
    } catch (Exception $e) {
        return 0.0;
    }
}

/**
 * DASHBOARD STAT: Fetch the N most recent orders with customer names
 */
function orders_latest_limit(int $limit = 5): array {
    try {
        $db = db();
        $sql = "SELECT o.order_id, o.total, o.status, o.order_date, u.name as customer_name 
                FROM orders o 
                JOIN users u ON o.user_id = u.user_id 
                ORDER BY o.order_date DESC 
                LIMIT :limit";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}
