<?php
require_once __DIR__ . '/../app/db.php';
header("Content-Type: application/json");

try {
    $pdo = db();
    // Fetch last 7 days of sales
    $sql = "SELECT DATE(created_at) as date, SUM(total_amount) as total 
            FROM orders 
            GROUP BY DATE(created_at) 
            ORDER BY date ASC LIMIT 7";
    $stmt = $pdo->query($sql);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo json_encode([]);
}