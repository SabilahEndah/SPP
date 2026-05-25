<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/cek_akses.php';

$title = "Riwayat Pembayaran";

include 'header.php';
include 'sidebar.php';
?>

<div class="content">

    <div class="card top-card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="mb-1">Riwayat Pembayaran</h4>
            <p class="mb-0">Menampilkan seluruh pembayaran SPP milik siswa.</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Riwayat Pembayaran</h5>
            <button onclick="window.print()" class="btn btn-light btn-sm no-print">
                Cetak Semua
            </button>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-success">
                    <tr>
                        <th>No</th>
                        <th>ID Pembayaran</th>
                        <th>Tanggal Bayar</th>
                        <th>Batas Pembayaran</th>
                        <th>Jumlah Bulan</th>
                        <th>Nominal Bayar</th>
                        <th>Jumlah Bayar</th>
                        <th>Kembalian</th>
                        <th>Status</th>
                        <th class="no-print">Cetak</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $no = 1;
                    $totalBayar = 0;

                    $query = mysqli_query($koneksi, "
                        SELECT *
                        FROM tb_pembayaran
                        WHERE nisn = '$nisn_login'
                        ORDER BY tgl_bayar DESC, id_pembayaran DESC
                    ");

                    if (mysqli_num_rows($query) > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                            $status = strtolower(trim($row['status'] ?? ''));
                            $totalBayar += (int) preg_replace('/[^0-9]/', '', $row['jumlah_bayar']);
                    ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= e($row['id_pembayaran']); ?></td>
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
                                <td class="no-print">
                                    <a href="cetak_pembayaran.php?id=<?= e($row['id_pembayaran']); ?>" target="_blank" class="btn btn-success btn-sm">
                                        Cetak
                                    </a>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted">
                                Belum ada riwayat pembayaran.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="6" class="text-end">Total Bayar</th>
                        <th colspan="4"><?= rupiah($totalBayar); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>