<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';

$id = $_GET['id'];

mysqli_query($conn,"DELETE FROM users WHERE id_user='$id'");

echo "<script>
        alert('User berhasil dihapus');
        window.location='users.php';
      </script>";
?>