<?php
declare(strict_types=1);
require_once __DIR__ . '/../_bootstrap.php';
require_admin();

$data = read_json_body();
$sku = trim($data['sku'] ?? '');
$name = trim($data['name'] ?? '');
$price = (float)($data['price'] ?? 0);
$stock = (int)($data['stock'] ?? 0);

if ($sku==='' || $name==='' || $price<=0) {
    json_response(['ok'=>false,'error'=>'sku, name, price required'], 400);
}

$id = product_create([
  'sku'=>$sku,
  'name'=>$name,
  'description'=> (string)($data['description'] ?? ''),
  'price'=>$price,
  'stock'=>$stock,
  'imageUrl'=> (string)($data['imageUrl'] ?? ''),
  'isActive'=> isset($data['isActive']) ? (int)$data['isActive'] : 1
]);

json_response(['ok'=>true,'id'=>$id]);
?>