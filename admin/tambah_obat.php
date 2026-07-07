<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';

$pesan = "";

if (isset($_POST['simpan'])) {

    $nama_obat = trim($_POST['nama_obat']);
    $jenis = trim($_POST['jenis']);
    $stok = trim($_POST['stok']);
    $harga = trim($_POST['harga']);

    if ($nama_obat == "") {

        $pesan = "Nama obat tidak boleh kosong.";

    } elseif ($jenis == "") {

        $pesan = "Jenis obat tidak boleh kosong.";

    } elseif ($stok == "") {

        $pesan = "Stok tidak boleh kosong.";

    } elseif ($harga == "") {

        $pesan = "Harga tidak boleh kosong.";

    } else {

        mysqli_query($conn, "INSERT INTO obat
        (nama_obat, jenis, stok, harga)
        VALUES
        ('$nama_obat','$jenis','$stok','$harga')");

        echo "<script>
                alert('Data obat berhasil ditambahkan.');
                window.location='obat.php';
              </script>";
        exit;
    }
}

include '../includes/header.php';
?>

<div class="container mt-5">

    <h2>Tambah Data Obat</h2>

    <?php if($pesan != "") { ?>
        <div class="alert alert-warning">
            <?= $pesan; ?>
        </div>
    <?php } ?>

    <form method="POST">

        <div class="mb-3">
            <label>Nama Obat</label>
            <input type="text" name="nama_obat" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Jenis</label>
            <input type="text" name="jenis" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Stok</label>
            <input type="number" name="stok" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Harga</label>
            <input type="number" name="harga" class="form-control" required>
        </div>

        <button type="submit" name="simpan" class="btn btn-success">
            Simpan
        </button>

        <a href="obat.php" class="btn btn-secondary">
            Kembali
        </a>

    </form>

</div>

<?php
include '../includes/footer.php';
?>