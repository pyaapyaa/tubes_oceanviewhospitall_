<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';

$id = $_GET['id'];

mysqli_query($conn,"DELETE FROM obat WHERE id_obat='$id'");

echo "<script>
        alert('Data obat berhasil dihapus');
        window.location='obat.php';
      </script>";
?>