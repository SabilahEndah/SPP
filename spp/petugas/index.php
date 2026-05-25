<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/cek_akses.php';

$title = "Dashboard Petugas";

function jumlahData($koneksi, $table) {
    $query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM $table");
    $data = mysqli_fetch_assoc($query);
    return $data['total'] ?? 0;
}

include 'header.php';
include 'sidebar.php';
?>

<div class="content">

    <div class="card top-card shadow-sm mb-4">
        <div class="card-body p-4">
            <h3 class="mb-1">Dashboard Petugas</h3>
            <p class="mb-0">
                Selamat datang, <?= e($_SESSION['nama_petugas']); ?>. Anda login sebagai petugas pembayaran SPP.
            </p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Data Siswa</h6>
                    <h3 class="text-primary"><?= jumlahData($koneksi, 'tb_siswa'); ?></h3>
                    <p class="small text-muted mb-0">Jumlah seluruh siswa terdaftar</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Transaksi Pembayaran</h6>
                    <h3 class="text-primary"><?= jumlahData($koneksi, 'tb_pembayaran'); ?></h3>
                    <p class="small text-muted mb-0">Jumlah data pembayaran masuk</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Cek Pembayaran</h6>
                    <h3 class="text-primary"><?= jumlahData($koneksi, 'cek_pembayaran'); ?></h3>
                    <p class="small text-muted mb-0">Jumlah data status pembayaran</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Menu Petugas</h5>
        </div>

        <div class="card-body">
            <div class="row g-3">

                <div class="col-md-3">
                    <a href="input_pembayaran.php" class="text-decoration-none">
                        <div class="card shadow-sm border border-primary h-100">
                            <div class="card-body text-center">
                                <h5 class="text-primary">Input Pembayaran</h5>
                                <p class="text-muted small mb-0">
                                    Tambah transaksi pembayaran siswa.
                                </p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="pembayaran.php" class="text-decoration-none">
                        <div class="card shadow-sm border border-primary h-100">
                            <div class="card-body text-center">
                                <h5 class="text-primary">Data Pembayaran</h5>
                                <p class="text-muted small mb-0">
                                    Melihat seluruh transaksi pembayaran.
                                </p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="cek_pembayaran.php" class="text-decoration-none">
                        <div class="card shadow-sm border border-primary h-100">
                            <div class="card-body text-center">
                                <h5 class="text-primary">Cek Pembayaran</h5>
                                <p class="text-muted small mb-0">
                                    Mengecek status pembayaran siswa.
                                </p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="detail_pembayaran.php" class="text-decoration-none">
                        <div class="card shadow-sm border border-primary h-100">
                            <div class="card-body text-center">
                                <h5 class="text-primary">Detail Pembayaran</h5>
                                <p class="text-muted small mb-0">
                                    Melihat detail dan laporan pembayaran.
                                </p>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>