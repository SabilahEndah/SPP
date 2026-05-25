<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/cek_akses.php';

$title = "Cek Pembayaran";

include 'header.php';
include 'sidebar.php';
?>

<div class="content">

    <!-- FORM CEK PEMBAYARAN -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Cek Pembayaran Siswa</h5>
        </div>

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
                    <button type="submit" class="btn btn-primary w-100">
                        Cek
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- HASIL CEK BERDASARKAN NISN / NIS / NAMA -->
    <?php
    if (isset($_GET['keyword']) && $_GET['keyword'] != '') {
        $keyword = mysqli_real_escape_string($koneksi, $_GET['keyword']);

        $queryCek = mysqli_query($koneksi, "
            SELECT 
                s.nisn,
                s.nis,
                s.nama AS nama_siswa,
                s.no_tlp AS no_tlp_siswa,
                k.nama_kelas,
                k.komp_keahlian,
                c.tgl_terakhir_bayar,
                c.tgl_sekarang,
                c.status_pembayaran,
                c.jumlah_bulan
            FROM tb_siswa s
            LEFT JOIN tb_kelas k 
                ON s.id_kelas = k.id_kelas
            LEFT JOIN cek_pembayaran c 
                ON s.nisn = c.nisn
            WHERE s.nisn LIKE '%$keyword%'
               OR s.nis LIKE '%$keyword%'
               OR s.nama LIKE '%$keyword%'
            ORDER BY s.nama ASC
        ");
    ?>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Hasil Cek Pembayaran</h5>
            </div>

            <div class="card-body table-responsive">
                <?php if (mysqli_num_rows($queryCek) > 0) { ?>

                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>NISN</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Keahlian</th>
                                <th>No Telepon</th>
                                <th>Tgl Terakhir Bayar</th>
                                <th>Tgl Sekarang</th>
                                <th>Jumlah Bulan</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $no = 1;
                            while ($data = mysqli_fetch_assoc($queryCek)) {
                                $status = strtolower(trim($data['status_pembayaran'] ?? ''));
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= e($data['nisn']); ?></td>
                                    <td><?= e($data['nis']); ?></td>
                                    <td><?= e($data['nama_siswa']); ?></td>
                                    <td><?= e($data['nama_kelas']); ?></td>
                                    <td><?= e($data['komp_keahlian']); ?></td>
                                    <td><?= e($data['no_tlp_siswa']); ?></td>
                                    <td><?= e($data['tgl_terakhir_bayar'] ?? '-'); ?></td>
                                    <td><?= e($data['tgl_sekarang'] ?? '-'); ?></td>
                                    <td><?= e($data['jumlah_bulan'] ?? '0'); ?> Bulan</td>
                                    <td>
                                        <?php if ($status == 'sudah lunas' || $status == 'lunas') { ?>
                                            <span class="badge bg-success">
                                                <?= e($data['status_pembayaran']); ?>
                                            </span>
                                        <?php } elseif ($status != '') { ?>
                                            <span class="badge bg-danger">
                                                <?= e($data['status_pembayaran']); ?>
                                            </span>
                                        <?php } else { ?>
                                            <span class="badge bg-secondary">
                                                Belum Ada Data
                                            </span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                <?php } else { ?>

                    <div class="alert alert-warning mb-0">
                        Data siswa tidak ditemukan berdasarkan kata kunci:
                        <b><?= e($keyword); ?></b>
                    </div>

                <?php } ?>
            </div>
        </div>

    <?php } ?>

    <?php
    /* ==========================
       QUERY SISWA SUDAH LUNAS
    ========================== */
    $querySudahLunas = mysqli_query($koneksi, "
        SELECT 
            s.nisn,
            s.nis,
            s.nama,
            s.no_tlp,
            k.nama_kelas,
            k.komp_keahlian,
            c.tgl_terakhir_bayar,
            c.tgl_sekarang,
            c.status_pembayaran,
            c.jumlah_bulan
        FROM tb_siswa s
        LEFT JOIN tb_kelas k 
            ON s.id_kelas = k.id_kelas
        LEFT JOIN cek_pembayaran c 
            ON s.nisn = c.nisn
        WHERE LOWER(TRIM(c.status_pembayaran)) IN ('sudah lunas', 'lunas')
        ORDER BY s.nama ASC
    ");

    /* ==========================
       QUERY SISWA BELUM LUNAS
    ========================== */
    $queryBelumLunas = mysqli_query($koneksi, "
        SELECT 
            s.nisn,
            s.nis,
            s.nama,
            s.no_tlp,
            k.nama_kelas,
            k.komp_keahlian,
            c.tgl_terakhir_bayar,
            c.tgl_sekarang,
            c.status_pembayaran,
            c.jumlah_bulan
        FROM tb_siswa s
        LEFT JOIN tb_kelas k 
            ON s.id_kelas = k.id_kelas
        LEFT JOIN cek_pembayaran c 
            ON s.nisn = c.nisn
        WHERE c.status_pembayaran IS NULL
           OR LOWER(TRIM(c.status_pembayaran)) NOT IN ('sudah lunas', 'lunas')
        ORDER BY s.nama ASC
    ");

    $jumlahSudahLunas = mysqli_num_rows($querySudahLunas);
    $jumlahBelumLunas = mysqli_num_rows($queryBelumLunas);
    ?>

    <!-- CARD JUMLAH DATA -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 bg-success text-white">
                <div class="card-body">
                    <h6 class="mb-1">Siswa Sudah Lunas</h6>
                    <h3 class="mb-0"><?= $jumlahSudahLunas; ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 bg-danger text-white">
                <div class="card-body">
                    <h6 class="mb-1">Siswa Belum Lunas</h6>
                    <h3 class="mb-0"><?= $jumlahBelumLunas; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- TABEL SISWA SUDAH LUNAS -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Tabel Siswa Sudah Lunas</h5>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-success">
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Keahlian</th>
                        <th>No Telepon</th>
                        <th>Tgl Terakhir Bayar</th>
                        <th>Tgl Sekarang</th>
                        <th>Jumlah Bulan</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $no = 1;

                    if ($jumlahSudahLunas > 0) {
                        while ($row = mysqli_fetch_assoc($querySudahLunas)) {
                    ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= e($row['nisn']); ?></td>
                                <td><?= e($row['nis']); ?></td>
                                <td><?= e($row['nama']); ?></td>
                                <td><?= e($row['nama_kelas']); ?></td>
                                <td><?= e($row['komp_keahlian']); ?></td>
                                <td><?= e($row['no_tlp']); ?></td>
                                <td><?= e($row['tgl_terakhir_bayar'] ?? '-'); ?></td>
                                <td><?= e($row['tgl_sekarang'] ?? '-'); ?></td>
                                <td><?= e($row['jumlah_bulan'] ?? '0'); ?> Bulan</td>
                                <td>
                                    <span class="badge bg-success">
                                        <?= e($row['status_pembayaran']); ?>
                                    </span>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted">
                                Belum ada siswa yang sudah lunas.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- TABEL SISWA BELUM LUNAS -->
    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">Tabel Siswa Belum Lunas</h5>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-danger">
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Keahlian</th>
                        <th>No Telepon</th>
                        <th>Tgl Terakhir Bayar</th>
                        <th>Tgl Sekarang</th>
                        <th>Jumlah Bulan</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $no = 1;

                    if ($jumlahBelumLunas > 0) {
                        while ($row = mysqli_fetch_assoc($queryBelumLunas)) {
                    ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= e($row['nisn']); ?></td>
                                <td><?= e($row['nis']); ?></td>
                                <td><?= e($row['nama']); ?></td>
                                <td><?= e($row['nama_kelas']); ?></td>
                                <td><?= e($row['komp_keahlian']); ?></td>
                                <td><?= e($row['no_tlp']); ?></td>
                                <td><?= e($row['tgl_terakhir_bayar'] ?? '-'); ?></td>
                                <td><?= e($row['tgl_sekarang'] ?? '-'); ?></td>
                                <td><?= e($row['jumlah_bulan'] ?? '0'); ?> Bulan</td>
                                <td>
                                    <?php if (!empty($row['status_pembayaran'])) { ?>
                                        <span class="badge bg-danger">
                                            <?= e($row['status_pembayaran']); ?>
                                        </span>
                                    <?php } else { ?>
                                        <span class="badge bg-secondary">
                                            Belum Ada Data
                                        </span>
                                    <?php } ?>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted">
                                Tidak ada siswa yang belum lunas.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>