<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title><?= $title ?? 'Petugas SPP'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background: #eef5ff;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(180deg, #0d6efd, #084298);
            position: fixed;
            left: 0;
            top: 0;
            color: white;
        }

        .sidebar .brand {
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            padding: 25px 10px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .sidebar .nav-link {
            color: #eaf3ff;
            padding: 12px 18px;
            border-radius: 10px;
            margin-bottom: 7px;
            font-size: 15px;
        }

        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.18);
            color: white;
        }

        .sidebar .nav-link.active {
            background: white;
            color: #0d6efd;
            font-weight: bold;
        }

        .sidebar .menu-title {
            font-size: 12px;
            color: #cfe2ff;
            margin-top: 20px;
            margin-bottom: 8px;
            padding-left: 8px;
            text-transform: uppercase;
        }

        .content {
            margin-left: 260px;
            padding: 25px;
        }

        .top-card {
            background: linear-gradient(135deg, #0d6efd, #4dabf7);
            color: white;
            border-radius: 18px;
        }

        .card {
            border: none;
            border-radius: 15px;
        }

        .btn-blue {
            background: #0d6efd;
            color: white;
        }

        .btn-blue:hover {
            background: #084298;
            color: white;
        }

        .table-primary th {
            background: #0d6efd !important;
            color: white;
        }
    </style>
</head>
<body>