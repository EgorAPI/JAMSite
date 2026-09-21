<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= e($title ?? 'Рекламное агентство «Джем»') ?></title>

    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">

    <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >
    <link rel="stylesheet" href="/assets/css/app.css">

</head>
<body>

<?php require dirname(__DIR__) . '/partials/header.php'; ?>

<main>
    <?= $content ?? '' ?>
</main>

<?php require dirname(__DIR__) . '/partials/footer.php'; ?>

<script src="/assets/js/bootstrap.bundle.min.js" defer></script>
<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
></script>

<script src="/assets/js/app.js" defer></script>

</body>
</html>