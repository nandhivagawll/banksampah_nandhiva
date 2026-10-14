<?php
require 'config.php'; wajib_login();
$uid=(int)$_SESSION['user']['id_user'];
$stmt=$conn->prepare("SELECT t.*,s.jenis_sampah FROM transaksi t JOIN sampah s ON s.id_sampah=t.id_sampah WHERE t.id_user=? ORDER BY t.tanggal DESC");
$stmt->bind_param("i",$uid); $stmt->execute(); $rows=$stmt->get_result();
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Transaksi</title><link rel="stylesheet" href="style.css?v=3"></head>
<body><?php include 'nav.php'; ?><main class="container"><h1>Riwayat Transaksi</h1><div class="panel table-wrap"><table><tr><th>Tanggal</th><th>Sampah</th><th>Berat</th><th>Total</th><th>Status</th></tr>
<?php while($r=$rows->fetch_assoc()): ?><tr><td><?=e($r['tanggal'])?></td><td><?=e($r['jenis_sampah'])?></td><td><?=number_format($r['berat_kg'],2,',','.')?> kg</td><td>Rp <?=number_format($r['total_rp'],0,',','.')?></td><td><span class="badge <?=e($r['status'])?>"><?=e($r['status'])?></span></td></tr><?php endwhile; ?>
</table></div></main></body></html>