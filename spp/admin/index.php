<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/cek_akses.php';

$title = "Dashboard Admin";

require_once __DIR__ . '/header.php';
require_once __DIR__ . '/sidebar.php';

function jumlahData($koneksi, $table) {
    $query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM $table");
    $data = mysqli_fetch_assoc($query);
    return $data['total'] ?? 0;
}
?>

<div class="content">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4>Dashboard Admin</h4>
            <p class="text-muted mb-0">
                Selamat datang, <?= e($_SESSION['nama_petugas']); ?>. Anda login sebagai admin.
            </p>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Data Kelas</h6>
                    <h3><?= jumlahData($koneksi, 'tb_kelas'); ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Data Siswa</h6>
                    <h3><?= jumlahData($koneksi, 'tb_siswa'); ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Pembayaran</h6>
                    <h3><?= jumlahData($koneksi, 'tb_pembayaran'); ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Petugas</h6>
                    <h3><?= jumlahData($koneksi, 'tb_petugas'); ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>