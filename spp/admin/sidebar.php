<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar p-3">
    <h4 class="text-white text-center mb-4">Admin SPP</h4>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="index.php" class="nav-link <?= $currentPage == 'index.php' ? 'active' : ''; ?>">
                Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a href="data_kelas.php" class="nav-link <?= $currentPage == 'data_kelas.php' ? 'active' : ''; ?>">
                Data Kelas
            </a>
        </li>

        <li class="nav-item">
            <a href="data_siswa.php" class="nav-link <?= $currentPage == 'data_siswa.php' ? 'active' : ''; ?>">
                Data Siswa
            </a>
        </li>

        <li class="nav-item">
            <a href="cek_pembayaran.php" class="nav-link <?= $currentPage == 'cek_pembayaran.php' ? 'active' : ''; ?>">
                Cek Pembayaran
            </a>
        </li>

        <li class="nav-item">
            <a href="pembayaran.php" class="nav-link <?= $currentPage == 'pembayaran.php' ? 'active' : ''; ?>">
                Pembayaran
            </a>
        </li>

        <li class="nav-item">
            <a href="detail_pembayaran.php" class="nav-link <?= $currentPage == 'detail_pembayaran.php' ? 'active' : ''; ?>">
                Detail Pembayaran
            </a>
        </li>

        <li class="nav-item">
            <a href="data_petugas.php" class="nav-link <?= $currentPage == 'data_petugas.php' ? 'active' : ''; ?>">
                Data Petugas
            </a>
        </li>

        <li class="nav-item mt-4">
            <a href="../logout.php" class="nav-link bg-danger text-white">
                Logout
            </a>
        </li>
    </ul>
</div>