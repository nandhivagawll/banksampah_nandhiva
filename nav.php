<?php $u=$_SESSION['user']; ?> 

<nav class="nav"> 

<div class="nav-inner"> 

<a class="brand" href="dashboard.php">
    ♻ Bank Sampah
</a> 

<div class="nav-links"> 

<a href="dashboard.php">
    Dashboard
</a> 


<?php if ($u['role'] === 'pengepul'): ?> 

    <a href="beli_admin.php">
        Beli Sampah
    </a>

    <a href="pembelian.php">
        Pembelian
    </a>


<?php elseif ($u['role'] !== 'admin'): ?> 

    <a href="setor.php">
        Setor
    </a>

    <a href="transaksi.php">
        Transaksi
    </a>

    <a href="tarik.php">
        Tarik Saldo
    </a>


<?php else: ?> 

    <!-- MENU KHUSUS ADMIN -->

    <a href="setor.php">
        Setor Sampah
    </a>

    <a href="admin_transaksi.php">
        Verifikasi
    </a>

    <a href="users.php">
        User
    </a>

    <a href="sampah.php">
        Sampah
    </a>

    <a href="jual_pengepul.php">
        Jual ke Pengepul
    </a>

<?php endif; ?> 


<span class="user">
    <?=e($u['nama'])?> 
    (<?=e($u['role'])?>)
</span> 


<a href="logout.php">
    Keluar
</a> 

</div>

</div>

</nav>