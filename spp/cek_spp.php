<?php
include 'koneksi.php';

if (!isset($_GET['nisn'])) {
    header("Location: index.php");
    exit;
}

$nisn = mysqli_real_escape_string($koneksi, $_GET['nisn']);

$query_siswa = mysqli_query($koneksi, "
    SELECT 
        tb_siswa.nisn,
        tb_siswa.nis,
        tb_siswa.nama,
        tb_siswa.alamat,
        tb_siswa.no_tlp,
        tb_kelas.nama_kelas,
        tb_kelas.kompetensi_keahlian,
        tb_spp.tahun,
        tb_spp.nominal
    FROM tb_siswa
    LEFT JOIN tb_kelas ON tb_siswa.id_kelas = tb_kelas.id_kelas
    LEFT JOIN tb_spp ON tb_siswa.id_spp = tb_spp.id_spp
    WHERE tb_siswa.nisn = '$nisn'
");

$data_siswa = mysqli_fetch_assoc($query_siswa);

if (!$data_siswa) {
    echo "
    <script>
        alert('Data siswa dengan NISN tersebut tidak ditemukan');
        window.location='index.php';
    </script>
    ";
    exit;
}

$query_pembayaran = mysqli_query($koneksi, "
    SELECT * FROM tb_pembayaran
    WHERE nisn = '$nisn'
    ORDER BY tgl_bayar DESC
");

$query_cek = mysqli_query($koneksi, "
    SELECT * FROM cek_pembayaran
    WHERE nisn = '$nisn'
");

$data_cek = mysqli_fetch_assoc($query_cek);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pembayaran SPP</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        .header {
            background: #2c7be5;
            color: white;
            padding: 25px;
            text-align: center;
        }

        .container {
            width: 90%;
            margin: 30px auto;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

        .card h2 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .item {
            background: #f8f9fa;
            padding: 13px;
            border-radius: 8px;
        }

        .item b {
            display: block;
            color: #555;
            margin-bottom: 5px;
        }

        .status {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            color: white;
            font-weight: bold;
        }

        .lunas {
            background: #28a745;
        }

        .belum {
            background: #dc3545;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th {
            background: #2c7be5;
            color: white;
            padding: 12px;
            text-align: left;
        }

        table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        table tr:hover {
            background: #f1f1f1;
        }

        .btn {
            display: inline-block;
            padding: 10px 18px;
            background: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .btn:hover {
            background: #555;
        }

        .total-box {
            background: #eaf3ff;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }

        @media print {
            .btn {
                display: none;
            }

            .header {
                background: white;
                color: black;
            }

            body {
                background: white;
            }
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Detail Pembayaran SPP</h1>
    <p>Informasi pembayaran siswa berdasarkan NISN</p>
</div>

<div class="container">

    <a href="index.php" class="btn">Kembali</a>
    <a href="javascript:window.print()" class="btn">Cetak</a>

    <div class="card">
        <h2>Data Siswa</h2>

        <div class="grid">
            <div class="item">
                <b>NISN</b>
                <?= $data_siswa['nisn']; ?>
            </div>

            <div class="item">
                <b>NIS</b>
                <?= $data_siswa['nis']; ?>
            </div>

            <div class="item">
                <b>Nama Siswa</b>
                <?= $data_siswa['nama']; ?>
            </div>

            <div class="item">
                <b>No Telepon</b>
                <?= $data_siswa['no_tlp']; ?>
            </div>

            <div class="item">
                <b>Kelas</b>
                <?= $data_siswa['nama_kelas']; ?>
            </div>

            <div class="item">
                <b>Kompetensi Keahlian</b>
                <?= $data_siswa['kompetensi_keahlian']; ?>
            </div>

            <div class="item">
                <b>Tahun SPP</b>
                <?= $data_siswa['tahun']; ?>
            </div>

            <div class="item">
                <b>Nominal SPP</b>
                Rp <?= number_format($data_siswa['nominal'], 0, ',', '.'); ?>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Status Pembayaran</h2>

        <?php if ($data_cek) { ?>
            <?php
            $status = $data_cek['status_pembayaran'];
            $class_status = ($status == 'Sudah Lunas') ? 'lunas' : 'belum';
            ?>

            <p>
                Status:
                <span class="status <?= $class_status; ?>">
                    <?= $status; ?>
                </span>
            </p>

            <div class="grid">
                <div class="item">
                    <b>Tanggal Terakhir Bayar</b>
                    <?= $data_cek['tgl_terakhir_bayar']; ?>
                </div>

                <div class="item">
                    <b>Tanggal Sekarang</b>
                    <?= $data_cek['tgl_sekarang']; ?>
                </div>

                <div class="item">
                    <b>Jumlah Bulan</b>
                    <?= $data_cek['jumlah_bulan']; ?> bulan
                </div>
            </div>

        <?php } else { ?>
            <p>Belum ada data status pembayaran.</p>
        <?php } ?>
    </div>

    <div class="card">
        <h2>Riwayat Pembayaran</h2>

        <table>
            <tr>
                <th>No</th>
                <th>Tanggal Bayar</th>
                <th>Batas Pembayaran</th>
                <th>Jumlah Bulan</th>
                <th>Nominal Bayar</th>
                <th>Jumlah Bayar</th>
                <th>Kembalian</th>
                <th>Status</th>
            </tr>

            <?php
            $no = 1;
            $total_bayar = 0;

            if (mysqli_num_rows($query_pembayaran) > 0) {
                while ($pembayaran = mysqli_fetch_assoc($query_pembayaran)) {
                    $total_bayar += (int)$pembayaran['jumlah_bayar'];
            ?>

            <tr>
                <td><?= $no++; ?></td>
                <td><?= $pembayaran['tgl_bayar']; ?></td>
                <td><?= $pembayaran['batas_pembayaran']; ?></td>
                <td><?= $pembayaran['jumlah_bulan']; ?></td>
                <td>Rp <?= number_format($pembayaran['nominal_bayar'], 0, ',', '.'); ?></td>
                <td>Rp <?= number_format($pembayaran['jumlah_bayar'], 0, ',', '.'); ?></td>
                <td>Rp <?= number_format($pembayaran['kembalian'], 0, ',', '.'); ?></td>
                <td><?= $pembayaran['status']; ?></td>
            </tr>

            <?php 
                }
            } else {
            ?>

            <tr>
                <td colspan="8" style="text-align:center;">Belum ada riwayat pembayaran</td>
            </tr>

            <?php } ?>
        </table>

        <div class="total-box">
            <b>Total Pembayaran:</b>
            Rp <?= number_format($total_bayar, 0, ',', '.'); ?>
        </div>
    </div>

</div>

</body>
</html>