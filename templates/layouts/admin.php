<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= e($title ?? 'Джем Admin') ?></title>
    <meta name="robots" content="noindex, nofollow">

    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>

<body class="admin-body">

    <?= $content ?>

    <script src="/assets/js/bootstrap.bundle.min.js" defer></script>
</body>
</html>