<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../koneksi.php';

if (!isset($_SESSION['level']) || $_SESSION['level'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

/* SWEET ALERT SESSION */
if (!function_exists('setAlert')) {
    function setAlert($icon, $title, $text, $redirect)
    {
        $_SESSION['swal'] = [
            'icon' => $icon,
            'title' => $title,
            'text' => $text
        ];

        header("Location: " . $redirect);
        exit;
    }
}
?>