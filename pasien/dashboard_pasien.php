<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != "Pasien") {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';
include '../includes/header.php';

$id_user = $_SESSION['id_user'];

// ambil riwayat pasien berdasarkan user login
$query = mysqli_query($conn, "
SELECT 
    pendaftaran.id_daftar,
    pendaftaran.tanggal,
    pendaftaran.keluhan,
    pendaftaran.status,
    dokter.nama_dokter
FROM pendaftaran
JOIN pasien ON pendaftaran.id_pasien = pasien.id_pasien
JOIN dokter ON pendaftaran.id_dokter = dokter.id_dokter
WHERE pasien.id_user = '$id_user'
ORDER BY pendaftaran.id_daftar DESC
");
?>

<div class="container mt-5">

    <h3 class="fw-bold mb-4">Dashboard Pasien</h3>

    <div class="card shadow">
        <div class="card-header bg-primary text-white fw-bold">
            Riwayat Berobat Saya
        </div>

        <div class="card-body">

            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Dokter</th>
                        <th>Keluhan</th>
                        <th>Status</th>
                        <th>Aksi</th>
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
                        <td class="text-center"><?= $row['tanggal']; ?></td>
                        <td><?= htmlspecialchars($row['nama_dokter']); ?></td>
                        <td><?= htmlspecialchars($row['keluhan']); ?></td>
                        <td class="text-center">
                            <?php if($row['status'] == "Selesai"){ ?>
                                <span class="badge bg-success">Selesai</span>
                            <?php } elseif($row['status'] == "Menunggu Obat"){ ?>
                                <span class="badge bg-info">Menunggu Obat</span>
                            <?php } else { ?>
                                <span class="badge bg-warning text-dark">
                                    <?= $row['status']; ?>
                                </span>
                            <?php } ?>
                        </td>

                        <td class="text-center">
                            <a href="../admin/detail_pemeriksaan.php?id=<?= $row['id_daftar']; ?>"
                               class="btn btn-primary btn-sm">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                <?php
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Belum ada riwayat pemeriksaan.
                        </td>
                    </tr>
                <?php } ?>
                </tbody>

            </table>

        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>