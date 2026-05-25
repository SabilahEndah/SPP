<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/cek_akses.php';

$title = "Data Pembayaran";

$keyword = $_GET['keyword'] ?? '';

/* ==========================
   PROSES HAPUS PEMBAYARAN
========================== */
if (isset($_GET['hapus'])) {
    $id_pembayaran = mysqli_real_escape_string($koneksi, $_GET['hapus']);

    try {
        $queryAmbil = mysqli_query($koneksi, "
            SELECT nisn 
            FROM tb_pembayaran 
            WHERE id_pembayaran = '$id_pembayaran'
            LIMIT 1
        ");

        $dataPembayaran = mysqli_fetch_assoc($queryAmbil);

        if ($dataPembayaran) {
            $nisn = mysqli_real_escape_string($koneksi, $dataPembayaran['nisn']);

            mysqli_query($koneksi, "
                DELETE FROM tb_pembayaran 
                WHERE id_pembayaran = '$id_pembayaran'
            ");

            $queryTotal = mysqli_query($koneksi, "
                SELECT 
                    COALESCE(SUM(CAST(jumlah_bulan AS UNSIGNED)), 0) AS total_bulan,
                    MAX(tgl_bayar) AS terakhir_bayar,
                    COALESCE(SUM(CAST(jumlah_bayar AS UNSIGNED)), 0) AS total_bayar,
                    COALESCE(SUM(CAST(nominal_bayar AS UNSIGNED)), 0) AS total_tagihan
                FROM tb_pembayaran
                WHERE nisn = '$nisn'
            ");

            $total = mysqli_fetch_assoc($queryTotal);

            if ($total['total_bulan'] > 0) {
                $status_akhir = ($total['total_bayar'] >= $total['total_tagihan']) ? "Sudah Lunas" : "Belum Lunas";

                mysqli_query($koneksi, "
                    UPDATE cek_pembayaran SET
                        tgl_terakhir_bayar = '{$total['terakhir_bayar']}',
                        tgl_sekarang = CURDATE(),
                        status_pembayaran = '$status_akhir',
                        jumlah_bulan = '{$total['total_bulan']}'
                    WHERE nisn = '$nisn'
                ");
            } else {
                mysqli_query($koneksi, "
                    DELETE FROM cek_pembayaran 
                    WHERE nisn = '$nisn'
                ");
            }

            echo "
                <script>
                    alert('Data pembayaran berhasil dihapus');
                    window.location='pembayaran.php';
                </script>
            ";
            exit;
        } else {
            echo "
                <script>
                    alert('Data pembayaran tidak ditemukan');
                    window.location='pembayaran.php';
                </script>
            ";
            exit;
        }

    } catch (Exception $e) {
        echo "
            <script>
                alert('Gagal menghapus pembayaran: " . addslashes($e->getMessage()) . "');
                window.location='pembayaran.php';
            </script>
        ";
        exit;
    }
}

/* ==========================
   SEARCH DATA PEMBAYARAN
========================== */
$where = "";

if ($keyword != '') {
    $keyword_safe = mysqli_real_escape_string($koneksi, $keyword);

    $where = "
        WHERE p.nisn LIKE '%$keyword_safe%'
           OR s.nis LIKE '%$keyword_safe%'
           OR s.nama LIKE '%$keyword_safe%'
           OR p.tgl_bayar LIKE '%$keyword_safe%'
           OR p.status LIKE '%$keyword_safe%'
    ";
}

include 'header.php';
include 'sidebar.php';
?>

<div class="content">

    <div class="card top-card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="mb-1">Data Pembayaran</h4>
            <p class="mb-0">
                Petugas dapat melihat, mencari, mencetak, dan menghapus data pembayaran yang salah input.
            </p>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-10">
                    <input type="text"
                           name="keyword"
                           class="form-control"
                           placeholder="Cari NISN, NIS, nama siswa, tanggal, atau status"
                           value="<?= e($keyword); ?>">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-blue w-100">Cari</button>
                </div>
            </form>

            <?php if ($keyword != '') { ?>
                <a href="pembayaran.php" class="btn btn-secondary btn-sm mt-3">
                    Reset
                </a>
            <?php } ?>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Daftar Pembayaran</h5>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>ID Pembayaran</th>
                        <th>NISN</th>
                        <th>NIS</th>
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
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $no = 1;
                    $totalBayar = 0;

                    $query = mysqli_query($koneksi, "
                        SELECT 
                            p.*,
                            s.nis,
                            s.nama,
                            k.nama_kelas
                        FROM tb_pembayaran p
                        LEFT JOIN tb_siswa s 
                            ON p.nisn = s.nisn
                        LEFT JOIN tb_kelas k 
                            ON s.id_kelas = k.id_kelas
                        $where
                        ORDER BY p.tgl_bayar DESC, p.id_pembayaran DESC
                    ");

                    if (mysqli_num_rows($query) > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            $totalBayar += (int) preg_replace('/[^0-9]/', '', $row['jumlah_bayar']);
                            $status = strtolower(trim($row['status'] ?? ''));
                    ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= e($row['id_pembayaran']); ?></td>
                                <td><?= e($row['nisn']); ?></td>
                                <td><?= e($row['nis']); ?></td>
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
                                <td>
                                    <a href="pembayaran.php?hapus=<?= e($row['id_pembayaran']); ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Yakin ingin menghapus data pembayaran ini?')">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="15" class="text-center text-muted">
                                Data pembayaran tidak ditemukan.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="10" class="text-end">Total Pembayaran</th>
                        <th colspan="5"><?= rupiah($totalBayar); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>