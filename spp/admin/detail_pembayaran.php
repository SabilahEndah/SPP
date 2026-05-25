<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/cek_akses.php';

$title = "Detail Pembayaran";

$keyword = $_GET['keyword'] ?? '';
$cetak   = $_GET['cetak'] ?? '';

function queryDetailPembayaran($koneksi, $keyword = '') {
    $where = "";

    if ($keyword != '') {
        $keyword = mysqli_real_escape_string($koneksi, $keyword);

        $where = "
            WHERE s.nisn LIKE '%$keyword%'
               OR s.nis LIKE '%$keyword%'
               OR s.nama LIKE '%$keyword%'
        ";
    }

    return mysqli_query($koneksi, "
        SELECT 
            p.id_pembayaran,
            p.nisn,
            p.tgl_bayar,
            p.batas_pembayaran,
            p.jumlah_bulan,
            p.nominal_bayar,
            p.jumlah_bayar,
            p.kembalian,
            p.status,

            s.nis,
            s.nama,
            s.no_tlp,
            s.alamat,

            k.nama_kelas,
            k.komp_keahlian,

            spp.tahun,
            spp.nominal
        FROM tb_pembayaran p
        LEFT JOIN tb_siswa s 
            ON p.nisn = s.nisn
        LEFT JOIN tb_kelas k 
            ON s.id_kelas = k.id_kelas
        LEFT JOIN tb_spp spp 
            ON p.id_spp = spp.id_spp
        $where
        ORDER BY s.nama ASC, p.tgl_bayar DESC
    ");
}

/* =========================
   HALAMAN CETAK
========================= */
if ($cetak == 'all' || $cetak == 'siswa') {
    $queryCetak = queryDetailPembayaran($koneksi, $keyword);
    $judulCetak = ($cetak == 'all') ? 'Laporan Detail Pembayaran Seluruh Siswa' : 'Laporan Detail Pembayaran Per Siswa';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title><?= e($judulCetak); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-size: 13px;
            background: white;
        }

        .kop {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .kop h4,
        .kop h5,
        .kop p {
            margin: 0;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid mt-3">

    <div class="no-print mb-3">
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            Print
        </button>

        <a href="detail_pembayaran.php" class="btn btn-secondary btn-sm">
            Kembali
        </a>
    </div>

    <div class="kop">
        <h4>SISTEM INFORMASI PEMBAYARAN SPP</h4>
        <h5><?= e($judulCetak); ?></h5>
        <?php if ($keyword != '') { ?>
            <p>Keyword: <?= e($keyword); ?></p>
        <?php } ?>
        <p>Tanggal Cetak: <?= date('d-m-Y'); ?></p>
    </div>

    <table class="table table-bordered table-sm align-middle">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>NISN</th>
                <th>NIS</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Tgl Bayar</th>
                <th>Batas Bayar</th>
                <th>SPP</th>
                <th>Jumlah Bulan</th>
                <th>Total Bayar</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            <?php
            $no = 1;
            $grandTotal = 0;

            if (mysqli_num_rows($queryCetak) > 0) {
                while ($row = mysqli_fetch_assoc($queryCetak)) {
                    $grandTotal += (int)$row['jumlah_bayar'];
            ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= e($row['nisn']); ?></td>
                        <td><?= e($row['nis']); ?></td>
                        <td><?= e($row['nama']); ?></td>
                        <td><?= e($row['nama_kelas']); ?></td>
                        <td><?= e($row['tgl_bayar']); ?></td>
                        <td><?= e($row['batas_pembayaran']); ?></td>
                        <td><?= e($row['tahun']); ?> - <?= rupiah($row['nominal']); ?></td>
                        <td><?= e($row['jumlah_bulan']); ?></td>
                        <td><?= rupiah($row['jumlah_bayar']); ?></td>
                        <td><?= e($row['status']); ?></td>
                    </tr>
            <?php
                }
            } else {
            ?>
                <tr>
                    <td colspan="11" class="text-center">
                        Data pembayaran tidak ditemukan.
                    </td>
                </tr>
            <?php } ?>
        </tbody>

        <tfoot>
            <tr>
                <th colspan="9" class="text-end">Grand Total</th>
                <th colspan="2"><?= rupiah($grandTotal); ?></th>
            </tr>
        </tfoot>
    </table>

</div>

<script>
    window.print();
</script>

</body>
</html>
<?php
    exit;
}

include 'header.php';
include 'sidebar.php';

$queryDetail = queryDetailPembayaran($koneksi, $keyword);
?>

<div class="content">

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detail Pembayaran</h5>

            <a href="detail_pembayaran.php?cetak=all" target="_blank" class="btn btn-success btn-sm">
                Cetak Seluruh Siswa
            </a>
        </div>

        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-9">
                    <input type="text"
                           name="keyword"
                           class="form-control"
                           placeholder="Cari berdasarkan NISN, NIS, atau Nama Siswa"
                           value="<?= e($keyword); ?>">
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        Cari
                    </button>
                </div>
            </form>

            <?php if ($keyword != '') { ?>
                <div class="mt-3">
                    <a href="detail_pembayaran.php" class="btn btn-secondary btn-sm">
                        Reset
                    </a>

                    <a href="detail_pembayaran.php?cetak=siswa&keyword=<?= urlencode($keyword); ?>" 
                       target="_blank" 
                       class="btn btn-success btn-sm">
                        Cetak Hasil Pencarian / Per Siswa
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <?php if ($keyword != '') { ?>
                    Hasil Detail Pembayaran: <?= e($keyword); ?>
                <?php } else { ?>
                    Detail Pembayaran Seluruh Siswa
                <?php } ?>
            </h5>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>ID Pembayaran</th>
                        <th>NISN</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Keahlian</th>
                        <th>Tgl Bayar</th>
                        <th>Batas Pembayaran</th>
                        <th>SPP</th>
                        <th>Jumlah Bulan</th>
                        <th>Nominal Bayar</th>
                        <th>Jumlah Bayar</th>
                        <th>Kembalian</th>
                        <th>Status</th>
                        <th width="120">Cetak</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $no = 1;
                    $grandTotal = 0;

                    if (mysqli_num_rows($queryDetail) > 0) {
                        while ($row = mysqli_fetch_assoc($queryDetail)) {
                            $grandTotal += (int)$row['jumlah_bayar'];
                    ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= e($row['id_pembayaran']); ?></td>
                                <td><?= e($row['nisn']); ?></td>
                                <td><?= e($row['nis']); ?></td>
                                <td><?= e($row['nama']); ?></td>
                                <td><?= e($row['nama_kelas']); ?></td>
                                <td><?= e($row['komp_keahlian']); ?></td>
                                <td><?= e($row['tgl_bayar']); ?></td>
                                <td><?= e($row['batas_pembayaran']); ?></td>
                                <td><?= e($row['tahun']); ?> - <?= rupiah($row['nominal']); ?></td>
                                <td><?= e($row['jumlah_bulan']); ?></td>
                                <td><?= rupiah($row['nominal_bayar']); ?></td>
                                <td><?= rupiah($row['jumlah_bayar']); ?></td>
                                <td><?= rupiah($row['kembalian']); ?></td>
                                <td>
                                    <span class="badge <?= strtolower(trim($row['status'])) == 'sudah lunas' || strtolower(trim($row['status'])) == 'lunas' ? 'bg-success' : 'bg-danger'; ?>">
                                        <?= e($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="detail_pembayaran.php?cetak=siswa&keyword=<?= urlencode($row['nisn']); ?>" 
                                       target="_blank" 
                                       class="btn btn-success btn-sm">
                                        Cetak
                                    </a>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="16" class="text-center text-muted">
                                Data pembayaran tidak ditemukan.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="12" class="text-end">Grand Total</th>
                        <th colspan="4"><?= rupiah($grandTotal); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>