<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';

// LANGKAH 5: Mengaktifkan mode report error otomatis
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$pesan = "";

// mengambil data pasien untuk pilihan pasien lama
$pasien = mysqli_query($conn, "SELECT * FROM pasien ORDER BY nama ASC");

// mengambil data dokter
$dokter = mysqli_query($conn, "SELECT * FROM dokter ORDER BY nama_dokter ASC");

// LOGIKA PROSES SIMPAN
if(isset($_POST['simpan'])){

    $jenis = $_POST['jenis_pasien'];

    if($jenis == "lama"){

        $id_pasien = $_POST['id_pasien'];
        $id_dokter = $_POST['id_dokter'];
        $tanggal   = $_POST['tanggal'];
        $keluhan   = trim($_POST['keluhan']);

        if($id_pasien == ""){
            $pesan = "Silakan pilih pasien.";
        }elseif($id_dokter == ""){
            $pesan = "Silakan pilih dokter.";
        }elseif($keluhan == ""){
            $pesan = "Keluhan tidak boleh kosong.";
        }else{
            try {
                mysqli_query($conn, "INSERT INTO pendaftaran
                (id_pasien, id_dokter, tanggal, keluhan, status)
                VALUES
                ('$id_pasien', '$id_dokter', '$tanggal', '$keluhan', 'Menunggu Pemeriksaan')");

                echo "<script>
                alert('Pendaftaran berhasil');
                window.location='pendaftaran.php';
                </script>";
                exit;
            } catch (Exception $e) {
                $pesan = "Gagal mendaftarkan pasien lama: " . $e->getMessage();
            }
        }

    }else{

        // LANGKAH 4: Ganti proses Pasien Baru
        $nama      = trim($_POST['nama']);
        $username  = trim($_POST['username']);
        $password  = trim($_POST['password']);
        $umur      = trim($_POST['umur']);
        $jk        = trim($_POST['jenis_kelamin']);
        $alamat    = trim($_POST['alamat']);

        $id_dokter = $_POST['id_dokter_baru'];
        $tanggal   = $_POST['tanggal_baru'];
        $keluhan   = trim($_POST['keluhan_baru']);

        // Perubahan: Cek tabel users (menggunakan akhiran 's')
        $cek = mysqli_query($conn,"SELECT * FROM users WHERE username='$username'");

        if(mysqli_num_rows($cek) > 0){

            $pesan = "Username sudah digunakan.";

        }else{

            mysqli_begin_transaction($conn);

            try{

                // =====================
                // Perubahan: Simpan akun user ke tabel users (menggunakan akhiran 's')
                // =====================

                mysqli_query($conn,"
                INSERT INTO users
                (nama,username,password,role)
                VALUES
                ('$nama','$username','$password','Pasien')
                ");

                $id_user = mysqli_insert_id($conn);

                // =====================
                // Simpan pasien
                // =====================

                mysqli_query($conn,"
                INSERT INTO pasien
                (nama,umur,jenis_kelamin,alamat,keluhan,id_user)
                VALUES
                ('$nama','$umur','$jk','$alamat','$keluhan','$id_user')
                ");

                $id_pasien = mysqli_insert_id($conn);

                // =====================
                // Simpan pendaftaran
                // =====================

                mysqli_query($conn,"
                INSERT INTO pendaftaran
                (id_pasien,id_dokter,tanggal,keluhan,status)
                VALUES
                ('$id_pasien','$id_dokter','$tanggal','$keluhan','Menunggu Pemeriksaan')
                ");

                mysqli_commit($conn);

                echo "
                <script>
                alert('Pasien baru berhasil didaftarkan');
                window.location='pendaftaran.php';
                </script>";
                exit;

            }catch(Exception $e){

                mysqli_rollback($conn);

                $pesan = "Terjadi kesalahan : ".$e->getMessage();

            }

        }

    }
}

include '../includes/header.php';
?>

<div class="container mt-5">

    <h2>Daftar Berobat</h2>

    <!-- Langkah 1 — Tombol Navigasi Jenis Pasien -->
    <div class="row mt-4 mb-4">
        <div class="col-md-6">
            <button
                type="button"
                class="btn btn-primary w-100 p-3 fw-bold"
                id="btnLama">
                👤 Pasien Lama
            </button>
        </div>
        <div class="col-md-6">
            <button
                type="button"
                class="btn btn-outline-success w-100 p-3 fw-bold"
                id="btnBaru">
                🆕 Pasien Baru
            </button>
        </div>
    </div>

    <?php if($pesan != ""){ ?>
        <div class="alert alert-danger">
            <?= $pesan; ?>
        </div>
    <?php } ?>

    <form method="POST">
        
        <!-- Langkah 6 — Penanda Jenis Pasien (Hidden Input) -->
        <input type="hidden" name="jenis_pasien" id="jenis_pasien" value="lama">

        <!-- Langkah 2 — Wrapper Form Pasien Lama -->
        <div id="formPasienLama">

            <div class="mb-3">
                <label class="form-label">Pasien</label>
                <select name="id_pasien" class="form-control">
                    <option value="">-- Pilih Pasien --</option>
                    <?php while($p = mysqli_fetch_assoc($pasien)){ ?>
                        <option value="<?= $p['id_pasien']; ?>">
                            <?= htmlspecialchars($p['nama']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Dokter</label>
                <select name="id_dokter" class="form-control">
                    <option value="">-- Pilih Dokter --</option>
                    <?php 
                    mysqli_data_seek($dokter, 0); 
                    while($d = mysqli_fetch_assoc($dokter)){ 
                    ?>
                        <option value="<?= $d['id_dokter']; ?>">
                            <?= htmlspecialchars($d['nama_dokter']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal</label>
                <input
                    type="date"
                    name="tanggal"
                    class="form-control"
                    value="<?= date('Y-m-d'); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Keluhan</label>
                <textarea
                    name="keluhan"
                    class="form-control"
                    rows="3"></textarea>
            </div>

            <button
                type="submit"
                name="simpan"
                class="btn btn-success">
                Daftarkan
            </button>

            <a href="pendaftaran.php" class="btn btn-secondary">
                Kembali
            </a>

        </div>

        <!-- Langkah 5 — Form Pasien Baru -->
        <div id="formPasienBaru" style="display:none;">

            <div class="mb-3">
                <label class="form-label">Nama Pasien</label>
                <input type="text" name="nama" class="form-control">
            </div>

            <!-- Tambahan Field Username & Password -->
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Umur</label>
                <input type="number" name="umur" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-control">
                    <option value="">-- Pilih --</option>
                    <option value="Laki-laki">Laki-laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea
                    name="alamat"
                    class="form-control"
                    rows="3"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Dokter</label>
                <select name="id_dokter_baru" class="form-control">
                    <option value="">-- Pilih Dokter --</option>
                    <?php
                    mysqli_data_seek($dokter, 0);
                    while($d = mysqli_fetch_assoc($dokter)){
                    ?>
                        <option value="<?= $d['id_dokter']; ?>">
                            <?= htmlspecialchars($d['nama_dokter']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Tanggal</label>
                <input
                    type="date"
                    name="tanggal_baru"
                    class="form-control"
                    value="<?= date('Y-m-d'); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Keluhan</label>
                <textarea
                    name="keluhan_baru"
                    class="form-control"
                    rows="3"></textarea>
            </div>

            <button
                type="submit"
                name="simpan"
                class="btn btn-success">
                Daftarkan Pasien Baru
            </button>

            <a href="pendaftaran.php" class="btn btn-secondary">
                Kembali
            </a>

        </div>

    </form>

</div>

<!-- JavaScript Pengendali Interface -->
<script>
const lama = document.getElementById("btnLama");
const baru = document.getElementById("btnBaru");
const jenisPasien = document.getElementById("jenis_pasien");

lama.onclick = function(){
    document.getElementById("formPasienLama").style.display = "block";
    document.getElementById("formPasienBaru").style.display = "none";
    jenisPasien.value = "lama"; 

    lama.classList.remove("btn-outline-primary");
    lama.classList.add("btn-primary");

    baru.classList.remove("btn-success");
    baru.classList.add("btn-outline-success");
}

baru.onclick = function(){
    document.getElementById("formPasienLama").style.display = "none";
    document.getElementById("formPasienBaru").style.display = "block";
    jenisPasien.value = "baru"; 

    baru.classList.remove("btn-outline-success");
    baru.classList.add("btn-success");

    lama.classList.remove("btn-primary");
    lama.classList.add("btn-outline-primary");
}
</script>

<?php
include '../includes/footer.php';
?>