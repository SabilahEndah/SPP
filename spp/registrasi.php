<?php
session_start();
require_once 'koneksi.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function setSwal($icon, $title, $text)
{
    $_SESSION['swal'] = [
        'icon' => $icon,
        'title' => $title,
        'text' => $text
    ];
}

function buatIdPetugasBaru($koneksi)
{
    $qKolom = mysqli_query($koneksi, "SHOW COLUMNS FROM tb_petugas LIKE 'id_petugas'");
    $kolom = mysqli_fetch_assoc($qKolom);

    $type = strtolower($kolom['Type'] ?? '');
    $extra = strtolower($kolom['Extra'] ?? '');

    if (strpos($extra, 'auto_increment') !== false) {
        return null;
    }

    if (strpos($type, 'int') !== false) {
        $q = mysqli_query($koneksi, "
            SELECT COALESCE(MAX(CAST(id_petugas AS UNSIGNED)), 0) + 1 AS id_baru
            FROM tb_petugas
        ");

        $d = mysqli_fetch_assoc($q);
        return $d['id_baru'];
    }

    $q = mysqli_query($koneksi, "SELECT id_petugas FROM tb_petugas");
    $max = 0;

    while ($row = mysqli_fetch_assoc($q)) {
        preg_match('/\d+/', $row['id_petugas'], $match);

        if (isset($match[0])) {
            $angka = (int) $match[0];

            if ($angka > $max) {
                $max = $angka;
            }
        }
    }

    $next = $max + 1;

    return 'S' . str_pad($next, 3, '0', STR_PAD_LEFT);
}

if (isset($_POST['registrasi'])) {
    $nisn = mysqli_real_escape_string($koneksi, $_POST['nisn']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $konfirmasi_password = mysqli_real_escape_string($koneksi, $_POST['konfirmasi_password']);

    try {
        if ($password != $konfirmasi_password) {
            throw new Exception("Konfirmasi password tidak sama");
        }

        if (strlen($password) < 4) {
            throw new Exception("Password minimal 4 karakter");
        }

        $qSiswa = mysqli_query($koneksi, "
            SELECT nisn, nama
            FROM tb_siswa
            WHERE nisn = '$nisn'
            LIMIT 1
        ");

        $siswa = mysqli_fetch_assoc($qSiswa);

        if (!$siswa) {
            throw new Exception("NISN tidak ditemukan di data siswa");
        }

        $qAkun = mysqli_query($koneksi, "
            SELECT username
            FROM tb_petugas
            WHERE username = '$nisn'
            LIMIT 1
        ");

        if (mysqli_num_rows($qAkun) > 0) {
            throw new Exception("Akun dengan NISN ini sudah terdaftar");
        }

        $id_petugas = buatIdPetugasBaru($koneksi);
        $nama_petugas = mysqli_real_escape_string($koneksi, $siswa['nama']);
        $passwordHash = md5($password);
        $level = 'siswa';

        if ($id_petugas === null) {
            mysqli_query($koneksi, "
                INSERT INTO tb_petugas
                (username, password, nama_petugas, level)
                VALUES
                ('$nisn', '$passwordHash', '$nama_petugas', '$level')
            ");
        } else {
            mysqli_query($koneksi, "
                INSERT INTO tb_petugas
                (id_petugas, username, password, nama_petugas, level)
                VALUES
                ('$id_petugas', '$nisn', '$passwordHash', '$nama_petugas', '$level')
            ");
        }

        setSwal(
            'success',
            'Registrasi Berhasil',
            'Akun siswa berhasil dibuat. Silakan login menggunakan NISN dan password.'
        );

        header("Location: login.php");
        exit;

    } catch (Exception $e) {
        setSwal(
            'error',
            'Registrasi Gagal',
            $e->getMessage()
        );

        header("Location: registrasi.php");
        exit;
    }
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Registrasi Siswa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-success">

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4">
                    <h3 class="text-center mb-2">Registrasi Siswa</h3>
                    <p class="text-center text-muted mb-4">
                        Daftar akun siswa menggunakan NISN
                    </p>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">NISN</label>
                            <input type="text" name="nisn" class="form-control" placeholder="Masukkan NISN siswa" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="konfirmasi_password" class="form-control" placeholder="Ulangi password" required>
                        </div>

                        <button type="submit" name="registrasi" class="btn btn-success w-100">
                            Registrasi
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="login.php" class="text-decoration-none">
                            Sudah punya akun? Login
                        </a>
                    </div>

                    <div class="alert alert-info mt-4 mb-0 small">
                        Registrasi hanya bisa dilakukan jika NISN sudah terdaftar di data siswa.
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
        confirmButtonColor: '#198754'
    });
</script>
<?php unset($_SESSION['swal']); } ?>

</body>
</html>