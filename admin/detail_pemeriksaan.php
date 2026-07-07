<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';

// VALIDASI ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("
    <div class='container mt-5'>
        <div class='alert alert-danger fw-bold'>
            ⚠️ ID pendaftaran tidak ditemukan atau tidak valid.
        </div>
        <a href='pendaftaran.php' class='btn btn-secondary'>Kembali</a>
    </div>");
}

$id = (int)$_GET['id'];

// =========================================================================
// QUERY FIX BARU (Menggunakan LEFT JOIN agar data pendaftaran tetap muncul)
// =========================================================================
$query = mysqli_query($conn, "
SELECT
    pendaftaran.*,
    pasien.nama,
    dokter.nama_dokter,
    pemeriksaan.suhu,
    pemeriksaan.tekanan_darah,
    pemeriksaan.berat_badan,
    pemeriksaan.tinggi_badan,
    pemeriksaan.diagnosa,
    pemeriksaan.tindakan,
    pemeriksaan.resep,
    pemeriksaan.catatan,
    pemeriksaan.tanggal_periksa

FROM pendaftaran
JOIN pasien ON pendaftaran.id_pasien = pasien.id_pasien
JOIN dokter ON pendaftaran.id_dokter = dokter.id_dokter
LEFT JOIN pemeriksaan ON pemeriksaan.id_daftar = pendaftaran.id_daftar

WHERE pendaftaran.id_daftar = '$id'
LIMIT 1
");

$data = mysqli_fetch_assoc($query);

// VALIDASI DATA PENDAFTARAN
if (!$data) {
    die("
    <div class='container mt-5'>
        <div class='alert alert-warning fw-bold'>
            ⚠️ Data pendaftaran tidak ditemukan pada sistem.
        </div>
        <a href='pendaftaran.php' class='btn btn-secondary'>Kembali</a>
    </div>");
}

include '../includes/header.php';
?>

<div class="container mt-5">

    <div class="card shadow mb-5" style="border-radius: 15px; border: none; overflow: hidden;">

        <div class="card-header bg-primary text-white py-3">
            <h4 class="mb-0 fw-bold">📋 Detail Pemeriksaan Pasien</h4>
        </div>

        <div class="card-body p-4">

            <!-- DATA UTAMA PASIEN DAN PENDAFTARAN -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Nama Pasien</label>
                    <input class="form-control bg-light" value="<?= htmlspecialchars($data['nama']); ?>" readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Dokter</label>
                    <input class="form-control bg-light" value="<?= htmlspecialchars($data['nama_dokter']); ?>" readonly>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Keluhan</label>
                <textarea class="form-control bg-light" rows="2" readonly><?= htmlspecialchars($data['keluhan']); ?></textarea>
            </div>

            <hr>

            <!-- KONDISIONAL: JIKA DOKTER BELUM MENGISI HASIL PEMERIKSAAN -->
            <?php if (empty($data['tanggal_periksa'])) : ?>
                <div class="alert alert-warning py-3 mb-0" style="border-radius: 10px;">
                    ✨ Pasien ini sedang berada dalam antrean dan <strong>belum dilakukan pemeriksaan medis</strong> oleh dokter.
                </div>
            <?php else : ?>

                <!-- HASIL PEMERIKSAAN MEDIS (MUNCUL JIKA SUDAH DIISI) -->
                <h5 class="fw-bold text-primary mb-3">🩺 Hasil Pemeriksaan</h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Suhu</label>
                        <input class="form-control bg-light" value="<?= htmlspecialchars($data['suhu']); ?> °C" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tekanan Darah</label>
                        <input class="form-control bg-light" value="<?= htmlspecialchars($data['tekanan_darah']); ?>" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Berat Badan</label>
                        <input class="form-control bg-light" value="<?= htmlspecialchars($data['berat_badan']); ?> kg" readonly>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tinggi Badan</label>
                        <input class="form-control bg-light" value="<?= htmlspecialchars($data['tinggi_badan']); ?> cm" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Diagnosa</label>
                    <textarea class="form-control bg-light" rows="3" readonly><?= htmlspecialchars($data['diagnosa']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Tindakan</label>
                    <textarea class="form-control bg-light" rows="3" readonly><?= htmlspecialchars($data['tindakan']); ?></textarea>
                </div>

                <!-- RESEP OBAT MULTI LINE -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Resep Obat</label>
                    <div class="form-control bg-light">
                        <?php
                        if (!empty($data['resep'])) {
                            $resep = preg_split('/\r\n|\r|\n/', $data['resep']);
                            foreach ($resep as $r) {
                                $r = trim($r);
                                if ($r == '') continue;

                                $item = explode("|", $r);
                                if (isset($item[0]) && isset($item[1])) {
                                    echo "💊 " . htmlspecialchars($item[0]) . " (" . htmlspecialchars($item[1]) . ")<br>";
                                } else {
                                    echo "💊 " . htmlspecialchars($r) . "<br>";
                                }
                            }
                        } else {
                            echo "<span class='text-muted'>Tidak ada resep obat.</span>";
                        }
                        ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Catatan</label>
                    <textarea class="form-control bg-light" rows="3" readonly><?= htmlspecialchars($data['catatan']); ?></textarea>
                </div>

            <?php endif; ?>

            <!-- NAVIGATION BUTTONS -->
            <div class="d-flex gap-2 mt-4">
                <a href="pendaftaran.php" class="btn btn-secondary">Kembali</a>
                <?php if (!empty($data['tanggal_periksa'])) : ?>
                    <button class="btn btn-success" onclick="window.print()">🖨 Cetak</button>
                <?php endif; ?>
            </div>

        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>