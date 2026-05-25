<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar p-3">
    <div class="brand">
        Petugas SPP
    </div>

    <ul class="nav flex-column mt-4">


        <li class="nav-item">
            <a href="index.php" class="nav-link <?= $currentPage == 'index.php' ? 'active' : ''; ?>">
                Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a href="input_pembayaran.php" class="nav-link <?= $currentPage == 'input_pembayaran.php' ? 'active' : ''; ?>">
                Input Pembayaran
            </a>
        </li>
        <li class="nav-item">
            <a href="tagihan_semester.php" class="nav-link <?= $currentPage == 'tagihan_semester.php' ? 'active' : ''; ?>">
                Tagihan Semester
            </a>
        </li>

        <li class="nav-item">
            <a href="pembayaran.php" class="nav-link <?= $currentPage == 'pembayaran.php' ? 'active' : ''; ?>">
                Data Pembayaran
            </a>
        </li>

        <li class="nav-item">
            <a href="detail_pembayaran.php" class="nav-link <?= $currentPage == 'detail_pembayaran.php' ? 'active' : ''; ?>">
                Detail Pembayaran
            </a>
        </li>

        <li class="nav-item">
            <a href="../logout.php" class="nav-link bg-light text-primary fw-bold">
                Logout
            </a>
        </li>
    </ul>
</div>