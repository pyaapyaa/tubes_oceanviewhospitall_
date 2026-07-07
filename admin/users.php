<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';
include '../includes/header.php';

$query = mysqli_query($conn, "SELECT * FROM users ORDER BY id_user DESC");
?>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Data User</h2>
        <div class="d-flex">
            <input type="text"
                   id="searchUser"
                   class="form-control me-2"
                   placeholder="Cari user..."
                   style="width:220px;">
            <a href="tambah_user.php" class="btn btn-success text-nowrap">
                + Tambah User
            </a>
        </div>
    </div>

    <table class="table table-bordered table-striped table-hover" id="tabelUser">

        <thead class="table-primary">
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Role</th>
                <th width="170">Aksi</th>
            </tr>
        </thead>

        <tbody>

        <?php
        $no = 1;

        while ($row = mysqli_fetch_assoc($query)) {
        ?>

            <tr>

                <td><?= $no++; ?></td>

                <td><?= htmlspecialchars($row['nama']); ?></td>

                <td><?= htmlspecialchars($row['username']); ?></td>

                <td><?= htmlspecialchars($row['role']); ?></td>

                <td>

                    <a href="edit_user.php?id=<?= $row['id_user']; ?>"
                       class="btn btn-warning btn-sm">
                        Edit
                    </a>

                    <a href="hapus_user.php?id=<?= $row['id_user']; ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                        Hapus
                    </a>

                </td>

            </tr>

        <?php
        }
        ?>

        </tbody>

    </table>

</div>

<script>
document.getElementById("searchUser").addEventListener("keyup", function(){

    let keyword = this.value.toLowerCase();

    let rows = document.querySelectorAll("#tabelUser tbody tr");

    rows.forEach(function(row){

        let isi = row.textContent.toLowerCase();

        if(isi.indexOf(keyword) > -1){
            row.style.display="";
        }else{
            row.style.display="none";
        }

    });

});
</script>

<?php
include '../includes/footer.php';
?>