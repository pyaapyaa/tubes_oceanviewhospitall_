<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

// 🔐 PROTEKSI HAK AKSES — Hanya Dokter yang boleh melakukan pemeriksaan
if ($_SESSION['role'] != "Dokter") {
    include '../includes/header.php';
    die("<div class='container mt-5'>
            <div class='alert alert-danger shadow-sm fw-bold py-3'>
                ⚠️ Akses ditolak. Halaman ini hanya didesain khusus untuk Dokter.
            </div>
            <a href='dashboard.php' class='btn btn-secondary shadow-sm'>Kembali ke Dashboard</a>
         </div>");
    include '../includes/footer.php';
    exit;
}

include '../config/koneksi.php';

// LANGKAH 1 - Pengamanan dan Validasi Parameter ID dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    include '../includes/header.php';
    die("<div class='container mt-5'><div class='alert alert-danger fw-bold'>⚠️ ID pendaftaran tidak ditemukan atau tidak valid.</div><a href='pendaftaran.php' class='btn btn-secondary'>Kembali</a></div>");
}

$id = (int)$_GET['id'];

// ================= KODE INSERT PEMERIKSAAN =================
if(isset($_POST['simpan'])){

    $suhu = mysqli_real_escape_string($conn, trim($_POST['suhu']));
    $tekanan_darah = mysqli_real_escape_string($conn, trim($_POST['tekanan_darah']));
    $berat_badan = mysqli_real_escape_string($conn, trim($_POST['berat_badan']));
    $tinggi_badan = mysqli_real_escape_string($conn, trim($_POST['tinggi_badan']));
    $diagnosa = mysqli_real_escape_string($conn, trim($_POST['diagnosa']));
    $tindakan = mysqli_real_escape_string($conn, trim($_POST['tindakan']));
    $catatan = mysqli_real_escape_string($conn, trim($_POST['catatan']));
    
    // FORMAT BARU: Menggabungkan obat dengan pembatas "|" dan setiap obat dipisahkan enter "\n"
    $resep = "";
    if(isset($_POST['obat']) && is_array($_POST['obat'])){
        $daftar_input_obat = $_POST['obat'];
        
        foreach($daftar_input_obat as $i => $nama_obat){
            $nama_obat = mysqli_real_escape_string($conn, trim($nama_obat));
            $jumlah = mysqli_real_escape_string($conn, trim($_POST['jumlah'][$i]));

            if($nama_obat != "" && $jumlah != ""){
                $resep .= $nama_obat . "|" . $jumlah . "\n";
            }
        }
    }

    // Jika resep kosong, beri tanda strip
    if (empty($resep)) {
        $resep = "-";
    }

    $tanggal = date('Y-m-d');

    // 1. Insert ke tabel pemeriksaan
    mysqli_query($conn,"INSERT INTO pemeriksaan
    (
        id_daftar,
        suhu,
        tekanan_darah,
        berat_badan,
        tinggi_badan,
        diagnosa,
        tindakan,
        resep,
        catatan,
        tanggal_periksa
    )
    VALUES
    (
        '$id',
        '$suhu',
        '$tekanan_darah',
        '$berat_badan',
        '$tinggi_badan',
        '$diagnosa',
        '$tindakan',
        '$resep',
        '$catatan',
        '$tanggal'
    )");

    // 2. Mengubah status pendaftaran menjadi 'Menunggu Obat'
    mysqli_query($conn,"
        UPDATE pendaftaran
        SET status='Menunggu Obat'
        WHERE id_daftar='$id'
    ");

    echo "<script>
            alert('Pemeriksaan berhasil disimpan. Status berganti: Menunggu Obat');
            window.location='pendaftaran.php';
          </script>";
    exit;
}
// ===========================================================

// Query dengan JOIN untuk mengambil informasi detail pendaftaran, pasien, dan dokter
$query = mysqli_query($conn, "
SELECT
    pendaftaran.*,
    pasien.nama,
    dokter.nama_dokter
FROM pendaftaran
JOIN pasien ON pendaftaran.id_pasien = pasien.id_pasien
JOIN dokter ON pendaftaran.id_dokter = dokter.id_dokter
WHERE id_daftar='$id'
");

$data = mysqli_fetch_assoc($query);

// Jika ID ada di URL tapi data tidak ditemukan di database
if (!$data) {
    include '../includes/header.php';
    die("<div class='container mt-5'><div class='alert alert-danger fw-bold'>⚠️ Data pendaftaran dengan ID tersebut tidak ditemukan di database.</div><a href='pendaftaran.php' class='btn btn-secondary'>Kembali</a></div>");
}

include '../includes/header.php';
?>

<div class="container mt-5">

    <h2 class="fw-bold mb-4">Pemeriksaan Pasien</h2>

    <div class="card shadow mb-5" style="border-radius: 15px; border: none; overflow: hidden;">
        <div class="card-header bg-success text-white fw-bold py-3">
            📋 Detail Pendaftaran Pasien
        </div>
        <div class="card-body p-4">

            <div class="mb-3">
                <strong>Nama Pasien :</strong>
                <p class="form-control-plaintext bg-light p-2 rounded mt-1">
                    <?= htmlspecialchars($data['nama']); ?>
                </p>
            </div>

            <div class="mb-3">
                <strong>Dokter :</strong>
                <p class="form-control-plaintext bg-light p-2 rounded mt-1">
                    <?= htmlspecialchars($data['nama_dokter']); ?>
                </p>
            </div>

            <div class="mb-3">
                <strong>Tanggal Pendaftaran :</strong>
                <p class="form-control-plaintext bg-light p-2 rounded mt-1">
                    <?= htmlspecialchars($data['tanggal']); ?>
                </p>
            </div>

            <div class="mb-3">
                <strong>Keluhan :</strong>
                <p class="form-control-plaintext bg-light p-2 rounded mt-1">
                    <?= htmlspecialchars($data['keluhan']); ?>
                </p>
            </div>

            <hr class="my-4 text-secondary">

            <!-- FORM INPUT PEMERIKSAAN -->
            <form method="POST">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">🌡️ Suhu Tubuh (°C)</label>
                        <input
                            type="text"
                            name="suhu"
                            class="form-control"
                            placeholder="Contoh: 36.8"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">❤️ Tekanan Darah</label>
                        <input
                            type="text"
                            name="tekanan_darah"
                            class="form-control"
                            placeholder="Contoh: 120/80"
                            required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">⚖️ Berat Badan (kg)</label>
                        <input
                            type="number"
                            step="0.1"
                            name="berat_badan"
                            class="form-control"
                            placeholder="Contoh: 65.5"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">📏 Tinggi Badan (cm)</label>
                        <input
                            type="number"
                            step="0.1"
                            name="tinggi_badan"
                            class="form-control"
                            placeholder="Contoh: 170"
                            required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Diagnosa</label>
                    <textarea
                        name="diagnosa"
                        class="form-control"
                        rows="3"
                        placeholder="Masukkan hasil diagnosa dokter..."
                        required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Tindakan</label>
                    <textarea
                        name="tindakan"
                        class="form-control"
                        rows="3"
                        placeholder="Masukkan tindakan medis yang diberikan..."
                        required></textarea>
                </div>

                <!-- SEKSI INPUT REPERKSAAN OBAT - DYNAMIC WRAPPER -->
                <div class="bg-light p-3 rounded mb-3 border">
                    <div id="daftarObat">
                        <div class="row item-obat mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">💊 Obat</label>
                                <input
                                    list="list_obat"
                                    name="obat[]"
                                    class="form-control"
                                    placeholder="Pilih atau ketik nama obat"
                                    required>
                                <datalist id="list_obat">
                                    <?php
                                    $obat = mysqli_query($conn, "SELECT * FROM obat ORDER BY nama_obat");
                                    while($o = mysqli_fetch_assoc($obat)){
                                    ?>
                                        <option value="<?= htmlspecialchars($o['nama_obat']); ?>">
                                    <?php } ?>
                                </datalist>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Jumlah</label>
                                <input
                                    type="number"
                                    name="jumlah[]"
                                    class="form-control"
                                    min="1"
                                    placeholder="0"
                                    required>
                            </div>
                            
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-secondary w-100" disabled>🔒</button>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Tambah Form Input Obat -->
                    <button type="button" class="btn btn-primary fw-bold" id="tambahObat">
                        ➕ Tambah Obat
                    </button>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Catatan Dokter</label>
                    <textarea
                        name="catatan"
                        class="form-control"
                        placeholder="Catatan tambahan (opsional)..."
                        rows="3"></textarea>
                </div>

                <div class="d-flex justify-content-start gap-2 mt-4">
                    <button type="submit" name="simpan" class="btn btn-success px-4">
                        Simpan Pemeriksaan
                    </button>
                    <a href="pendaftaran.php" class="btn btn-secondary px-4">
                        Kembali
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

<!-- JAVASCRIPT DINAMIS UNTUK TAMBAH/HAPUS BARIS OBAT -->
<script>
document.getElementById("tambahObat").addEventListener("click", function(){
    let html = `
    <div class="row item-obat mb-3">
        <div class="col-md-6">
            <input
                list="list_obat"
                name="obat[]"
                class="form-control"
                placeholder="Pilih atau ketik nama obat"
                required>
        </div>

        <div class="col-md-4">
            <input
                type="number"
                name="jumlah[]"
                class="form-control"
                min="1"
                placeholder="0"
                required>
        </div>

        <div class="col-md-2">
            <button type="button" class="btn btn-danger w-100 btn-hapus">🗑️</button>
        </div>
    </div>`;
    
    document.getElementById("daftarObat").insertAdjacentHTML('beforeend', html);
});

document.getElementById("daftarObat").addEventListener("click", function(e){
    if(e.target && e.target.classList.contains('btn-hapus')){
        e.target.closest('.item-obat').remove();
    }
});
</script>

<?php
include '../includes/footer.php';
?>