<?php
session_start();

if (isset($_SESSION['level'])) {
    if ($_SESSION['level'] == 'admin') {
        header("Location: admin/index.php");
        exit;
    } elseif ($_SESSION['level'] == 'petugas') {
        header("Location: petugas/index.php");
        exit;
    } elseif ($_SESSION['level'] == 'siswa') {
        header("Location: siswa/index.php");
        exit;
    }
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Login SPP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-primary">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4">
                    <h3 class="text-center mb-2">Login SPP</h3>
                    <p class="text-center text-muted mb-4">
                        Masuk sebagai admin, petugas, atau siswa
                    </p>

                    <form action="proses_login.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Masukkan username / NISN" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Login
                        </button>
                    </form>
                    <div class="text-center mt-3">
    <a href="registrasi.php" class="text-decoration-none">
        Belum punya akun siswa? Registrasi
    </a>
</div>

                    <div class="alert alert-info mt-4 mb-0 small">
                        Untuk siswa, username dapat menggunakan NISN.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_SESSION['swal'])) { ?>
<script>
    Swal.fire({
        icon: <?= json_encode($_SESSION['swal']['icon']); ?>,
        title: <?= json_encode($_SESSION['swal']['title']); ?>,
        text: <?= json_encode($_SESSION['swal']['text']); ?>,
        confirmButtonColor: '#0d6efd'
    });
</script>
<?php unset($_SESSION['swal']); } ?>

</body>
</html>