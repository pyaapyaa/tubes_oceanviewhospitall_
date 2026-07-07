<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';

$id = $_GET['id'];

mysqli_query($conn,"DELETE FROM dokter WHERE id_dokter='$id'");

echo "<script>
        alert('Data dokter berhasil dihapus');
        window.location='dokter.php';
      </script>";
?>