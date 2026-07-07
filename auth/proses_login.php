<?php
session_start();

include '../config/koneksi.php';

// Cek apakah data dikirim melalui metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Amankan input dari SQL Injection
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; 

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");

    if (mysqli_num_rows($query) > 0) {

        $data = mysqli_fetch_assoc($query);

        // Verifikasi Password
        if ($password == $data['password']) {

            // Daftarkan session pengguna
            $_SESSION['id_user']  = $data['id_user'];
            $_SESSION['nama']     = $data['nama'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['role']     = $data['role'];

            // =========================================================================
            // UJI COBA RUTE AMAN: DETEKSI PASIEN TERLEBIH DAHULU
            // =========================================================================
            if ($data['role'] == "Pasien") {
                header("Location: ../pasien/dashboard_pasien.php");
                exit;
            } else {
                // Rute default yang lama (Aman untuk Admin, Dokter, Apoteker saat ini)
                header("Location: ../admin/dashboard.php");
                exit;
            }
            // =========================================================================

        } else {
            echo "<script>
                    alert('Password salah!');
                    window.location='login.php';
                  </script>";
            exit;
        }

    } else {
        echo "<script>
                alert('Username tidak ditemukan!');
                window.location='login.php';
              </script>";
        exit;
    }

} else {
    // Jika file diakses langsung tanpa method POST
    header("Location: login.php");
    exit;
}
?>