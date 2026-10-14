<?php
require 'config.php'; wajib_login();
if(!role('admin')) { header("Location: dashboard.php"); exit; }
$msg="";
if(isset($_GET['aksi'],$_GET['id'])){
    $id=(int)$_GET['id']; $aksi=$_GET['aksi'];
    if($aksi==='tolak'){
        $stmt=$conn->prepare("UPDATE transaksi SET status='ditolak' WHERE id_transaksi=? AND status='pending'");
        $stmt->bind_param("i",$id); $stmt->execute(); $msg="Transaksi ditolak.";
    } elseif($aksi==='setujui'){
        $conn->begin_transaction();
        try {
            $stmt=$conn->prepare("SELECT id_user,total_rp,status FROM transaksi WHERE id_transaksi=? FOR UPDATE");
            $stmt->bind_param("i",$id); $stmt->execute(); $t=$stmt->get_result()->fetch_assoc();
            if(!$t || $t['status']!=='pending') throw new Exception("Transaksi sudah diproses.");
            $stmt=$conn->prepare("UPDATE transaksi SET status='diverifikasi' WHERE id_transaksi=?");
            $stmt->bind_param("i",$id); $stmt->execute();
            $stmt=$conn->prepare("UPDATE user SET saldo=saldo+? WHERE id_user=?");
            $stmt->bind_param("di",$t['total_rp'],$t['id_user']); $stmt->execute();
            $q2=$conn->prepare("SELECT id_sampah,berat_kg FROM transaksi WHERE id_transaksi=?"); $q2->bind_param("i",$id); $q2->execute(); $tr2=$q2->get_result()->fetch_assoc();
            $q3=$conn->prepare("UPDATE sampah SET stok_kg=stok_kg+? WHERE id_sampah=?"); $q3->bind_param("di",$tr2['berat_kg'],$tr2['id_sampah']); $q3->execute();
            $conn->commit(); $msg="Transaksi disetujui dan saldo ditambahkan.";
        } catch(Exception $e){ $conn->rollback(); $msg=$e->getMessage(); }
    }
}
$rows=$conn->query("SELECT t.*,u.nama,s.jenis_sampah FROM transaksi t JOIN user u ON u.id_user=t.id_user JOIN sampah s ON s.id_sampah=t.id_sampah ORDER BY t.tanggal DESC");
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Verifikasi Transaksi</title><link rel="stylesheet" href="style.css?v=3"></head>
<body><?php include 'nav.php'; ?><main class="container"><h1>Verifikasi Transaksi</h1><?php if($msg): ?><div class="alert"><?=e($msg)?></div><?php endif; ?><div class="panel table-wrap"><table><tr><th>Tanggal</th><th>User</th><th>Sampah</th><th>Berat</th><th>Total</th><th>Status</th><th>Aksi</th></tr>
<?php while($r=$rows->fetch_assoc()): ?><tr><td><?=e($r['tanggal'])?></td><td><?=e($r['nama'])?></td><td><?=e($r['jenis_sampah'])?></td><td><?=number_format($r['berat_kg'],2,',','.')?> kg</td><td>Rp <?=number_format($r['total_rp'],0,',','.')?></td><td><?=e($r['status'])?></td><td><?php if($r['status']==='pending'): ?><a class="btn small success" href="?aksi=setujui&id=<?=$r['id_transaksi']?>">Setujui</a> <a class="btn small danger" href="?aksi=tolak&id=<?=$r['id_transaksi']?>">Tolak</a><?php else: ?>-<?php endif; ?></td></tr><?php endwhile; ?></table></div></main></body></html>