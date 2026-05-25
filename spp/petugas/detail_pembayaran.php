<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/cek_akses.php';

$title = "Detail Pembayaran";

include 'header.php';
include 'sidebar.php';
?>

<div class="content">

    <div class="card top-card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="mb-1">Detail Pembayaran</h4>
            <p class="mb-0">Detail pembayaran siswa yang dapat dilihat oleh petugas.</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Detail Pembayaran Siswa</h5>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Tanggal Bayar</th>
                        <th>Batas Pembayaran</th>
                        <th>Jumlah Bulan</th>
                        <th>Jumlah Bayar</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $no = 1;

                    $query = mysqli_query($koneksi, "
                        SELECT 
                            p.*,
                            s.nis,
                            s.nama,
                            k.nama_kelas
                        FROM tb_pembayaran p
                        LEFT JOIN tb_siswa s ON p.nisn = s.nisn
                        LEFT JOIN tb_kelas k ON s.id_kelas = k.id_kelas
                        ORDER BY p.tgl_bayar DESC
                    ");

                    if (mysqli_num_rows($query) > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                    ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= e($row['nisn']); ?></td>
                                <td><?= e($row['nis']); ?></td>
                                <td><?= e($row['nama']); ?></td>
                                <td><?= e($row['nama_kelas']); ?></td>
                                <td><?= e($row['tgl_bayar']); ?></td>
                                <td><?= e($row['batas_pembayaran']); ?></td>
                                <td><?= e($row['jumlah_bulan']); ?> Bulan</td>
                                <td><?= rupiah($row['jumlah_bayar']); ?></td>
                                <td>
                                    <span class="badge bg-primary"><?= e($row['status']); ?></span>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted">
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