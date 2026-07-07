<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != "Admin") {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';

// cek id booking
if (!isset($_GET['id'])) {
    die("ID Booking tidak ditemukan.");
}

$id_booking = (int) $_GET['id'];

// ambil data booking
$query = mysqli_query($conn, "
SELECT * FROM booking
WHERE id_booking='$id_booking'
");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data booking tidak ditemukan.");
}

// masukkan ke tabel pendaftaran
mysqli_query($conn, "
INSERT INTO pendaftaran
(
    id_pasien,
    id_dokter,
    tanggal,
    keluhan,
    status
)
VALUES
(
    '{$data['id_pasien']}',
    '{$data['id_dokter']}',
    CURDATE(),
    '{$data['keluhan']}',
    'Menunggu Pemeriksaan'
)
");

// ubah status booking
mysqli_query($conn, "
UPDATE booking
SET status='Sudah Check-in'
WHERE id_booking='$id_booking'
");

echo "
<script>
alert('Check-in berhasil dilakukan');
window.location='booking.php';
</script>";
exit;
?>