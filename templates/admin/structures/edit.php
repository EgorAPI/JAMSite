<?php

declare(strict_types=1);

$title = 'Редактирование конструкции';

ob_start();
?>

<div class="admin-shell">

    <?php require dirname(__DIR__) . '/partials/sidebar.php'; ?>

    <main class="admin-main">

        <header class="admin-topbar">
            <div>
                <p class="admin-topbar__eyebrow">
                    Рекламные конструкции
                </p>

                <h1 class="admin-topbar__title">
                    Редактирование конструкции № <?= e((string) ($structure['number'] ?? '')) ?>
                </h1>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= e((string) $error) ?>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <section>
            <p class="admin-dashboard__intro">
                <?= e((string) ($structure['location_description'] ?? '')) ?>
            </p>

            <form
                method="post"
                action="/admin/structures/update"
                enctype="multipart/form-data"
            >
                <input
                    type="hidden"
                    name="_csrf"
                    value="<?= e(csrf_token()) ?>"
                >

                <input
                    type="hidden"
                    name="id"
                    value="<?= e((string) ($structure['id'] ?? '')) ?>"
                >

                <div class="mb-4">
                    <label for="structure-number" class="form-label">
                        Номер конструкции
                    </label>

                    <input
                        type="text"
                        id="structure-number"
                        name="number"
                        class="form-control"
                        value="<?= e((string) ($structure['number'] ?? '')) ?>"
                    >
                </div>

                <div class="mb-4">
                    <label for="structure-type" class="form-label">
                        Тип конструкции
                    </label>

                    <select
                        id="structure-type"
                        name="type"
                        class="form-select"
                    >
                        <?php foreach ($structureTypes as $typeKey => $typeConfig): ?>
                            <option
                                value="<?= e($typeKey) ?>"
                                <?= ($structure['type'] ?? '') === $typeKey ? 'selected' : '' ?>
                            >
                                <?= e($typeConfig['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="structure-description" class="form-label">
                        Местоположение
                    </label>

                    <textarea
                        id="structure-description"
                        name="location_description"
                        class="form-control"
                        rows="3"
                    ><?= e((string) ($structure['location_description'] ?? '')) ?></textarea>
                </div>

            

                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input
                            type="checkbox"
                            id="structure-visible"
                            name="is_visible"
                            class="form-check-input"
                            value="1"
                            <?= !empty($structure['is_visible']) ? 'checked' : '' ?>
                        >

                        <label
                            for="structure-visible"
                            class="form-check-label"
                        >
                            Показывать конструкцию на сайте
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    <h2 class="h5 mb-3">
                        Рекламные поверхности
                    </h2>

                    <?php foreach (($structure['surfaces'] ?? []) as $surface): ?>
                        <div class="border rounded p-3 mb-3">
                            <strong>
                                <?= e((string) ($surface['name'] ?? '')) ?>
                            </strong>

                            

                            <div class="mt-3">
                                <label
                                    for="surface-image-<?= e((string) ($surface['id'] ?? '')) ?>"
                                    class="form-label"
                                >
                                    Фотография поверхности
                                </label>

                                <?php if (!empty($surface['image'])): ?>
                                    <div class="mb-2">
                                        <img
                                            src="<?= e((string) $surface['image']) ?>"
                                            alt="Фотография поверхности"
                                            style="max-width: 300px; max-height: 200px; object-fit: cover;"
                                            class="img-thumbnail"
                                        >
                                    </div>
                                    <div class="form-check mb-2">
                                    <input
                                        type="checkbox"
                                        id="surface-image-remove-<?= e((string) ($surface['id'] ?? '')) ?>"
                                        name="surface_image_remove[<?= e((string) ($surface['id'] ?? '')) ?>]"
                                        value="1"
                                        class="form-check-input"
                                    >

                                    <label
                                        for="surface-image-remove-<?= e((string) ($surface['id'] ?? '')) ?>"
                                        class="form-check-label"
                                    >
                                        Удалить фотографию
                                    </label>
                                </div>
                                <?php endif; ?>

                                <input
                                    type="file"
                                    id="surface-image-<?= e((string) ($surface['id'] ?? '')) ?>"
                                    name="surface_image[<?= e((string) ($surface['id'] ?? '')) ?>]"
                                    class="form-control"
                                    accept="image/jpeg,image/png,image/webp"
                                >
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="structure-latitude" class="form-label">
                            Широта
                        </label>

                        <input
                            type="number"
                            id="structure-latitude"
                            name="latitude"
                            class="form-control"
                            step="any"
                            value="<?= e((string) ($structure['latitude'] ?? '')) ?>"
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="structure-longitude" class="form-label">
                            Долгота
                        </label>

                        <input
                            type="number"
                            id="structure-longitude"
                            name="longitude"
                            class="form-control"
                            step="any"
                            value="<?= e((string) ($structure['longitude'] ?? '')) ?>"
                        >
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">
                        Точка на карте
                    </label>

                    <div
                        id="structure-coordinate-map"
                        style="width: 100%; height: 420px;"
                    ></div>

                    <div class="form-text">
                        Нажмите на карту, чтобы изменить координаты конструкции.
                    </div>
                </div>

                <button
                    type="submit"
                    class="btn btn-danger"
                >
                    Сохранить изменения
                </button>

                <a href="/admin/structures" class="btn btn-light">
                    ← Назад к конструкциям
                </a>
            </form>

            <form
                method="post"
                action="/admin/structures/delete"
                class="mt-3"
                onsubmit="return confirm('Удалить эту конструкцию? Это действие нельзя отменить.');"
            >
                <input
                    type="hidden"
                    name="_csrf"
                    value="<?= e(csrf_token()) ?>"
                >

                <input
                    type="hidden"
                    name="id"
                    value="<?= e((string) ($structure['id'] ?? '')) ?>"
                >

                <button
                    type="submit"
                    class="btn btn-outline-danger"
                >
                    Удалить конструкцию
                </button>
            </form>
        </section>

    </main>

</div>

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
>

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
></script>

<script src="/assets/js/admin-structure-map.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('structure-type');

    if (!typeSelect) {
        return;
    }

    const originalType = typeSelect.value;
    const form = typeSelect.closest('form');

    form.addEventListener('submit', function (event) {
        if (typeSelect.value === originalType) {
            return;
        }

        const confirmed = window.confirm(
            'При смене типа текущие поверхности, их статусы и фотографии будут сброшены. Продолжить?'
        );

        if (!confirmed) {
            event.preventDefault();
        }
    });
});
</script>

<?php
$content = ob_get_clean();

require dirname(__DIR__, 2) . '/layouts/admin.php';