<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/cek_akses.php';

$title = "Pembayaran Per Bulan";

$keyword = $_GET['keyword'] ?? '';
$bulan = $_GET['bulan'] ?? '';
$tahun = $_GET['tahun'] ?? date('Y');

function namaBulan($bulan)
{
    $nama = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    ];

    return $nama[(int)$bulan] ?? '-';
}

/* ==========================
   FILTER PENCARIAN
========================== */
$where = "WHERE 1=1";

if ($keyword != '') {
    $keyword_safe = mysqli_real_escape_string($koneksi, $keyword);

    $where .= "
        AND (
            p.nisn LIKE '%$keyword_safe%'
            OR s.nis LIKE '%$keyword_safe%'
            OR s.nama LIKE '%$keyword_safe%'
            OR k.nama_kelas LIKE '%$keyword_safe%'
            OR p.tgl_bayar LIKE '%$keyword_safe%'
            OR p.status LIKE '%$keyword_safe%'
        )
    ";
}

if ($bulan != '') {
    $bulan_safe = (int) $bulan;
    $where .= " AND MONTH(p.batas_pembayaran) = '$bulan_safe'";
}

if ($tahun != '') {
    $tahun_safe = mysqli_real_escape_string($koneksi, $tahun);
    $where .= " AND YEAR(p.batas_pembayaran) = '$tahun_safe'";
}

include 'header.php';
include 'sidebar.php';
?>

<div class="content">

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Data Pembayaran Per Bulan</h5>
        </div>

        <div class="card-body">
            <form method="GET" class="row g-2">

                <div class="col-md-5">
                    <input type="text"
                           name="keyword"
                           class="form-control"
                           placeholder="Cari NISN, NIS, Nama Siswa, Kelas, Tanggal, atau Status"
                           value="<?= e($keyword); ?>">
                </div>

                <div class="col-md-3">
                    <select name="bulan" class="form-select">
                        <option value="">Semua Bulan</option>
                        <?php for ($i = 1; $i <= 12; $i++) { ?>
                            <option value="<?= $i; ?>" <?= $bulan == $i ? 'selected' : ''; ?>>
                                <?= namaBulan($i); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <input type="number"
                           name="tahun"
                           class="form-control"
                           value="<?= e($tahun); ?>"
                           placeholder="Tahun">
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        Cari
                    </button>
                </div>

            </form>

            <?php if ($keyword != '' || $bulan != '' || $tahun != date('Y')) { ?>
                <div class="mt-3">
                    <a href="pembayaran.php" class="btn btn-secondary btn-sm">
                        Reset Pencarian
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Rekap Pembayaran SPP Per Bulan</h5>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Bulan Tagihan</th>
                        <th>NISN</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Nominal SPP</th>
                        <th>Total Dibayar</th>
                        <th>Sisa Tagihan</th>
                        <th>Terakhir Bayar</th>
                        <th>Jumlah Transaksi</th>
                        <th>Status Bulan</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $no = 1;
                    $grandTotalBayar = 0;
                    $grandTotalTagihan = 0;
                    $grandTotalSisa = 0;

                    $query = mysqli_query($koneksi, "
                        SELECT 
                            p.nisn,
                            s.nis,
                            s.nama,
                            k.nama_kelas,
                            k.komp_keahlian,

                            YEAR(p.batas_pembayaran) AS tahun_tagihan,
                            MONTH(p.batas_pembayaran) AS bulan_tagihan,

                            MAX(p.tgl_bayar) AS terakhir_bayar,
                            COUNT(p.id_pembayaran) AS jumlah_transaksi,

                            COALESCE(
                                SUM(
                                    CAST(
                                        REPLACE(
                                            REPLACE(
                                                REPLACE(p.jumlah_bayar, 'Rp', ''),
                                            '.', ''),
                                        ',', '') 
                                    AS UNSIGNED)
                                ), 
                            0) AS total_bayar_bulan,

                            COALESCE(
                                MAX(
                                    CAST(
                                        REPLACE(
                                            REPLACE(
                                                REPLACE(spp.nominal, 'Rp', ''),
                                            '.', ''),
                                        ',', '') 
                                    AS UNSIGNED)
                                ), 
                            0) AS nominal_spp

                        FROM tb_pembayaran p
                        LEFT JOIN tb_siswa s 
                            ON p.nisn = s.nisn
                        LEFT JOIN tb_kelas k 
                            ON s.id_kelas = k.id_kelas
                        LEFT JOIN tb_spp spp 
                            ON p.id_spp = spp.id_spp

                        $where

                        GROUP BY 
                            p.nisn,
                            s.nis,
                            s.nama,
                            k.nama_kelas,
                            k.komp_keahlian,
                            YEAR(p.batas_pembayaran),
                            MONTH(p.batas_pembayaran)

                        ORDER BY 
                            YEAR(p.batas_pembayaran) DESC,
                            MONTH(p.batas_pembayaran) DESC,
                            s.nama ASC
                    ");

                    if (mysqli_num_rows($query) > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            $nominalSpp = (int) $row['nominal_spp'];
                            $totalBayarBulan = (int) $row['total_bayar_bulan'];

                            $sisaTagihan = $nominalSpp - $totalBayarBulan;

                            if ($sisaTagihan < 0) {
                                $sisaTagihan = 0;
                            }

                            if ($totalBayarBulan >= $nominalSpp && $nominalSpp > 0) {
                                $status = "Sudah Lunas";
                                $badge = "bg-success";
                            } elseif ($totalBayarBulan > 0 && $totalBayarBulan < $nominalSpp) {
                                $status = "Kurang Bayar";
                                $badge = "bg-warning text-dark";
                            } else {
                                $status = "Belum Lunas";
                                $badge = "bg-danger";
                            }

                            $grandTotalBayar += $totalBayarBulan;
                            $grandTotalTagihan += $nominalSpp;
                            $grandTotalSisa += $sisaTagihan;
                    ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <?= e(namaBulan($row['bulan_tagihan'])); ?>
                                    <?= e($row['tahun_tagihan']); ?>
                                </td>
                                <td><?= e($row['nisn']); ?></td>
                                <td><?= e($row['nis']); ?></td>
                                <td><?= e($row['nama']); ?></td>
                                <td>
                                    <?= e($row['nama_kelas']); ?>
                                    <?php if (!empty($row['komp_keahlian'])) { ?>
                                        <br>
                                        <small class="text-muted"><?= e($row['komp_keahlian']); ?></small>
                                    <?php } ?>
                                </td>
                                <td><?= rupiah($nominalSpp); ?></td>
                                <td><?= rupiah($totalBayarBulan); ?></td>
                                <td><?= rupiah($sisaTagihan); ?></td>
                                <td><?= e($row['terakhir_bayar'] ?? '-'); ?></td>
                                <td><?= e($row['jumlah_transaksi']); ?> Transaksi</td>
                                <td>
                                    <span class="badge <?= $badge; ?>">
                                        <?= e($status); ?>
                                    </span>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="12" class="text-center text-muted">
                                Data pembayaran tidak ditemukan.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="6" class="text-end">Total</th>
                        <th><?= rupiah($grandTotalTagihan); ?></th>
                        <th><?= rupiah($grandTotalBayar); ?></th>
                        <th><?= rupiah($grandTotalSisa); ?></th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>