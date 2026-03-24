<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Fatura #<?= e($order['order_no']) ?></title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: Arial, sans-serif; font-size: 13px; color: #333; padding: 20px; }
  .header { display: flex; justify-content: space-between; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #333; }
  .logo { font-size: 22px; font-weight: bold; }
  .invoice-info { text-align: right; }
  table { width: 100%; border-collapse: collapse; margin: 20px 0; }
  th { background: #f3f4f6; padding: 8px 10px; text-align: left; font-size: 12px; }
  td { padding: 8px 10px; border-bottom: 1px solid #f3f4f6; }
  .total { text-align: right; font-size: 16px; font-weight: bold; margin-top: 10px; }
  @media print { body { padding: 0; } .no-print { display: none; } }
</style>
</head>
<body>
<div class="header">
  <div>
    <div class="logo"><?= e($siteName ?? 'Magazam') ?></div>
  </div>
  <div class="invoice-info">
    <div>Sipariş No: <strong><?= e($order['order_no']) ?></strong></div>
    <div>Tarih: <?= formatDate($order['created_at']) ?></div>
  </div>
</div>

<div style="display:flex;justify-content:space-between;margin-bottom:20px">
  <div>
    <div style="font-weight:bold;margin-bottom:4px">Müşteri</div>
    <div><?= e($order['customer_name']) ?></div>
    <div><?= e($order['customer_email']) ?></div>
  </div>
  <div>
    <div style="font-weight:bold;margin-bottom:4px">Teslimat Adresi</div>
    <?php $addr = json_decode($order['shipping_address']??'{}',true); ?>
    <div><?= e($addr['address']??$order['shipping_address']??'') ?></div>
    <div><?= e(($addr['district']??$order['shipping_district']??'').' '.($addr['city']??$order['shipping_city']??'')) ?></div>
  </div>
</div>

<table>
  <thead><tr><th>Ürün</th><th>SKU</th><th>Fiyat</th><th>Adet</th><th>Toplam</th></tr></thead>
  <tbody>
    <?php foreach ($items as $item): ?>
    <tr>
      <td><?= e($item['product_name'] ?? $item['name'] ?? '—') ?></td>
      <td><?= e($item['sku'] ?? '—') ?></td>
      <td><?= formatPriceTRY($item['price']) ?></td>
      <td><?= $item['quantity'] ?></td>
      <td><?= formatPriceTRY($item['price'] * $item['quantity']) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<div class="total">Toplam: <?= formatPriceTRY($order['total']) ?></div>

<div class="no-print" style="margin-top:30px;text-align:center">
  <button onclick="window.print()" style="padding:8px 24px;background:#2563eb;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:13px">Yazdır</button>
  <button onclick="window.close()" style="padding:8px 24px;background:#f3f4f6;color:#374151;border:none;border-radius:6px;cursor:pointer;font-size:13px;margin-left:8px">Kapat</button>
</div>
</body>
</html>
