<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';

$id = (int) $_GET['id'];

$query = mysqli_query($conn, "SELECT * FROM users WHERE id_user='$id'");
$data = mysqli_fetch_assoc($query);

$pesan = "";

if (isset($_POST['update'])) {

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

        // cek username digunakan user lain atau tidak
        $cek = mysqli_query($conn, "SELECT * FROM users
        WHERE username='$username'
        AND id_user != '$id'");

        if (mysqli_num_rows($cek) > 0) {

            $pesan = "Username sudah digunakan.";

        } else {

            mysqli_query($conn, "UPDATE users SET

                nama='$nama',
                username='$username',
                password='$password',
                role='$role'

                WHERE id_user='$id'
            ");

            echo "<script>
                    alert('Data user berhasil diubah.');
                    window.location='users.php';
                  </script>";
            exit;
        }
    }

    // supaya data tetap tampil jika validasi gagal
    $data['nama'] = $nama;
    $data['username'] = $username;
    $data['password'] = $password;
    $data['role'] = $role;
}

include '../includes/header.php';
?>

<div class="container mt-5">

    <h2 class="mb-4">Edit User</h2>

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
                value="<?= htmlspecialchars($data['nama']); ?>"
                required>

        </div>

        <div class="mb-3">

            <label class="form-label">Username</label>

            <input
                type="text"
                name="username"
                class="form-control"
                value="<?= htmlspecialchars($data['username']); ?>"
                required>

        </div>

        <div class="mb-3">

            <label class="form-label">Password</label>

            <input
                type="text"
                name="password"
                class="form-control"
                value="<?= htmlspecialchars($data['password']); ?>"
                required>

            <small class="text-muted">
                Minimal 6 karakter.
            </small>

        </div>

        <div class="mb-3">

            <label class="form-label">Role</label>

            <select name="role" class="form-control" required>

                <option value="">-- Pilih Role --</option>

                <option value="Admin"
                <?= ($data['role']=="Admin") ? "selected" : ""; ?>>
                    Admin
                </option>

            </select>

        </div>

        <button type="submit" name="update" class="btn btn-warning">
            Update
        </button>

        <a href="users.php" class="btn btn-secondary">
            Kembali
        </a>

    </form>

</div>

<?php
include '../includes/footer.php';
?>s