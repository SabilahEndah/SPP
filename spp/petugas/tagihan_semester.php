<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/cek_akses.php';

$title = "Tagihan Semester";

$keyword = $_GET['keyword'] ?? '';
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

include 'header.php';
include 'sidebar.php';
?>

<div class="content">

    <div class="card top-card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="mb-1">Tagihan Semester</h4>
            <p class="mb-0">
                Petugas dapat melihat tagihan SPP siswa selama 1 semester atau 6 bulan.
            </p>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <input type="text"
                           name="keyword"
                           class="form-control"
                           placeholder="Masukkan NISN, NIS, atau Nama Siswa"
                           value="<?= e($keyword); ?>"
                           required>
                </div>

                <div class="col-md-3">
                    <select name="semester" class="form-select">
                        <option value="genap" <?= $semester == 'genap' ? 'selected' : ''; ?>>
                            Semester Genap - Januari s/d Juni
                        </option>

                        <option value="ganjil" <?= $semester == 'ganjil' ? 'selected' : ''; ?>>
                            Semester Ganjil - Juli s/d Desember
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <input type="number"
                           name="tahun"
                           class="form-control"
                           value="<?= e($tahun); ?>"
                           required>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-blue w-100">
                        Tampilkan
                    </button>
                </div>
            </form>

            <?php if ($keyword != '') { ?>
                <a href="tagihan_semester.php" class="btn btn-secondary btn-sm mt-3">
                    Reset
                </a>
            <?php } ?>
        </div>
    </div>

    <?php
    if ($keyword != '') {
        $keywordSafe = mysqli_real_escape_string($koneksi, $keyword);

        $querySiswa = mysqli_query($koneksi, "
            SELECT 
                s.nisn,
                s.nis,
                s.nama,
                s.no_tlp,
                s.alamat,
                s.id_spp,

                k.nama_kelas,
                k.komp_keahlian,

                spp.tahun,
                spp.nominal

            FROM tb_siswa s
            LEFT JOIN tb_kelas k 
                ON s.id_kelas = k.id_kelas
            LEFT JOIN tb_spp spp 
                ON s.id_spp = spp.id_spp

            WHERE s.nisn LIKE '%$keywordSafe%'
               OR s.nis LIKE '%$keywordSafe%'
               OR s.nama LIKE '%$keywordSafe%'

            ORDER BY s.nama ASC
            LIMIT 1
        ");

        $siswa = mysqli_fetch_assoc($querySiswa);

        if ($siswa) {
            $nisn = mysqli_real_escape_string($koneksi, $siswa['nisn']);

            $nominalSpp = preg_replace('/[^0-9]/', '', $siswa['nominal']);
            $nominalSpp = (int) $nominalSpp;

            $dataBayar = [];

            $queryBayar = mysqli_query($koneksi, "
                SELECT 
                    MONTH(batas_pembayaran) AS bulan,

                    SUM(
                        CAST(
                            REPLACE(
                                REPLACE(
                                    REPLACE(jumlah_bayar, 'Rp', ''),
                                '.', ''),
                            ',', '') 
                        AS UNSIGNED)
                    ) AS total_bayar,

                    MAX(tgl_bayar) AS terakhir_bayar

                FROM tb_pembayaran

                WHERE nisn = '$nisn'
                  AND YEAR(batas_pembayaran) = '$tahun'

                GROUP BY MONTH(batas_pembayaran)
            ");

            while ($row = mysqli_fetch_assoc($queryBayar)) {
                $bulan = (int) $row['bulan'];

                $dataBayar[$bulan] = [
                    'total_bayar' => (int) $row['total_bayar'],
                    'terakhir_bayar' => $row['terakhir_bayar']
                ];
            }

            $totalTagihanSemester = 0;
            $totalDibayarSemester = 0;
            $totalSisaSemester = 0;
    ?>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
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

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        Tagihan 
                        <?= $semester == 'ganjil' ? 'Semester Ganjil' : 'Semester Genap'; ?>
                        Tahun <?= e($tahun); ?>
                    </h5>

                    <button onclick="window.print()" class="btn btn-light btn-sm">
                        Cetak
                    </button>
                </div>

                <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-primary">
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

    <?php
        } else {
    ?>

            <div class="alert alert-warning">
                Data siswa tidak ditemukan berdasarkan kata kunci:
                <b><?= e($keyword); ?></b>
            </div>

    <?php
        }
    }
    ?>

</div>

<?php include 'footer.php'; ?>