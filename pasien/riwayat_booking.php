<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != "Pasien") {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';
include '../includes/header.php';

$id_user = $_SESSION['id_user'];

// mengambil data booking milik pasien yang login
$query = mysqli_query($conn, "
SELECT
    booking.*,
    dokter.nama_dokter
FROM booking
JOIN pasien ON booking.id_pasien = pasien.id_pasien
JOIN dokter ON booking.id_dokter = dokter.id_dokter
WHERE pasien.id_user='$id_user'
ORDER BY booking.id_booking DESC
");
?>

<div class="container mt-4">

    <div class="card shadow">

        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">📋 Riwayat Booking Saya</h4>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-striped align-middle">

                    <thead class="table-light text-center">
                        <tr>
                            <th>No</th>
                            <th>Kode Booking</th>
                            <th>No. Antrean</th>
                            <th>Dokter</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Keluhan</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php
                    $no = 1;

                    if(mysqli_num_rows($query) > 0){

                        while($row = mysqli_fetch_assoc($query)){
                    ?>

                    <tr>

                        <td class="text-center"><?= $no++; ?></td>

                        <td><?= htmlspecialchars($row['kode_booking']); ?></td>

                        <!-- Menampilkan Nomor Antrean Pasien -->
                        <td class="text-center fw-bold text-primary">
                            <?= htmlspecialchars($row['nomor_antrian'] ?? '-'); ?>
                        </td>

                        <td><?= htmlspecialchars($row['nama_dokter']); ?></td>

                        <td class="text-center">
                            <?= date('d-m-Y', strtotime($row['tanggal_booking'])); ?>
                        </td>

                        <td class="text-center">
                            <?= htmlspecialchars($row['jam_booking']); ?>
                        </td>

                        <td><?= htmlspecialchars($row['keluhan']); ?></td>

                        <td class="text-center">

                            <?php
                            if($row['status'] == "Menunggu Check-in"){
                                echo '<span class="badge bg-warning text-dark">Menunggu Check-in</span>';
                            }elseif($row['status'] == "Sudah Check-in"){
                                echo '<span class="badge bg-info">Sudah Check-in</span>';
                            }elseif($row['status'] == "Selesai"){
                                echo '<span class="badge bg-success">Selesai</span>';
                            }else{
                                echo '<span class="badge bg-danger">'.htmlspecialchars($row['status']).'</span>';
                            }
                            ?>

                        </td>

                    </tr>

                    <?php
                        }

                    }else{
                    ?>

                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Belum ada booking.
                        </td>
                    </tr>

                    <?php } ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php include '../includes/footer.php'; ?>