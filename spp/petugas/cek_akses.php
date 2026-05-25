<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['level']) || $_SESSION['level'] != 'petugas') {
    header("Location: ../login.php");
    exit;
}
?>