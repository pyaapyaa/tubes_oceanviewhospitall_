<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';

$id = $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM obat WHERE id_obat='$id'");
$data = mysqli_fetch_assoc($query);

$pesan = "";

if(isset($_POST['update'])){

    $nama_obat = trim($_POST['nama_obat']);
    $jenis = trim($_POST['jenis']);
    $stok = trim($_POST['stok']);
    $harga = trim($_POST['harga']);

    if($nama_obat == ""){

        $pesan = "Nama obat tidak boleh kosong.";

    }elseif($jenis == ""){

        $pesan = "Jenis obat tidak boleh kosong.";

    }elseif($stok == ""){

        $pesan = "Stok tidak boleh kosong.";

    }elseif($harga == ""){

        $pesan = "Harga tidak boleh kosong.";

    }else{

        mysqli_query($conn,"UPDATE obat SET
            nama_obat='$nama_obat',
            jenis='$jenis',
            stok='$stok',
            harga='$harga'
            WHERE id_obat='$id'
        ");

        echo "<script>
                alert('Data obat berhasil diubah');
                window.location='obat.php';
              </script>";
        exit;
    }
}

include '../includes/header.php';
?>

<div class="container mt-5">

    <h2>Edit Data Obat</h2>

    <?php if($pesan!=""){ ?>
        <div class="alert alert-warning">
            <?= $pesan; ?>
        </div>
    <?php } ?>

    <form method="POST">

        <div class="mb-3">
            <label>Nama Obat</label>
            <input type="text"
                   name="nama_obat"
                   class="form-control"
                   value="<?= $data['nama_obat']; ?>"
                   required>
        </div>

        <div class="mb-3">
            <label>Jenis</label>
            <input type="text"
                   name="jenis"
                   class="form-control"
                   value="<?= $data['jenis']; ?>"
                   required>
        </div>

        <div class="mb-3">
            <label>Stok</label>
            <input type="number"
                   name="stok"
                   class="form-control"
                   value="<?= $data['stok']; ?>"
                   required>
        </div>

        <div class="mb-3">
            <label>Harga</label>
            <input type="number"
                   name="harga"
                   class="form-control"
                   value="<?= $data['harga']; ?>"
                   required>
        </div>

        <button type="submit" name="update" class="btn btn-warning">
            Update
        </button>

        <a href="obat.php" class="btn btn-secondary">
            Kembali
        </a>

    </form>

</div>

<?php
include '../includes/footer.php';
?>