<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/cek_akses.php';

$tahun = $_GET['tahun'] ?? date('Y');
$semester = $_GET['semester'] ?? 'genap';

if ($semester == 'ganjil') {
    $bulanSemester = [
        7  => 'Juli',
        8  => 'Agustus',
        9  => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];
} else {
    $bulanSemester = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni'
    ];
}

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

if (!$siswa) {
    die("Data siswa tidak ditemukan. Pastikan username siswa sama dengan NISN.");
}

$nominalSpp = preg_replace('/[^0-9]/', '', $siswa['nominal']);
$nominalSpp = (int) $nominalSpp;

$dataBayar = [];

$queryBayar = mysqli_query($koneksi, "
    SELECT *
    FROM tb_pembayaran
    WHERE nisn = '$nisn_login'
      AND YEAR(batas_pembayaran) = '$tahun'
");

while ($row = mysqli_fetch_assoc($queryBayar)) {
    $bulan = (int) date('n', strtotime($row['batas_pembayaran']));

    $jumlahBayar = preg_replace('/[^0-9]/', '', $row['jumlah_bayar']);
    $jumlahBayar = (int) $jumlahBayar;

    if (!isset($dataBayar[$bulan])) {
        $dataBayar[$bulan] = [
            'total_bayar' => 0,
            'terakhir_bayar' => '-'
        ];
    }

    $dataBayar[$bulan]['total_bayar'] += $jumlahBayar;
    $dataBayar[$bulan]['terakhir_bayar'] = $row['tgl_bayar'];
}

$totalTagihanSemester = 0;
$totalDibayarSemester = 0;
$totalSisaSemester = 0;
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Tagihan Semester Siswa</title>
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

        @media print {
            .sidebar,
            .no-print {
                display: none !important;
            }

            .content {
                margin-left: 0;
                padding: 0;
            }

            body {
                background: white;
            }
        }
    </style>
</head>

<body>

<div class="sidebar">
    <div class="brand">Siswa SPP</div>

    <a href="index.php">Dashboard</a>
    <a href="tagihan_semester.php" class="active">Tagihan Semester</a>
    <a href="riwayat_pembayaran.php">Riwayat Pembayaran</a>
    <a href="../logout.php" class="bg-light text-success fw-bold mt-4">Logout</a>
</div>

<div class="content">

    <div class="card top-card shadow-sm mb-4">
        <div class="card-body p-4">
            <h3 class="mb-1">Tagihan Semester</h3>
            <p class="mb-0">
                Menampilkan tagihan SPP selama 1 semester atau 6 bulan.
            </p>
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
                        <small class="text-muted">Nama Siswa</small>
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
                        <h6><?= rupiah($nominalSpp); ?></h6>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4 no-print">
        <div class="card-body">
            <form method="GET" class="row g-2">

                <div class="col-md-5">
                    <select name="semester" class="form-select">
                        <option value="genap" <?= $semester == 'genap' ? 'selected' : ''; ?>>
                            Semester Genap - Januari s/d Juni
                        </option>

                        <option value="ganjil" <?= $semester == 'ganjil' ? 'selected' : ''; ?>>
                            Semester Ganjil - Juli s/d Desember
                        </option>
                    </select>
                </div>

                <div class="col-md-5">
                    <input type="number" name="tahun" class="form-control" value="<?= e($tahun); ?>">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-success w-100">
                        Tampilkan
                    </button>
                </div>

            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                Tagihan 
                <?= $semester == 'ganjil' ? 'Semester Ganjil' : 'Semester Genap'; ?>
                Tahun <?= e($tahun); ?>
            </h5>

            <button onclick="window.print()" class="btn btn-light btn-sm no-print">
                Cetak
            </button>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-success">
                    <tr>
                        <th>No</th>
                        <th>Bulan</th>
                        <th>Nominal SPP</th>
                        <th>Dibayar</th>
                        <th>Sisa Tagihan</th>
                        <th>Tanggal Terakhir Bayar</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $no = 1;

                    foreach ($bulanSemester as $angkaBulan => $namaBulan) {
                        $tagihan = $nominalSpp;
                        $dibayar = $dataBayar[$angkaBulan]['total_bayar'] ?? 0;
                        $tglTerakhirBayar = $dataBayar[$angkaBulan]['terakhir_bayar'] ?? '-';

                        $sisa = $tagihan - $dibayar;

                        if ($sisa < 0) {
                            $sisa = 0;
                        }

                        if ($dibayar >= $tagihan && $tagihan > 0) {
                            $status = "Lunas";
                            $badge = "bg-success";
                        } elseif ($dibayar > 0 && $dibayar < $tagihan) {
                            $status = "Kurang Bayar";
                            $badge = "bg-warning text-dark";
                        } else {
                            $status = "Belum Lunas";
                            $badge = "bg-danger";
                        }

                        $totalTagihanSemester += $tagihan;
                        $totalDibayarSemester += $dibayar;
                        $totalSisaSemester += $sisa;
                    ?>

                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= e($namaBulan); ?></td>
                            <td><?= rupiah($tagihan); ?></td>
                            <td><?= rupiah($dibayar); ?></td>
                            <td><?= rupiah($sisa); ?></td>
                            <td><?= e($tglTerakhirBayar); ?></td>
                            <td>
                                <span class="badge <?= $badge; ?>">
                                    <?= e($status); ?>
                                </span>
                            </td>
                        </tr>

                    <?php } ?>
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="2" class="text-end">Total Semester</th>
                        <th><?= rupiah($totalTagihanSemester); ?></th>
                        <th><?= rupiah($totalDibayarSemester); ?></th>
                        <th><?= rupiah($totalSisaSemester); ?></th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>