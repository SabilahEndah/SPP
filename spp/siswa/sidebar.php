<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar p-3">
    <div class="brand">
        Siswa SPP
    </div>

    <ul class="nav flex-column mt-4">

        <li class="nav-item">
            <a href="index.php" class="nav-link <?= $currentPage == 'index.php' ? 'active' : ''; ?>">
                Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a href="tagihan_semester.php" class="nav-link <?= $currentPage == 'tagihan_semester.php' ? 'active' : ''; ?>">
                Tagihan Semester
            </a>
        </li>

        <li class="nav-item">
            <a href="riwayat_pembayaran.php" class="nav-link <?= $currentPage == 'riwayat_pembayaran.php' ? 'active' : ''; ?>">
                Riwayat Pembayaran
            </a>
        </li>

        <li class="nav-item mt-4">
            <a href="../logout.php" class="nav-link bg-light text-success fw-bold">
                Logout
            </a>
        </li>

    </ul>
</div>