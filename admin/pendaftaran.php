<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';
include '../includes/header.php';

// PERBAIKAN: Ditambahkan WHERE pendaftaran.status != 'Selesai' 
// Supaya data yang sudah selesai otomatis hilang dari daftar aktif dan langsung pindah ke Riwayat
$query = mysqli_query($conn, "
SELECT
    pendaftaran.*,
    pasien.nama,
    dokter.nama_dokter
FROM pendaftaran
JOIN pasien ON pendaftaran.id_pasien = pasien.id_pasien
JOIN dokter ON pendaftaran.id_dokter = dokter.id_dokter
WHERE pendaftaran.status != 'Selesai'
ORDER BY id_daftar DESC
");
?>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- Judul Halaman -->
        <h2 class="fw-bold">Data Pendaftaran Berobat</h2>

        <div class="d-flex">
            <input
                type="text"
                id="searchPasien"
                class="form-control me-2"
                placeholder="Cari pasien..."
                style="width:220px;">

            <!-- KONDISI 1: Tombol Daftar Berobat hanya tampil jika user adalah Admin -->
            <?php if ($_SESSION['role'] == "Admin") { ?>
                <a href="tambah_pendaftaran.php" class="btn btn-success text-nowrap">
                    + Daftar Berobat
                </a>
            <?php } ?>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle" id="tabelPasien">

            <thead class="table-primary text-center">
                <tr>
                    <th>No</th>
                    <th>Nama Pasien</th>
                    <th>Dokter</th>
                    <th>Tanggal</th>
                    <th>Keluhan</th>
                    <th>Status</th>
                    <th width="140">Aksi</th>
                </tr>
            </thead>

            <tbody>
            <?php
            $no = 1;
            if (mysqli_num_rows($query) > 0) {
                while ($row = mysqli_fetch_assoc($query)) {
                ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['nama']); ?></td>
                        <td><?= htmlspecialchars($row['nama_dokter']); ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['tanggal']); ?></td>
                        <td><?= htmlspecialchars($row['keluhan']); ?></td>
                        
                        <!-- BAGIAN BADGE STATUS DINAMIS -->
                        <td class="text-center">
                            <?php
                            if($row['status'] == "Menunggu Pemeriksaan"){
                                echo '<span class="badge bg-warning text-dark p-2 w-100">Menunggu Pemeriksaan</span>';
                            } elseif($row['status'] == "Menunggu Obat"){
                                echo '<span class="badge bg-info text-white p-2 w-100">Menunggu Obat</span>';
                            }
                            ?>
                        </td>
                        
                        <!-- BAGIAN TOMBOL AKSI KONDISIONAL (HANYA PROSES AKTIF) -->
                        <td class="text-center">
                            <?php
                            // --- KONDISI STATUS: MENUNGGU PEMERIKSAAN ---
                            if($row['status'] == "Menunggu Pemeriksaan"){
                                if($_SESSION['role'] == "Dokter"){
                            ?>
                                    <a href="pemeriksaan.php?id=<?= $row['id_daftar']; ?>" class="btn btn-success btn-sm w-100">
                                        Periksa
                                    </a>
                            <?php
                                } elseif($_SESSION['role'] == "Admin"){
                            ?>
                                    <span class="badge bg-secondary p-2 w-100 text-wrap">
                                        Menunggu Dokter
                                    </span>
                            <?php
                                } else {
                            ?>
                                    <span class="text-muted">-</span>
                            <?php
                                }
                            }
                            
                            // --- KONDISI STATUS: MENUNGGU OBAT ---
                            elseif($row['status'] == "Menunggu Obat"){
                                if($_SESSION['role'] == "Admin" || $_SESSION['role'] == "Dokter" || $_SESSION['role'] == "Apoteker"){
                            ?>
                                    <span class="badge bg-secondary p-2 w-100 text-wrap">
                                        Menunggu Apotek
                                    </span>
                            <?php
                                } else {
                            ?>
                                    <span class="text-muted">-</span>
                            <?php
                                }
                            }
                            ?>
                        </td>
                    </tr>
                <?php
                }
            } else {
            ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Tidak ada antrean atau pendaftaran aktif saat ini.
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>

        </table>
    </div>

</div>

<!-- Fitur Pencarian Realtime menggunakan JavaScript -->
<script>
document.getElementById("searchPasien").addEventListener("keyup", function () {
    let keyword = this.value.toLowerCase();
    let rows = document.querySelectorAll("#tabelPasien tbody tr");

    rows.forEach(function (row) {
        if (row.cells.length === 1) return; // Abaikan baris "Tidak ada antrean"
        
        let text = row.textContent.toLowerCase();
        if (text.includes(keyword)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
});
</script>

<?php
include '../includes/footer.php';
?>