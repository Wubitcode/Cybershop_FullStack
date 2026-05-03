<?php
declare(strict_types=1);

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function money(float $amount): string {
    return '$' . number_format($amount, 2);
}

function json_response(array $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_SLASHES);
    exit;
}

function read_json_body(): array {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

/**
 * Resolve product image paths globally.
 * Supports:
 *  - empty/null => placeholder
 *  - full URLs (https://...) => unchanged
 *  - local paths (assets/images/..., public/uploads/...) => prefixed with BASE_URL
 */
function image_url(?string $path): string
{
    $path = trim((string)$path);

    // Fallback placeholder
    if ($path === '') {
        return BASE_URL . '/assets/images/cybershop.png';
    }

    // External URL
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }

    // Already includes BASE_URL
    if (str_starts_with($path, BASE_URL)) {
        return $path;
    }

    // Local relative path
    return BASE_URL . '/' . ltrim($path, '/');
}
