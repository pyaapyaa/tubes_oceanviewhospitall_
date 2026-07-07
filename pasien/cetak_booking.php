<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != "Pasien") {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';

if (!isset($_GET['id'])) {
    die("Data booking tidak ditemukan.");
}

$id = (int) $_GET['id'];

$query = mysqli_query($conn,"
SELECT
    booking.*,
    pasien.nama,
    dokter.nama_dokter
FROM booking
JOIN pasien ON booking.id_pasien = pasien.id_pasien
JOIN dokter ON booking.id_dokter = dokter.id_dokter
WHERE booking.id_booking='$id'
LIMIT 1
");

$data = mysqli_fetch_assoc($query);

if(!$data){
    die("Booking tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Bukti Booking</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f5f5f5;
}

.card-booking{
    max-width:650px;
    margin:40px auto;
    border-radius:15px;
}

@media print{
    button, a{
        display:none !important;
    }
    body{
        background:white;
    }
    .card-booking{
        box-shadow: none !important;
        margin: 0 auto;
    }
}
</style>

</head>

<body>

<div class="card shadow card-booking">

    <div class="card-header bg-primary text-white text-center py-3">
        <h3>🏥 Ocean View Hospital</h3>
        <h5 class="mb-0 fw-light">Bukti Booking Berobat</h5>
    </div>

    <div class="card-body p-4">

        <table class="table align-middle">
            <tr>
                <th width="220">Kode Booking</th>
                <td><?= htmlspecialchars($data['kode_booking']); ?></td>
            </tr>

            <!-- UPDATE 1: Nomor Antrean Lebih Besar -->
            <tr>
                <th>Nomor Antrean</th>
                <td>
                    <span class="badge bg-primary fs-5 px-4 py-2">
                        <?= htmlspecialchars($data['nomor_antrian']); ?>
                    </span>
                </td>
            </tr>

            <tr>
                <th>Nama Pasien</th>
                <td><?= htmlspecialchars($data['nama']); ?></td>
            </tr>

            <tr>
                <th>Dokter</th>
                <td><?= htmlspecialchars($data['nama_dokter']); ?></td>
            </tr>

            <tr>
                <th>Tanggal</th>
                <td><?= date('d-m-Y',strtotime($data['tanggal_booking'])); ?></td>
            </tr>

            <tr>
                <th>Jam</th>
                <td><?= htmlspecialchars($data['jam_booking']); ?></td>
            </tr>

            <!-- UPDATE 2: Status Menjadi Badge -->
            <tr>
                <th>Status</th>
                <td>
                    <?php
                    if($data['status'] == "Menunggu Check-in"){
                        echo '<span class="badge bg-warning text-dark">Menunggu Check-in</span>';
                    } elseif($data['status'] == "Sudah Check-in"){
                        echo '<span class="badge bg-info">Sudah Check-in</span>';
                    } elseif($data['status'] == "Selesai"){
                        echo '<span class="badge bg-success">Selesai</span>';
                    } else {
                        echo '<span class="badge bg-danger">'.htmlspecialchars($data['status']).'</span>';
                    }
                    ?>
                </td>
            </tr>

            <tr>
                <th>Keluhan</th>
                <td><?= htmlspecialchars($data['keluhan']); ?></td>
            </tr>
        </table>

        <div class="alert alert-info">
            Datang minimal <strong>15 menit</strong> sebelum jadwal pemeriksaan dan tunjukkan bukti booking ini kepada petugas.
        </div>

        <!-- UPDATE 3: Tanggal Cetak -->
        <p class="text-end text-muted mb-4 small">
            Dicetak pada : <?= date('d-m-Y H:i'); ?>
        </p>

        <div class="text-center mb-2">
            <button onclick="window.print()" class="btn btn-success me-2">
                🖨 Cetak Bukti Booking
            </button>
            <a href="riwayat_booking.php" class="btn btn-secondary">
                Kembali
            </a>
        </div>

        <!-- UPDATE 4: Footer -->
        <hr>
        <p class="text-center text-muted small mb-0">
            © 2026 Ocean View Hospital <br>
            Sistem Informasi Manajemen Pasien Berbasis Web
        </p>

    </div>

</div>

</body>
</html>