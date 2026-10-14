<?php
require 'config.php'; wajib_login(); if(!role('admin')){header("Location: dashboard.php");exit;}
$msg="";
if($_SERVER['REQUEST_METHOD']==='POST'){
    $jenis=trim($_POST['jenis_sampah']??''); $harga=(float)($_POST['harga_per_kg']??0);
    if($jenis!=='' && $harga>0){
        $stmt=$conn->prepare("INSERT INTO sampah(jenis_sampah,harga_per_kg) VALUES(?,?)");
        $stmt->bind_param("sd",$jenis,$harga); $stmt->execute(); $msg="Jenis sampah ditambahkan.";
    }
}
$rows=$conn->query("SELECT * FROM sampah ORDER BY id_sampah");
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Data Sampah</title><link rel="stylesheet" href="style.css?v=3"></head>
<body><?php include 'nav.php'; ?><main class="container"><h1>Data Sampah</h1><div class="panel narrow"><?php if($msg): ?><div class="alert"><?=e($msg)?></div><?php endif; ?><form method="post"><label>Jenis Sampah</label><input name="jenis_sampah" required><label>Harga / kg</label><input type="number" name="harga_per_kg" min="1" required><button class="btn primary full">Tambah</button></form></div>
<div class="panel table-wrap"><table><tr><th>ID</th><th>Jenis</th><th>Harga/kg</th><th>Stok</th></tr><?php while($r=$rows->fetch_assoc()): ?><tr><td><?=$r['id_sampah']?></td><td><?=e($r['jenis_sampah'])?></td><td>Rp <?=number_format($r['harga_per_kg'],0,',','.')?></td><td><?=number_format($r['stok_kg'],2,',','.')?> kg</td></tr><?php endwhile; ?></table></div></main></body></html>