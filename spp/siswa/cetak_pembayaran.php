<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/cek_akses.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('ID pembayaran tidak ditemukan'); window.location='riwayat_pembayaran.php';</script>";
    exit;
}

$id_pembayaran = mysqli_real_escape_string($koneksi, $_GET['id']);

$query = mysqli_query($koneksi, "
    SELECT 
        p.*,
        s.nis,
        s.nama,
        s.no_tlp,
        s.alamat,
        k.nama_kelas,
        k.komp_keahlian,
        spp.tahun,
        spp.nominal
    FROM tb_pembayaran p
    LEFT JOIN tb_siswa s ON p.nisn = s.nisn
    LEFT JOIN tb_kelas k ON s.id_kelas = k.id_kelas
    LEFT JOIN tb_spp spp ON p.id_spp = spp.id_spp
    WHERE p.id_pembayaran = '$id_pembayaran'
      AND p.nisn = '$nisn_login'
    LIMIT 1
");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data pembayaran tidak ditemukan'); window.location='riwayat_pembayaran.php';</script>";
    exit;
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Bukti Pembayaran</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
            font-size: 14px;
        }

        .print-area {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            padding: 35px;
            border-radius: 10px;
        }

        .kop {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .kop h4,
        .kop p {
            margin: 0;
        }

        .table th {
            width: 35%;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
            }

            .print-area {
                margin: 0;
                max-width: 100%;
                box-shadow: none;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>

<div class="print-area shadow-sm">

    <div class="no-print mb-3">
        <button onclick="window.print()" class="btn btn-success btn-sm">
            Cetak
        </button>

        <a href="riwayat_pembayaran.php" class="btn btn-secondary btn-sm">
            Kembali
        </a>
    </div>

    <div class="kop">
        <h4>BUKTI PEMBAYARAN SPP</h4>
        <p>Sistem Informasi Pembayaran SPP</p>
    </div>

    <table class="table table-bordered">
        <tr>
            <th>ID Pembayaran</th>
            <td><?= e($data['id_pembayaran']); ?></td>
        </tr>

        <tr>
            <th>NISN</th>
            <td><?= e($data['nisn']); ?></td>
        </tr>

        <tr>
            <th>NIS</th>
            <td><?= e($data['nis']); ?></td>
        </tr>

        <tr>
            <th>Nama Siswa</th>
            <td><?= e($data['nama']); ?></td>
        </tr>

        <tr>
            <th>Kelas</th>
            <td><?= e($data['nama_kelas']); ?> - <?= e($data['komp_keahlian']); ?></td>
        </tr>

        <tr>
            <th>Tanggal Bayar</th>
            <td><?= e($data['tgl_bayar']); ?></td>
        </tr>

        <tr>
            <th>Batas Pembayaran</th>
            <td><?= e($data['batas_pembayaran']); ?></td>
        </tr>

        <tr>
            <th>Tahun SPP</th>
            <td><?= e($data['tahun']); ?></td>
        </tr>

        <tr>
            <th>Nominal SPP</th>
            <td><?= rupiah($data['nominal']); ?></td>
        </tr>

        <tr>
            <th>Jumlah Bulan</th>
            <td><?= e($data['jumlah_bulan']); ?> Bulan</td>
        </tr>

        <tr>
            <th>Nominal Bayar</th>
            <td><?= rupiah($data['nominal_bayar']); ?></td>
        </tr>

        <tr>
            <th>Jumlah Bayar</th>
            <td><?= rupiah($data['jumlah_bayar']); ?></td>
        </tr>

        <tr>
            <th>Kembalian</th>
            <td><?= rupiah($data['kembalian']); ?></td>
        </tr>

        <tr>
            <th>Status</th>
            <td><b><?= e($data['status']); ?></b></td>
        </tr>
    </table>

</div>

<script>
    window.print();
</script>

</body>
</html>