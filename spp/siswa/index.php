<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/cek_akses.php';

$title = "Dashboard Siswa";

$querySiswa = mysqli_query($koneksi, "
    SELECT 
        s.nisn,
        s.nis,
        s.nama,
        s.no_tlp,
        s.alamat,
        k.nama_kelas,
        k.komp_keahlian,
        spp.tahun,
        spp.nominal
    FROM tb_siswa s
    LEFT JOIN tb_kelas k 
        ON s.id_kelas = k.id_kelas
    LEFT JOIN tb_spp spp 
        ON s.id_spp = spp.id_spp
    WHERE s.nisn = '$nisn_login'
    LIMIT 1
");

$siswa = mysqli_fetch_assoc($querySiswa);

$queryTotal = mysqli_query($koneksi, "
    SELECT 
        COALESCE(SUM(CAST(jumlah_bulan AS UNSIGNED)), 0) AS total_bulan,
        COALESCE(SUM(CAST(jumlah_bayar AS UNSIGNED)), 0) AS total_bayar,
        COALESCE(SUM(CAST(nominal_bayar AS UNSIGNED)), 0) AS total_tagihan,
        MAX(tgl_bayar) AS terakhir_bayar
    FROM tb_pembayaran
    WHERE nisn = '$nisn_login'
");

$total = mysqli_fetch_assoc($queryTotal);

$total_bulan = (int)($total['total_bulan'] ?? 0);
$total_bayar = (int)($total['total_bayar'] ?? 0);
$total_tagihan = (int)($total['total_tagihan'] ?? 0);

$sisa = $total_tagihan - $total_bayar;

if ($sisa < 0) {
    $sisa = 0;
}

if ($total_bulan == 0) {
    $status = "Belum Ada Pembayaran";
    $badge = "bg-secondary";
} elseif ($total_bayar >= $total_tagihan) {
    $status = "Sudah Lunas";
    $badge = "bg-success";
} else {
    $status = "Belum Lunas";
    $badge = "bg-danger";
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Dashboard Siswa</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f9ff;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(180deg, #198754, #0f5132);
            position: fixed;
            top: 0;
            left: 0;
            color: white;
            padding: 20px;
        }

        .sidebar .brand {
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            padding: 20px 10px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 25px;
        }

        .sidebar a {
            display: block;
            color: #e8fff2;
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 10px;
            margin-bottom: 8px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: white;
            color: #198754;
            font-weight: bold;
        }

        .content {
            margin-left: 260px;
            padding: 25px;
        }

        .top-card {
            background: linear-gradient(135deg, #198754, #20c997);
            color: white;
            border-radius: 18px;
        }

        .card {
            border: none;
            border-radius: 15px;
        }
    </style>
</head>

<body>

<div class="sidebar">
    <div class="brand">Siswa SPP</div>

    <a href="index.php" class="active">Dashboard</a>
    <a href="tagihan_semester.php">Tagihan Semester</a>
    <a href="riwayat_pembayaran.php">Riwayat Pembayaran</a>
    <a href="../logout.php" class="bg-light text-success fw-bold mt-4">Logout</a>
</div>

<div class="content">

    <div class="card top-card shadow-sm mb-4">
        <div class="card-body p-4">
            <h3 class="mb-1">Dashboard Siswa</h3>
            <p class="mb-0">
                Selamat datang, <?= e($_SESSION['nama_petugas'] ?? 'Siswa'); ?>.
            </p>
        </div>
    </div>

    <?php if ($siswa) { ?>

        <div class="row g-3 mb-4">

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Status Pembayaran</h6>
                        <h5>
                            <span class="badge <?= $badge; ?>">
                                <?= e($status); ?>
                            </span>
                        </h5>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Total Dibayar</h6>
                        <h4 class="text-success"><?= rupiah($total_bayar); ?></h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted">Sisa Tagihan</h6>
                        <h4 class="text-danger"><?= rupiah($sisa); ?></h4>
                    </div>
                </div>
            </div>

        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Data Siswa</h5>
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <small class="text-muted">NISN</small>
                            <h6><?= e($siswa['nisn']); ?></h6>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <small class="text-muted">NIS</small>
                            <h6><?= e($siswa['nis']); ?></h6>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <small class="text-muted">Nama</small>
                            <h6><?= e($siswa['nama']); ?></h6>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <small class="text-muted">Kelas</small>
                            <h6><?= e($siswa['nama_kelas']); ?></h6>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <small class="text-muted">Kompetensi Keahlian</small>
                            <h6><?= e($siswa['komp_keahlian']); ?></h6>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <small class="text-muted">Nominal SPP per Bulan</small>
                            <h6><?= rupiah($siswa['nominal']); ?></h6>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="border rounded p-3">
                            <small class="text-muted">Alamat</small>
                            <h6><?= e($siswa['alamat']); ?></h6>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    <?php } else { ?>

        <div class="alert alert-danger">
            Data siswa tidak ditemukan. Pastikan username akun siswa sama dengan NISN di tabel <b>tb_siswa</b>.
            <br>
            Username login saat ini: <b><?= e($nisn_login); ?></b>
        </div>

    <?php } ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>