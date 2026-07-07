<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';

$id = (int)$_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM pasien WHERE id_pasien='$id'");
$data = mysqli_fetch_assoc($query);

$pesan = "";

if (isset($_POST['update'])) {

    $nama = trim($_POST['nama']);
    $umur = trim($_POST['umur']);
    $jenis_kelamin = trim($_POST['jenis_kelamin']);
    $alamat = trim($_POST['alamat']);
    $keluhan = trim($_POST['keluhan']);

    // ================= VALIDASI =================

    if ($nama == "") {

        $pesan = "Nama pasien tidak boleh kosong.";

    } elseif (!is_numeric($umur) || $umur <= 0) {

        $pesan = "Umur harus berupa angka dan lebih dari 0.";

    } elseif ($jenis_kelamin == "") {

        $pesan = "Silakan pilih jenis kelamin.";

    } elseif ($alamat == "") {

        $pesan = "Alamat tidak boleh kosong.";

    } elseif ($keluhan == "") {

        $pesan = "Keluhan tidak boleh kosong.";

    } else {

        mysqli_query($conn, "UPDATE pasien SET

            nama='$nama',
            umur='$umur',
            jenis_kelamin='$jenis_kelamin',
            alamat='$alamat',
            keluhan='$keluhan'

            WHERE id_pasien='$id'
        ");

        echo "<script>
                alert('Data pasien berhasil diubah.');
                window.location='pasien.php';
              </script>";
        exit;
    }

    // agar data pada form tetap mengikuti input terakhir
    $data['nama'] = $nama;
    $data['umur'] = $umur;
    $data['jenis_kelamin'] = $jenis_kelamin;
    $data['alamat'] = $alamat;
    $data['keluhan'] = $keluhan;
}

include '../includes/header.php';
?>

<div class="container mt-5">

    <h2 class="mb-4">Edit Data Pasien</h2>

    <?php if($pesan != "") { ?>

        <div class="alert alert-warning">
            <?= $pesan; ?>
        </div>

    <?php } ?>

    <form method="POST">

        <div class="mb-3">

            <label class="form-label">Nama Pasien</label>

            <input
                type="text"
                name="nama"
                class="form-control"
                value="<?= htmlspecialchars($data['nama']); ?>"
                required>

        </div>

        <div class="mb-3">

            <label class="form-label">Umur</label>

            <input
                type="number"
                name="umur"
                min="1"
                class="form-control"
                value="<?= htmlspecialchars($data['umur']); ?>"
                required>

        </div>

        <div class="mb-3">

            <label class="form-label">Jenis Kelamin</label>

            <select name="jenis_kelamin" class="form-control" required>

                <option value="">-- Pilih Jenis Kelamin --</option>

                <option value="Laki-laki"
                    <?= ($data['jenis_kelamin']=="Laki-laki") ? "selected" : ""; ?>>
                    Laki-laki
                </option>

                <option value="Perempuan"
                    <?= ($data['jenis_kelamin']=="Perempuan") ? "selected" : ""; ?>>
                    Perempuan
                </option>

            </select>

        </div>

        <div class="mb-3">

            <label class="form-label">Alamat</label>

            <textarea
                name="alamat"
                class="form-control"
                rows="3"
                required><?= htmlspecialchars($data['alamat']); ?></textarea>

        </div>

        <div class="mb-3">

            <label class="form-label">Keluhan</label>

            <textarea
                name="keluhan"
                class="form-control"
                rows="3"
                required><?= htmlspecialchars($data['keluhan']); ?></textarea>

        </div>

        <button type="submit" name="update" class="btn btn-warning">
            Update
        </button>

        <a href="pasien.php" class="btn btn-secondary">
            Kembali
        </a>

    </form>

</div>

<?php
include '../includes/footer.php';
?>