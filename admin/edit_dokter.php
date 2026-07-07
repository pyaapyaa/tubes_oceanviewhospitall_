<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';

$id = $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM dokter WHERE id_dokter='$id'");
$data = mysqli_fetch_assoc($query);

$pesan = "";

if(isset($_POST['update'])){

    $nama_dokter = trim($_POST['nama_dokter']);
    $spesialis = trim($_POST['spesialis']);
    $jadwal = trim($_POST['jadwal']);
    $no_hp = trim($_POST['no_hp']);

    if($nama_dokter == ""){

        $pesan = "Nama dokter tidak boleh kosong.";

    }elseif($spesialis == ""){

        $pesan = "Spesialis tidak boleh kosong.";

    }elseif($jadwal == ""){

        $pesan = "Jadwal praktik tidak boleh kosong.";

    }elseif($no_hp == ""){

        $pesan = "Nomor HP tidak boleh kosong.";

    }else{

        mysqli_query($conn,"UPDATE dokter SET
            nama_dokter='$nama_dokter',
            spesialis='$spesialis',
            jadwal='$jadwal',
            no_hp='$no_hp'
            WHERE id_dokter='$id'
        ");

        echo "<script>
                alert('Data dokter berhasil diubah');
                window.location='dokter.php';
              </script>";
        exit;
    }
}

include '../includes/header.php';
?>

<div class="container mt-5">

    <h2>Edit Data Dokter</h2>

    <?php if($pesan!=""){ ?>
        <div class="alert alert-warning">
            <?= $pesan; ?>
        </div>
    <?php } ?>

    <form method="POST">

        <div class="mb-3">
            <label>Nama Dokter</label>
            <input type="text" name="nama_dokter"
            class="form-control"
            value="<?= $data['nama_dokter']; ?>" required>
        </div>

        <div class="mb-3">
            <label>Spesialis</label>
            <input type="text" name="spesialis"
            class="form-control"
            value="<?= $data['spesialis']; ?>" required>
        </div>

        <div class="mb-3">
            <label>Jadwal Praktik</label>
            <input type="text" name="jadwal"
            class="form-control"
            value="<?= $data['jadwal']; ?>" required>
        </div>

        <div class="mb-3">
            <label>No HP</label>
            <input type="text" name="no_hp"
            class="form-control"
            value="<?= $data['no_hp']; ?>" required>
        </div>

        <button type="submit" name="update" class="btn btn-warning">
            Update
        </button>

        <a href="dokter.php" class="btn btn-secondary">
            Kembali
        </a>

    </form>

</div>

<?php
include '../includes/footer.php';
?>