<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';

$pesan = "";

if (isset($_POST['simpan'])) {

    $nama_dokter = trim($_POST['nama_dokter']);
    $spesialis = trim($_POST['spesialis']);
    $jadwal = trim($_POST['jadwal']);
    $no_hp = trim($_POST['no_hp']);

    if ($nama_dokter == "") {

        $pesan = "Nama dokter tidak boleh kosong.";

    } elseif ($spesialis == "") {

        $pesan = "Spesialis tidak boleh kosong.";

    } elseif ($jadwal == "") {

        $pesan = "Jadwal praktik tidak boleh kosong.";

    } elseif ($no_hp == "") {

        $pesan = "Nomor HP tidak boleh kosong.";

    } else {

        mysqli_query($conn, "INSERT INTO dokter
        (nama_dokter, spesialis, jadwal, no_hp)
        VALUES
        ('$nama_dokter','$spesialis','$jadwal','$no_hp')");

        echo "<script>
                alert('Data dokter berhasil ditambahkan.');
                window.location='dokter.php';
              </script>";
        exit;
    }
}

include '../includes/header.php';
?>

<div class="container mt-5">

    <h2>Tambah Data Dokter</h2>

    <?php if($pesan != "") { ?>
        <div class="alert alert-warning">
            <?= $pesan; ?>
        </div>
    <?php } ?>

    <form method="POST">

        <div class="mb-3">
            <label>Nama Dokter</label>
            <input type="text" name="nama_dokter" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Spesialis</label>
            <input type="text" name="spesialis" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Jadwal Praktik</label>
            <input type="text" name="jadwal" class="form-control" placeholder="Contoh: Senin - Jumat, 08.00 - 15.00" required>
        </div>

        <div class="mb-3">
            <label>No HP</label>
            <input type="text" name="no_hp" class="form-control" required>
        </div>

        <button type="submit" name="simpan" class="btn btn-success">
            Simpan
        </button>

        <a href="dokter.php" class="btn btn-secondary">
            Kembali
        </a>

    </form>

</div>

<?php
include '../includes/footer.php';
?>