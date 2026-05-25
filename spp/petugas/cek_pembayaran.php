<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/cek_akses.php';

$title = "Cek Pembayaran";

include 'header.php';
include 'sidebar.php';
?>

<div class="content">

    <div class="card top-card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="mb-1">Cek Pembayaran</h4>
            <p class="mb-0">Cari pembayaran berdasarkan NISN, NIS, atau nama siswa.</p>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-10">
                    <input type="text"
                           name="keyword"
                           class="form-control"
                           placeholder="Masukkan NISN, NIS, atau Nama Siswa"
                           value="<?= e($_GET['keyword'] ?? ''); ?>">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-blue w-100">Cek</button>
                </div>
            </form>
        </div>
    </div>

    <?php
    if (isset($_GET['keyword']) && $_GET['keyword'] != '') {
        $keyword = mysqli_real_escape_string($koneksi, $_GET['keyword']);

        $queryCek = mysqli_query($koneksi, "
            SELECT 
                s.nisn,
                s.nis,
                s.nama AS nama_siswa,
                s.no_tlp,
                k.nama_kelas,
                k.komp_keahlian,

                MAX(p.tgl_bayar) AS tgl_terakhir_bayar,
                COALESCE(SUM(CAST(p.jumlah_bulan AS UNSIGNED)), 0) AS total_bulan,
                COALESCE(SUM(CAST(p.nominal_bayar AS UNSIGNED)), 0) AS total_tagihan,
                COALESCE(SUM(CAST(p.jumlah_bayar AS UNSIGNED)), 0) AS total_bayar

            FROM tb_siswa s
            LEFT JOIN tb_kelas k 
                ON s.id_kelas = k.id_kelas
            LEFT JOIN tb_pembayaran p 
                ON s.nisn = p.nisn

            WHERE s.nisn LIKE '%$keyword%'
               OR s.nis LIKE '%$keyword%'
               OR s.nama LIKE '%$keyword%'

            GROUP BY 
                s.nisn,
                s.nis,
                s.nama,
                s.no_tlp,
                k.nama_kelas,
                k.komp_keahlian

            ORDER BY s.nama ASC
        ");
    ?>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Hasil Cek Pembayaran</h5>
            </div>

            <div class="card-body table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>No</th>
                            <th>NISN</th>
                            <th>NIS</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>No Telepon</th>
                            <th>Tgl Terakhir Bayar</th>
                            <th>Jumlah Bulan</th>
                            <th>Total Tagihan</th>
                            <th>Total Bayar</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $no = 1;

                        if (mysqli_num_rows($queryCek) > 0) {
                            while ($row = mysqli_fetch_assoc($queryCek)) {
                                $total_bulan = (int) $row['total_bulan'];
                                $total_tagihan = (int) $row['total_tagihan'];
                                $total_bayar = (int) $row['total_bayar'];

                                if ($total_bulan == 0) {
                                    $status = "Belum Ada Data";
                                    $badge = "bg-secondary";
                                } elseif ($total_bayar >= $total_tagihan) {
                                    $status = "Sudah Lunas";
                                    $badge = "bg-success";
                                } else {
                                    $status = "Belum Lunas";
                                    $badge = "bg-danger";
                                }
                        ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= e($row['nisn']); ?></td>
                                    <td><?= e($row['nis']); ?></td>
                                    <td><?= e($row['nama_siswa']); ?></td>
                                    <td><?= e($row['nama_kelas']); ?></td>
                                    <td><?= e($row['no_tlp']); ?></td>
                                    <td><?= e($row['tgl_terakhir_bayar'] ?? '-'); ?></td>
                                    <td><?= e($total_bulan); ?> Bulan</td>
                                    <td><?= rupiah($total_tagihan); ?></td>
                                    <td><?= rupiah($total_bayar); ?></td>
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
                                <td colspan="11" class="text-center text-muted">
                                    Data siswa tidak ditemukan.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php } ?>

</div>

<?php include 'footer.php'; ?>