<?php
require 'config.php';
wajib_login();

/*
|--------------------------------------------------------------------------
| PROSES SETOR SAMPAH
| Admin, Siswa, dan Guru semuanya boleh setor
|--------------------------------------------------------------------------
*/

$msg = "";
$msg_type = "success";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_sampah = (int) ($_POST['id_sampah'] ?? 0);
    $berat     = (float) ($_POST['berat_kg'] ?? 0);

    if ($id_sampah <= 0) {

        $msg = "Silakan pilih jenis sampah.";
        $msg_type = "danger";

    } elseif ($berat <= 0) {

        $msg = "Berat sampah harus lebih dari 0 kg.";
        $msg_type = "danger";

    } else {

        /*
        |--------------------------------------------------------------------------
        | Ambil harga sampah langsung dari database
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare("
            SELECT
                id_sampah,
                jenis_sampah,
                harga_per_kg,
                stok_kg
            FROM sampah
            WHERE id_sampah = ?
            LIMIT 1
        ");

        $stmt->bind_param("i", $id_sampah);
        $stmt->execute();

        $sampah_data = $stmt->get_result()->fetch_assoc();

        if (!$sampah_data) {

            $msg = "Jenis sampah tidak ditemukan di database.";
            $msg_type = "danger";

        } else {

            /*
            |--------------------------------------------------------------------------
            | Harga diambil dari database
            |--------------------------------------------------------------------------
            */

            $harga = (float) $sampah_data['harga_per_kg'];

            /*
            |--------------------------------------------------------------------------
            | Hitung total
            |--------------------------------------------------------------------------
            */

            $total = $berat * $harga;

            /*
            |--------------------------------------------------------------------------
            | ID user yang sedang login
            |--------------------------------------------------------------------------
            */

            $id_user = (int) $_SESSION['user']['id_user'];

            /*
            |--------------------------------------------------------------------------
            | Simpan transaksi
            |--------------------------------------------------------------------------
            */

            $stmt = $conn->prepare("
                INSERT INTO transaksi
                (
                    id_user,
                    id_sampah,
                    tipe,
                    berat_kg,
                    total_rp,
                    status
                )
                VALUES
                (
                    ?,
                    ?,
                    'setor',
                    ?,
                    ?,
                    'pending'
                )
            ");

            $stmt->bind_param(
                "iidd",
                $id_user,
                $id_sampah,
                $berat,
                $total
            );

            if ($stmt->execute()) {

                $msg = "Setoran berhasil dibuat. Menunggu verifikasi Admin.";
                $msg_type = "success";

            } else {

                $msg = "Gagal menyimpan setoran: " . $conn->error;
                $msg_type = "danger";
            }
        }
    }
}


/*
|--------------------------------------------------------------------------
| AMBIL DATA SAMPAH DARI DATABASE
|--------------------------------------------------------------------------
*/

$sampah = $conn->query("
    SELECT
        id_sampah,
        jenis_sampah,
        harga_per_kg,
        stok_kg
    FROM sampah
    ORDER BY jenis_sampah ASC
");

?>

<!doctype html>
<html lang="id">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Setor Sampah</title>

    <link
        rel="stylesheet"
        href="style.css?v=3"
    >

</head>

<body>

<?php include 'nav.php'; ?>


<main class="container">

    <div class="panel narrow">

        <h1>Setor Sampah</h1>

        <p class="muted">
            Admin, Siswa, dan Guru dapat melakukan setoran sampah.
            Jenis dan harga sampah diambil langsung dari database.
        </p>


        <?php if ($msg): ?>

            <div class="alert <?= $msg_type === 'danger' ? 'danger' : '' ?>">

                <?= e($msg) ?>

            </div>

        <?php endif; ?>


        <form method="post">

            <!-- =====================================================
                 JENIS SAMPAH
                 ===================================================== -->

            <label for="id_sampah">
                Jenis Sampah
            </label>

            <select
                name="id_sampah"
                id="id_sampah"
                required
                onchange="updateHarga()"
            >

                <option value="">
                    -- Pilih Jenis Sampah --
                </option>


                <?php while ($s = $sampah->fetch_assoc()): ?>

                    <option
                        value="<?= $s['id_sampah'] ?>"
                        data-harga="<?= $s['harga_per_kg'] ?>"
                        data-stok="<?= $s['stok_kg'] ?>"
                    >

                        <?= e($s['jenis_sampah']) ?>

                        -
                        Rp <?= number_format(
                            $s['harga_per_kg'],
                            0,
                            ',',
                            '.'
                        ) ?>/kg

                    </option>

                <?php endwhile; ?>

            </select>


            <!-- =====================================================
                 HARGA
                 ===================================================== -->

            <label>
                Harga per Kg
            </label>

            <input
                type="text"
                id="harga"
                value="-"
                readonly
            >


            <!-- =====================================================
                 STOK
                 ===================================================== -->

            <label>
                Stok Saat Ini
            </label>

            <input
                type="text"
                id="stok"
                value="-"
                readonly
            >


            <!-- =====================================================
                 BERAT
                 ===================================================== -->

            <label for="berat_kg">
                Berat Sampah (kg)
            </label>

            <input
                type="number"
                name="berat_kg"
                id="berat_kg"
                min="0.01"
                step="0.01"
                placeholder="Contoh: 2.5"
                required
                oninput="hitungTotal()"
            >


            <!-- =====================================================
                 TOTAL
                 ===================================================== -->

            <label>
                Total Estimasi
            </label>

            <input
                type="text"
                id="total"
                value="Rp 0"
                readonly
            >


            <button
                type="submit"
                class="btn primary full"
            >
                Kirim Setoran
            </button>

        </form>

    </div>

</main>


<script>

/*
|--------------------------------------------------------------------------
| Format Rupiah
|--------------------------------------------------------------------------
*/

function formatRupiah(angka) {

    angka = Number(angka) || 0;

    return "Rp " + angka.toLocaleString("id-ID");
}


/*
|--------------------------------------------------------------------------
| Update harga dan stok
|--------------------------------------------------------------------------
*/

function updateHarga() {

    const select =
        document.getElementById("id_sampah");

    const option =
        select.options[select.selectedIndex];


    if (!option.value) {

        document.getElementById("harga").value = "-";

        document.getElementById("stok").value = "-";

        document.getElementById("total").value = "Rp 0";

        return;
    }


    const harga =
        Number(option.dataset.harga) || 0;

    const stok =
        Number(option.dataset.stok) || 0;


    document.getElementById("harga").value =
        formatRupiah(harga) + " / kg";


    document.getElementById("stok").value =
        stok.toLocaleString("id-ID") + " kg";


    hitungTotal();
}


/*
|--------------------------------------------------------------------------
| Hitung total otomatis
|--------------------------------------------------------------------------
*/

function hitungTotal() {

    const select =
        document.getElementById("id_sampah");

    const option =
        select.options[select.selectedIndex];


    const berat =
        Number(
            document.getElementById("berat_kg").value
        ) || 0;


    if (!option.value) {

        document.getElementById("total").value =
            "Rp 0";

        return;
    }


    const harga =
        Number(option.dataset.harga) || 0;


    const total =
        berat * harga;


    document.getElementById("total").value =
        formatRupiah(total);
}

</script>

</body>
</html>
