<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';
include '../includes/header.php';

// QUERY FIX
$query = mysqli_query($conn, "
SELECT
    pemeriksaan.id_periksa,
    pemeriksaan.id_daftar,
    pemeriksaan.diagnosa,
    pemeriksaan.resep,
    pemeriksaan.tanggal_periksa,

    pendaftaran.status,
    pasien.nama,
    dokter.nama_dokter

FROM pemeriksaan
JOIN pendaftaran ON pemeriksaan.id_daftar = pendaftaran.id_daftar
JOIN pasien ON pendaftaran.id_pasien = pasien.id_pasien
JOIN dokter ON pendaftaran.id_dokter = dokter.id_dokter

WHERE pendaftaran.status = 'Selesai'

ORDER BY pemeriksaan.id_periksa DESC
");
?>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold">Riwayat Pemeriksaan Pasien</h2>

        <input type="text" id="searchRiwayat" class="form-control"
               placeholder="Cari pasien..."
               style="width:250px;">
    </div>

    <div class="card shadow">
        <div class="card-header bg-primary text-white fw-bold">
            Data Riwayat Pemeriksaan
        </div>

        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-bordered table-striped align-middle" id="tabelRiwayat">

                    <thead class="table-primary text-center">
                        <tr>
                            <th>No</th>
                            <th>Pasien</th>
                            <th>Dokter</th>
                            <th>Diagnosa</th>
                            <th>Resep</th>
                            <th>Tanggal</th>
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
                            <td><?= htmlspecialchars($row['nama']); ?></td>
                            <td><?= htmlspecialchars($row['nama_dokter']); ?></td>

                            <td><?= nl2br(htmlspecialchars($row['diagnosa'])); ?></td>

                            <!-- RESEP RAPI -->
                            <td>
                                <?php
                                $resep_baris = preg_split('/\r\n|\r|\n/', $row['resep']);

                                foreach ($resep_baris as $r) {

                                    $r = trim($r);
                                    if ($r == '') continue;

                                    $item = explode("|", $r);

                                    if (isset($item[0]) && isset($item[1])) {
                                        echo "💊 " . htmlspecialchars($item[0]) .
                                             " <span class='text-muted'>(" . htmlspecialchars($item[1]) . ")</span><br>";
                                    } else {
                                        echo "💊 " . htmlspecialchars($r) . "<br>";
                                    }
                                }
                                ?>
                            </td>

                            <td class="text-center">
                                <?= date('d-m-Y', strtotime($row['tanggal_periksa'])); ?>
                            </td>

                            <td class="text-center">
                                <span class="badge bg-success w-100 p-2">
                                    Selesai
                                </span>
                            </td>

                            <td class="text-center">
                                <a href="detail_pemeriksaan.php?id=<?= $row['id_daftar']; ?>"
                                   class="btn btn-primary btn-sm w-100">
                                    Lihat
                                </a>
                            </td>
                        </tr>

                    <?php
                        }
                    } else {
                    ?>

                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Belum ada data riwayat pemeriksaan.
                            </td>
                        </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>
        </div>
    </div>

</div>

<!-- SEARCH -->
<script>
document.getElementById("searchRiwayat").addEventListener("keyup", function () {
    let keyword = this.value.toLowerCase();
    let rows = document.querySelectorAll("#tabelRiwayat tbody tr");

    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(keyword) ? "" : "none";
    });
});
</script>

<?php include '../includes/footer.php'; ?>