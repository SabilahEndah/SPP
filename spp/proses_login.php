<?php
session_start();
include 'koneksi.php';

$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$password = mysqli_real_escape_string($koneksi, $_POST['password']);

$query = mysqli_query($koneksi, "
    SELECT * FROM tb_petugas
    WHERE username = '$username'
    LIMIT 1
");

$data = mysqli_fetch_assoc($query);

if ($data) {
    $passwordDatabase = $data['password'];

    if ($passwordDatabase == $password || $passwordDatabase == md5($password)) {

        $_SESSION['id_petugas'] = $data['id_petugas'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['nama_petugas'] = $data['nama_petugas'];
        $_SESSION['level'] = $data['level'];

        $_SESSION['swal'] = [
            'icon' => 'success',
            'title' => 'Login Berhasil',
            'text' => 'Selamat datang, ' . $data['nama_petugas']
        ];

        if ($data['level'] == 'admin') {
            header("Location: admin/index.php");
            exit;
        } elseif ($data['level'] == 'petugas') {
            header("Location: petugas/index.php");
            exit;
        } elseif ($data['level'] == 'siswa') {
            header("Location: siswa/index.php");
            exit;
        } else {
            $_SESSION['swal'] = [
                'icon' => 'error',
                'title' => 'Login Gagal',
                'text' => 'Level pengguna tidak dikenali'
            ];

            header("Location: login.php");
            exit;
        }

    } else {
        $_SESSION['swal'] = [
            'icon' => 'error',
            'title' => 'Login Gagal',
            'text' => 'Password yang Anda masukkan salah'
        ];

        header("Location: login.php");
        exit;
    }
} else {
    $_SESSION['swal'] = [
        'icon' => 'error',
        'title' => 'Login Gagal',
        'text' => 'Username tidak ditemukan'
    ];

    header("Location: login.php");
    exit;
}
?>