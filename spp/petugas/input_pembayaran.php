<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/cek_akses.php';

$title = "Input Pembayaran";

/* ==========================
   PROSES SIMPAN PEMBAYARAN
========================== */
if (isset($_POST['simpan'])) {
    $nisn = mysqli_real_escape_string($koneksi, $_POST['nisn']);
    $tgl_bayar = mysqli_real_escape_string($koneksi, $_POST['tgl_bayar']);
    $batas_pembayaran = mysqli_real_escape_string($koneksi, $_POST['batas_pembayaran']);
    $jumlah_bulan = (int) $_POST['jumlah_bulan'];
    $jumlah_bayar = (int) $_POST['jumlah_bayar'];

    try {
        if ($jumlah_bulan <= 0) {
            throw new Exception("Jumlah bulan harus lebih dari 0");
        }

        if ($jumlah_bayar <= 0) {
            throw new Exception("Jumlah bayar harus lebih dari 0");
        }

        /* ==========================
           AMBIL DATA SISWA DAN SPP
        ========================== */
        $querySiswa = mysqli_query($koneksi, "
            SELECT 
                s.nisn,
                s.nis,
                s.nama,
                s.no_tlp,
                s.id_spp,
                spp.nominal
            FROM tb_siswa s
            LEFT JOIN tb_spp spp 
                ON s.id_spp = spp.id_spp
            WHERE s.nisn = '$nisn'
            LIMIT 1
        ");

        $siswa = mysqli_fetch_assoc($querySiswa);

        if (!$siswa) {
            throw new Exception("Data siswa tidak ditemukan");
        }

        $id_spp = mysqli_real_escape_string($koneksi, $siswa['id_spp']);
        $nama_siswa = mysqli_real_escape_string($koneksi, $siswa['nama']);
        $no_tlp = mysqli_real_escape_string($koneksi, $siswa['no_tlp']);

        $nominal_spp = preg_replace('/[^0-9]/', '', $siswa['nominal']);
        $nominal_spp = (int) $nominal_spp;

        if ($nominal_spp <= 0) {
            throw new Exception("Nominal SPP siswa belum tersedia");
        }

        /*
            Status awal transaksi.
            Nanti status ini akan diperbarui lagi berdasarkan total pembayaran
            pada bulan tagihan yang sama.
        */
        $nominal_bayar = $nominal_spp * $jumlah_bulan;
        $kembalian = $jumlah_bayar - $nominal_bayar;

        if ($kembalian < 0) {
            $kembalian = 0;
        }

        $status = ($jumlah_bayar >= $nominal_bayar) ? "Sudah Lunas" : "Belum Lunas";

        /* ==========================
           MEMBUAT ID PEMBAYARAN OTOMATIS
        ========================== */
        $queryId = mysqli_query($koneksi, "
            SELECT COALESCE(MAX(CAST(id_pembayaran AS UNSIGNED)), 0) + 1 AS id_baru
            FROM tb_pembayaran
        ");

        $dataId = mysqli_fetch_assoc($queryId);
        $id_pembayaran = $dataId['id_baru'];

        /* ==========================
           INSERT PEMBAYARAN BARU
        ========================== */
        mysqli_query($koneksi, "
            INSERT INTO tb_pembayaran
            (
                id_pembayaran,
                status,
                nisn,
                tgl_bayar,
                tgl_terakhir_bayar,
                batas_pembayaran,
                jumlah_bulan,
                id_spp,
                nominal_bayar,
                jumlah_bayar,
                kembalian
            )
            VALUES
            (
                '$id_pembayaran',
                '$status',
                '$nisn',
                '$tgl_bayar',
                '$tgl_bayar',
                '$batas_pembayaran',
                '$jumlah_bulan',
                '$id_spp',
                '$nominal_bayar',
                '$jumlah_bayar',
                '$kembalian'
            )
        ");

        /* ==========================
           HITUNG STATUS PER BULAN TAGIHAN
           Contoh:
           Ghea bayar bulan Mei:
           50.000 + 50.000 = 100.000
           maka semua transaksi Mei menjadi Sudah Lunas
        ========================== */
        $queryStatusBulan = mysqli_query($koneksi, "
            SELECT 
                COALESCE(
                    SUM(
                        CAST(
                            REPLACE(
                                REPLACE(
                                    REPLACE(jumlah_bayar, 'Rp', ''),
                                '.', ''),
                            ',', '') 
                        AS UNSIGNED)
                    ), 
                0) AS total_bayar_bulan
            FROM tb_pembayaran
            WHERE nisn = '$nisn'
              AND YEAR(batas_pembayaran) = YEAR('$batas_pembayaran')
              AND MONTH(batas_pembayaran) = MONTH('$batas_pembayaran')
        ");

        $dataStatusBulan = mysqli_fetch_assoc($queryStatusBulan);
        $total_bayar_bulan = (int) $dataStatusBulan['total_bayar_bulan'];

        if ($total_bayar_bulan >= $nominal_spp) {
            $status_bulan = "Sudah Lunas";
        } else {
            $status_bulan = "Belum Lunas";
        }

        mysqli_query($koneksi, "
            UPDATE tb_pembayaran SET
                status = '$status_bulan'
            WHERE nisn = '$nisn'
              AND YEAR(batas_pembayaran) = YEAR('$batas_pembayaran')
              AND MONTH(batas_pembayaran) = MONTH('$batas_pembayaran')
        ");

        /* ==========================
           HITUNG TOTAL UNTUK CEK PEMBAYARAN
           Total tagihan dihitung berdasarkan jumlah bulan berbeda yang sudah ada transaksi.
           Jadi kalau bulan Mei ada 3 transaksi, tagihan Mei tetap dihitung 1x nominal SPP.
        ========================== */
        $queryTotal = mysqli_query($koneksi, "
            SELECT 
                COUNT(DISTINCT DATE_FORMAT(batas_pembayaran, '%Y-%m')) AS total_bulan_tagihan,
                MAX(tgl_bayar) AS terakhir_bayar,
                COALESCE(
                    SUM(
                        CAST(
                            REPLACE(
                                REPLACE(
                                    REPLACE(jumlah_bayar, 'Rp', ''),
                                '.', ''),
                            ',', '') 
                        AS UNSIGNED)
                    ), 
                0) AS total_bayar
            FROM tb_pembayaran
            WHERE nisn = '$nisn'
        ");

        $total = mysqli_fetch_assoc($queryTotal);

        $total_bulan = (int) $total['total_bulan_tagihan'];
        $terakhir_bayar = $total['terakhir_bayar'];
        $total_bayar = (int) $total['total_bayar'];
        $total_tagihan = $total_bulan * $nominal_spp;

        if ($total_bayar >= $total_tagihan && $total_bulan > 0) {
            $status_akhir = "Sudah Lunas";
        } else {
            $status_akhir = "Belum Lunas";
        }

        /* ==========================
           UPDATE / INSERT CEK PEMBAYARAN
        ========================== */
        $queryCekStatus = mysqli_query($koneksi, "
            SELECT nisn 
            FROM cek_pembayaran
            WHERE nisn = '$nisn'
            LIMIT 1
        ");

        if (mysqli_num_rows($queryCekStatus) > 0) {
            mysqli_query($koneksi, "
                UPDATE cek_pembayaran SET
                    tgl_terakhir_bayar = '$terakhir_bayar',
                    tgl_sekarang = CURDATE(),
                    status_pembayaran = '$status_akhir',
                    jumlah_bulan = '$total_bulan',
                    nama = '$nama_siswa',
                    no_tlp = '$no_tlp'
                WHERE nisn = '$nisn'
            ");
        } else {
            mysqli_query($koneksi, "
                INSERT INTO cek_pembayaran
                (
                    nisn,
                    tgl_terakhir_bayar,
                    tgl_sekarang,
                    status_pembayaran,
                    jumlah_bulan,
                    nama,
                    no_tlp
                )
                VALUES
                (
                    '$nisn',
                    '$terakhir_bayar',
                    CURDATE(),
                    '$status_akhir',
                    '$total_bulan',
                    '$nama_siswa',
                    '$no_tlp'
                )
            ");
        }

        echo "
            <script>
                alert('Pembayaran berhasil disimpan');
                window.location='cetak_pembayaran.php?id=$id_pembayaran';
            </script>
        ";
        exit;

    } catch (Exception $e) {
        echo "
            <script>
                alert('Gagal menyimpan pembayaran: " . addslashes($e->getMessage()) . "');
                window.location='input_pembayaran.php';
            </script>
        ";
        exit;
    }
}

include 'header.php';
include 'sidebar.php';
?>

<div class="content">

    <div class="card top-card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="mb-1">Input Pembayaran</h4>
            <p class="mb-0">Halaman untuk menambahkan transaksi pembayaran SPP siswa.</p>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Form Input Pembayaran</h5>
        </div>

        <div class="card-body">
            <form method="POST">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pilih Siswa</label>
                        <select name="nisn" class="form-select" required>
                            <option value="">-- Pilih Siswa --</option>

                            <?php
                            $qSiswa = mysqli_query($koneksi, "
                                SELECT 
                                    s.nisn,
                                    s.nis,
                                    s.nama,
                                    k.nama_kelas,
                                    spp.tahun,
                                    spp.nominal
                                FROM tb_siswa s
                                LEFT JOIN tb_kelas k 
                                    ON s.id_kelas = k.id_kelas
                                LEFT JOIN tb_spp spp 
                                    ON s.id_spp = spp.id_spp
                                ORDER BY s.nama ASC
                            ");

                            while ($siswa = mysqli_fetch_assoc($qSiswa)) {
                            ?>
                                <option value="<?= e($siswa['nisn']); ?>">
                                    <?= e($siswa['nisn']); ?> -
                                    <?= e($siswa['nama']); ?> -
                                    <?= e($siswa['nama_kelas']); ?> -
                                    <?= e($siswa['tahun']); ?> -
                                    <?= rupiah($siswa['nominal']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Bayar</label>
                        <input type="date"
                               name="tgl_bayar"
                               class="form-control"
                               value="<?= date('Y-m-d'); ?>"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Batas Pembayaran</label>
                        <input type="date"
                               name="batas_pembayaran"
                               class="form-control"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jumlah Bulan</label>
                        <input type="number"
                               name="jumlah_bulan"
                               class="form-control"
                               placeholder="Contoh: 1"
                               min="1"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jumlah Bayar</label>
                        <input type="number"
                               name="jumlah_bayar"
                               class="form-control"
                               placeholder="Contoh: 300000"
                               min="1"
                               required>
                    </div>

                </div>

                <div class="alert alert-info small">
                    Status dihitung berdasarkan total pembayaran pada bulan tagihan yang sama.
                    Contoh: jika nominal SPP Rp100.000 dan siswa membayar Rp50.000 dua kali untuk bulan yang sama, maka status menjadi <b>Sudah Lunas</b>.
                </div>

                <button type="submit" name="simpan" class="btn btn-blue">
                    Simpan Pembayaran
                </button>

            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Riwayat Pembayaran Terbaru</h5>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>ID Pembayaran</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Tanggal Bayar</th>
                        <th>Batas Pembayaran</th>
                        <th>Jumlah Bulan</th>
                        <th>Nominal Bayar</th>
                        <th>Jumlah Bayar</th>
                        <th>Kembalian</th>
                        <th>Status</th>
                        <th>Cetak</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $no = 1;

                    $queryRiwayat = mysqli_query($koneksi, "
                        SELECT 
                            p.*,
                            s.nama,
                            k.nama_kelas
                        FROM tb_pembayaran p
                        LEFT JOIN tb_siswa s 
                            ON p.nisn = s.nisn
                        LEFT JOIN tb_kelas k 
                            ON s.id_kelas = k.id_kelas
                        ORDER BY p.tgl_bayar DESC, p.id_pembayaran DESC
                        LIMIT 10
                    ");

                    if (mysqli_num_rows($queryRiwayat) > 0) {
                        while ($row = mysqli_fetch_assoc($queryRiwayat)) {
                            $status = strtolower(trim($row['status'] ?? ''));
                    ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= e($row['id_pembayaran']); ?></td>
                                <td><?= e($row['nisn']); ?></td>
                                <td><?= e($row['nama']); ?></td>
                                <td><?= e($row['nama_kelas']); ?></td>
                                <td><?= e($row['tgl_bayar']); ?></td>
                                <td><?= e($row['batas_pembayaran']); ?></td>
                                <td><?= e($row['jumlah_bulan']); ?> Bulan</td>
                                <td><?= rupiah($row['nominal_bayar']); ?></td>
                                <td><?= rupiah($row['jumlah_bayar']); ?></td>
                                <td><?= rupiah($row['kembalian']); ?></td>
                                <td>
                                    <span class="badge <?= ($status == 'sudah lunas' || $status == 'lunas') ? 'bg-success' : 'bg-danger'; ?>">
                                        <?= e($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="cetak_pembayaran.php?id=<?= e($row['id_pembayaran']); ?>" 
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
                            <td colspan="13" class="text-center text-muted">
                                Belum ada data pembayaran.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>