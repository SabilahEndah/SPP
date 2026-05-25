<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title><?= $title ?? 'Siswa SPP'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f9ff;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(180deg, #198754, #0f5132);
            position: fixed;
            top: 0;
            left: 0;
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
            color: #e8fff2;
            padding: 12px 18px;
            border-radius: 10px;
            margin-bottom: 7px;
        }

        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.18);
            color: white;
        }

        .sidebar .nav-link.active {
            background: white;
            color: #198754;
            font-weight: bold;
        }

        .content {
            margin-left: 260px;
            padding: 25px;
        }

        .top-card {
            background: linear-gradient(135deg, #198754, #20c997);
            color: white;
            border-radius: 18px;
        }

        .card {
            border: none;
            border-radius: 15px;
        }

        .btn-green {
            background: #198754;
            color: white;
        }

        .btn-green:hover {
            background: #146c43;
            color: white;
        }

        @media print {
            .sidebar,
            .no-print {
                display: none !important;
            }

            .content {
                margin-left: 0;
                padding: 0;
            }

            body {
                background: white;
            }
        }
    </style>
</head>
<body>