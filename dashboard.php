<?php
require 'config.php'; wajib_login();
$u = $_SESSION['user'];

if ($u['role'] === 'admin') {
    $totalUser = $conn->query("SELECT COUNT(*) c FROM user")->fetch_assoc()['c'];
    $pending = $conn->query("SELECT COUNT(*) c FROM transaksi WHERE status='pending'")->fetch_assoc()['c'];
    $totalSetor = $conn->query("SELECT COALESCE(SUM(total_rp),0) c FROM transaksi WHERE tipe='setor' AND status='diverifikasi'")->fetch_assoc()['c'];
} else {
    $stmt = $conn->prepare("SELECT COUNT(*) c FROM transaksi WHERE id_user=?");
    $stmt->bind_param("i",$u['id_user']); $stmt->execute();
    $totalTransaksi = $stmt->get_result()->fetch_assoc()['c'];

    $stmt = $conn->prepare("SELECT COALESCE(SUM(berat_kg),0) c FROM transaksi WHERE id_user=? AND tipe='setor' AND status='diverifikasi'");
    $stmt->bind_param("i",$u['id_user']); $stmt->execute();
    $totalKg = $stmt->get_result()->fetch_assoc()['c'];
}
?>
<!doctype html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard - Bank Sampah</title><link rel="stylesheet" href="style.css?v=3"></head>
<body>
<?php include 'nav.php'; ?>
<main class="container">
<h1>Dashboard</h1>
<p class="muted">Selamat datang, <?=e($u['nama'])?>.</p>

<?php if ($u['role']==='admin'): ?>
<div class="cards">
 <div class="card"><span>Total User</span><strong><?=$totalUser?></strong></div>
 <div class="card"><span>Transaksi Pending</span><strong><?=$pending?></strong></div>
 <div class="card"><span>Nilai Setoran Terverifikasi</span><strong>Rp <?=number_format($totalSetor,0,',','.')?></strong></div>
</div>
<div class="panel">
<h2>Akses Admin</h2>
<p>Kelola transaksi setoran, pengguna, jenis sampah, dan setoran ke pengepul.</p>
<a class="btn primary" href="admin_transaksi.php">Kelola Transaksi</a>
<a class="btn" href="users.php">Data User</a>
<a class="btn" href="sampah.php">Data Sampah</a>
</div>
<?php else: ?>
<div class="cards">
 <div class="card"><span>Saldo</span><strong>Rp <?=number_format($u['saldo'],0,',','.')?></strong></div>
 <div class="card"><span>Total Transaksi</span><strong><?=$totalTransaksi?></strong></div>
 <div class="card"><span>Sampah Terverifikasi</span><strong><?=number_format($totalKg,2,',','.')?> kg</strong></div>
</div>
<div class="panel">
<a class="btn primary" href="setor.php">+ Setor Sampah</a>
<a class="btn" href="transaksi.php">Riwayat Transaksi</a>
<a class="btn" href="tarik.php">Tarik Saldo</a>
</div>
<?php endif; ?>
</main>
</body></html>