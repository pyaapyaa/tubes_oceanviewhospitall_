<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

// 📍 PROTEKSI HAK AKSES — Hanya Apoteker yang boleh mengakses halaman ini
if ($_SESSION['role'] != "Apoteker") {
    include '../includes/header.php';
    die("<div class='container mt-5'>
            <div class='alert alert-danger shadow-sm fw-bold py-3'>
                ⚠️ Akses ditolak. Halaman ini hanya didesain khusus untuk Apoteker.
            </div>
         </div>");
    include '../includes/footer.php';
    exit;
}

include '../config/koneksi.php';
include '../includes/header.php';
?>

<div class="container mt-5">

    <!-- Judul Halaman -->
    <h2 class="mb-4 fw-bold">💊 Apotek (Penyerahan Obat)</h2>

    <!-- Ambil Data “Menunggu Obat” dengan JOIN ke tabel pemeriksaan -->
    <?php
    $query = mysqli_query($conn, "
    SELECT 
        pendaftaran.id_daftar,
        pendaftaran.status,
        pasien.nama,
        dokter.nama_dokter,
        pemeriksaan.resep
    FROM pendaftaran
    JOIN pasien ON pasien.id_pasien = pendaftaran.id_pasien
    JOIN dokter ON dokter.id_dokter = pendaftaran.id_dokter
    JOIN pemeriksaan ON pemeriksaan.id_daftar = pendaftaran.id_daftar
    WHERE pendaftaran.status = 'Menunggu Obat'
    ORDER BY pendaftaran.id_daftar DESC
    ");
    ?>

    <!-- Tampilkan Tabel Antrean Obat -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle shadow-sm">
            <thead class="table-primary text-center">
                <tr>
                    <th width="50">No</th>
                    <th>Pasien</th>
                    <th>Dokter</th>
                    <th>Resep</th>
                    <th width="180">Status</th>
                    <th width="180">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $no = 1;
            if (mysqli_num_rows($query) > 0) {
                while ($row = mysqli_fetch_assoc($query)) {
                ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['nama_dokter']) ?></td>
                        
                        <!-- FORMAT PRINT RESEP DENGAN LOGIKA EXPLODE STRIP DATA -->
                        <td>
                            <?php
                            // Memecah resep per baris baru (\n)
                            $resep_baris = preg_split('/\r\n|\r|\n/', $row['resep']);
                            
                            foreach ($resep_baris as $r) {
                                if (trim($r) === '') continue; // Lewati baris kosong
                                
                                $item = explode("|", $r);
                                if (isset($item[0]) && isset($item[1]) && trim($item[0]) !== '') {
                                    echo "💊 " . htmlspecialchars(trim($item[0])) . " (" . htmlspecialchars(trim($item[1])) . ")<br>";
                                } else {
                                    echo "💊 " . htmlspecialchars(trim($r)) . "<br>";
                                }
                            }
                            ?>
                        </td>
                        
                        <td class="text-center">
                            <span class="badge bg-warning text-dark w-100 py-2"><?= htmlspecialchars($row['status']) ?></span>
                        </td>
                        <td class="text-center">
                            <a href="proses_serahkan.php?id=<?= $row['id_daftar'] ?>" class="btn btn-success btn-sm w-100 py-2 fw-bold">
                                💊 Serahkan Obat
                            </a>
                        </td>
                    </tr>
                <?php 
                }
            } else { 
            ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Tidak ada pasien yang sedang menunggu obat.</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<?php
include '../includes/footer.php';
?>