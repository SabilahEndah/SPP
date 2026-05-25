<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title><?= $title ?? 'Admin SPP'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f4f6f9;
        }

        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: #212529;
            position: fixed;
            left: 0;
            top: 0;
        }

        .sidebar .nav-link {
            color: #ddd;
            padding: 12px 18px;
            border-radius: 8px;
            margin-bottom: 5px;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: #0d6efd;
            color: white;
        }

        .content {
            margin-left: 260px;
            padding: 25px;
        }

        .card {
            border: none;
            border-radius: 12px;
        }
    </style>
</head>
<body>