<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['level'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SESSION['level'] != 'siswa') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

$nisn_login = mysqli_real_escape_string($koneksi, $_SESSION['username']);

if (!function_exists('e')) {
    function e($text) {
        return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('rupiah')) {
    function rupiah($angka) {
        $angka = preg_replace('/[^0-9]/', '', $angka ?? 0);

        if ($angka == '') {
            $angka = 0;
        }

        return 'Rp ' . number_format((int)$angka, 0, ',', '.');
    }
}
?>