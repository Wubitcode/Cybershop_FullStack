<?php
declare(strict_types=1);

require_once __DIR__ . '/../db.php';

/**
 * 1. Fetches only active products (isActive = 1)
 */
function products_all_active(): array {
    try {
        $stmt = db()->query("SELECT * FROM products WHERE isActive = 1 ORDER BY product_id DESC");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Repo Error: " . $e->getMessage());
        return [];
    }
}

/**
 * 2. Returns the total count of all products
 */
function products_count(): int {
    try {
        $stmt = db()->query("SELECT COUNT(*) FROM products");
        return (int)$stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

/**
 * 3. Fetches all products for Admin Terminal
 */
function products_all(): array {
    return db()->query("SELECT * FROM products ORDER BY product_id DESC")->fetchAll();
}

/**
 * 4. Finds a specific product node by ID
 */
function product_find(int $id): ?array {
    $stmt = db()->prepare("SELECT * FROM products WHERE product_id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch() ?: null;
}

/**
 * 5. Deletes a product node (Warning: Permanent Erase)
 */
function product_delete(int $id): void {
    $stmt = db()->prepare("DELETE FROM products WHERE product_id = :id");
    $stmt->execute(['id' => $id]);
}

/**
 * 6. EXECUTE_PATCH: Updates an existing product using an associative array
 * This matches the logic in your admin/products/edit.php
 */
function product_update(int $id, array $data): bool {
    try {
        $db = db();
        $sql = "UPDATE products 
                SET sku = :sku, 
                    name = :name, 
                    description = :description, 
                    price = :price, 
                    stock = :stock, 
                    image = :image, 
                    isActive = :isActive 
                WHERE product_id = :id";
        
        $stmt = $db->prepare($sql);
        
        // Merge the ID into the data array for the SQL binder
        $data['id'] = $id;
        
        return $stmt->execute($data);
    } catch (Exception $e) {
        error_log("Update Protocol Failure: " . $e->getMessage());
        return false;
    }
}

/**
 * 7. INITIALIZE_NODE: Saves a new product node using an array
 */
function product_create(array $data): bool {
    try {
        $db = db();
        $sql = "INSERT INTO products (sku, name, description, price, stock, image, isActive) 
                VALUES (:sku, :name, :description, :price, :stock, :image, :isActive)";
        
        $stmt = $db->prepare($sql);
        
        // Ensure isActive is set to 1 if not provided
        if (!isset($data['isActive'])) {
            $data['isActive'] = 1;
        }
        
        return $stmt->execute($data);
    } catch (Exception $e) {
        error_log("Initialization Failure: " . $e->getMessage());
        return false;
    }
}
/**
 * ANALYTICS: Returns count of products with 0 stock
 */
function products_out_of_stock_count(): int {
    $stmt = db()->query("SELECT COUNT(*) FROM products WHERE stock <= 0");
    return (int)$stmt->fetchColumn();
}