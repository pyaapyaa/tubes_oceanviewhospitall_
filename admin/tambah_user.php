<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';

$pesan = "";

if (isset($_POST['simpan'])) {

    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // ================= VALIDASI =================

    if ($nama == "") {

        $pesan = "Nama tidak boleh kosong.";

    } elseif ($username == "") {

        $pesan = "Username tidak boleh kosong.";

    } elseif (strlen($username) < 4) {

        $pesan = "Username minimal 4 karakter.";

    } elseif ($password == "") {

        $pesan = "Password tidak boleh kosong.";

    } elseif (strlen($password) < 6) {

        $pesan = "Password minimal 6 karakter.";

    } elseif ($role == "") {

        $pesan = "Silakan pilih role.";

    } else {

        // Cek username sudah digunakan atau belum
        $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");

        if (mysqli_num_rows($cek) > 0) {

            $pesan = "Username sudah digunakan.";

        } else {

            mysqli_query($conn, "INSERT INTO users
            (nama, username, password, role)
            VALUES
            (
                '" . mysqli_real_escape_string($conn, $nama) . "',
                '" . mysqli_real_escape_string($conn, $username) . "',
                '" . mysqli_real_escape_string($conn, $password) . "',
                '" . mysqli_real_escape_string($conn, $role) . "'
            )");

            echo "<script>
                    alert('User berhasil ditambahkan.');
                    window.location='users.php';
                  </script>";
            exit;
        }
    }
}

include '../includes/header.php';
?>

<div class="container mt-5">

    <h2 class="mb-4">Tambah User</h2>

    <?php if($pesan != "") { ?>

        <div class="alert alert-warning">
            <?= $pesan; ?>
        </div>

    <?php } ?>

    <form method="POST">

        <div class="mb-3">

            <label class="form-label">Nama</label>

            <input
                type="text"
                name="nama"
                class="form-control"
                value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>"
                required>

        </div>

        <div class="mb-3">

            <label class="form-label">Username</label>

            <input
                type="text"
                name="username"
                class="form-control"
                value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                required>

        </div>

        <div class="mb-3">

            <label class="form-label">Password</label>

            <input
                type="password"
                name="password"
                class="form-control"
                required>

            <small class="text-muted">
                Minimal 6 karakter.
            </small>

        </div>

        <!-- 📍 LANGKAH 1 — Perubahan komponen dropdown role baru -->
        <div class="mb-3">

            <label class="form-label">Role</label>

            <select name="role" class="form-select" required>

                <option value="">-- Pilih Role --</option>

                <option value="Admin"
                    <?= (isset($_POST['role']) && $_POST['role']=="Admin") ? "selected" : ""; ?>>
                    Admin
                </option>

                <option value="Dokter"
                    <?= (isset($_POST['role']) && $_POST['role']=="Dokter") ? "selected" : ""; ?>>
                    Dokter
                </option>

                <option value="Apoteker"
                    <?= (isset($_POST['role']) && $_POST['role']=="Apoteker") ? "selected" : ""; ?>>
                    Apoteker
                </option>

            </select>

        </div>

        <button type="submit" name="simpan" class="btn btn-success">
            Simpan
        </button>

        <a href="users.php" class="btn btn-secondary">
            Kembali
        </a>

    </form>

</div>

<?php
include '../includes/footer.php';
?>