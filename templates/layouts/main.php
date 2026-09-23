<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/assets/images/logo/logo.webp" type="image/webp">

    <title><?= e($title ?? 'Рекламное агентство «Джем»') ?></title>
    <?php if (!empty($robots)): ?>
        <meta name="robots" content="<?= e($robots) ?>">
    <?php endif; ?>
    <?php if (!empty($metaDescription)): ?>
        <meta
            name="description"
            content="<?= e($metaDescription) ?>"
        >
    <?php endif; ?>

    <?php if (!empty($canonical)): ?>
        <link
            rel="canonical"
            href="<?= e($canonical) ?>"
        >
    <?php endif; ?>

    <?php if (!empty($ogTitle)): ?>
        <meta property="og:title" content="<?= e($ogTitle) ?>">
    <?php endif; ?>

    <?php if (!empty($ogDescription)): ?>
        <meta property="og:description" content="<?= e($ogDescription) ?>">
    <?php endif; ?>

    <?php if (!empty($ogUrl)): ?>
        <meta property="og:url" content="<?= e($ogUrl) ?>">
    <?php endif; ?>

    <meta property="og:type" content="<?= e($ogType ?? 'website') ?>">
    <meta property="og:site_name" content="Рекламное агентство «Джем»">
    <meta property="og:locale" content="ru_RU">

    <?php if (!empty($ogImage)): ?>
        <meta property="og:image" content="<?= e($ogImage) ?>">
    <?php endif; ?>

    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">

    <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >
    <link rel="stylesheet" href="/assets/css/app.css">

    <?php if (!empty($localBusinessSchema)): ?>
        <script type="application/ld+json">
            <?= json_encode(
                $localBusinessSchema,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
                | JSON_PRETTY_PRINT
            ) ?>
        </script>
    <?php endif; ?>
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