<?php
include '../includes/header.php';
?>

<style>
.login-card{
    border: none;
    border-radius: 12px;
    overflow: hidden;
}

.login-header{
    background:#0b5ed7;
    color:#fff;
    text-align:center;
    padding:25px;
}

.login-header .logo{
    font-size:50px;
    margin-bottom:10px;
}

.login-header h3{
    margin-bottom:5px;
    font-weight:600;
}

.login-header p{
    margin:0;
    font-size:14px;
    opacity:.9;
}

.login-body{
    padding:30px;
}

.form-label{
    font-weight:600;
}

.form-control{
    height:45px;
    border-radius:8px;
}

.btn-login{
    height:45px;
    border-radius:8px;
    font-weight:600;
}

.login-footer{
    text-align:center;
    color:#6c757d;
    font-size:13px;
    padding:15px;
    border-top:1px solid #eee;
}
</style>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-5">

            <div class="card shadow login-card">

                <div class="login-header">

                    <div class="logo">
                        🏥
                    </div>

                    <h3>Ocean View Hospital</h3>

                    <p>Sistem Informasi Manajemen Pasien</p>

                </div>

                <div class="login-body">

                    <form action="proses_login.php" method="POST">

                        <div class="mb-3">

                            <label class="form-label">
                                Username
                            </label>

                            <input
                                type="text"
                                name="username"
                                class="form-control"
                                placeholder="Masukkan username"
                                required>

                        </div>

                        <div class="mb-4">

                            <label class="form-label">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                placeholder="Masukkan password"
                                required>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary w-100 btn-login">

                            Login

                        </button>

                    </form>

                </div>

                <div class="login-footer">

                    © 2026 Ocean View Hospital

                </div>

            </div>

        </div>

    </div>

</div>

<?php
include '../includes/footer.php';
?>