<?php
$koneksi = mysqli_connect("localhost", "root", "", "spp");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

function e($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

function rupiah($angka) {
    $angka = preg_replace('/[^0-9]/', '', $angka ?? 0);

    if ($angka == '') {
        $angka = 0;
    }

    return "Rp " . number_format((int)$angka, 0, ',', '.');
}
?>