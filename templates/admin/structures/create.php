<?php

declare(strict_types=1);

$title = 'Добавление конструкции';

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
                    Добавление конструкции
                </h1>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= e((string) $error) ?>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <section>

        <form
            method="post"
            action="/admin/structures/store"
            enctype="multipart/form-data"
            class="mb-4"
        >
            <input
                type="hidden"
                name="_csrf"
                value="<?= e(csrf_token()) ?>"
            >

            <div class="mb-3">
                <label for="structure-number" class="form-label">
                    Номер конструкции
                </label>

                <input
                    type="text"
                    id="structure-number"
                    name="number"
                    class="form-control"
                    required
                >
            </div>

            <div class="mb-3">
                <label for="structure-type" class="form-label">
                    Тип конструкции
                </label>

                <select
                    id="structure-type"
                    name="type"
                    class="form-select"
                    required
                >
                    <?php foreach ($structureTypes as $typeKey => $type): ?>
                        <option value="<?= e($typeKey) ?>">
                            <?= e((string) $type['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="structure-description" class="form-label">
                    Местоположение
                </label>

                <textarea
                    id="structure-description"
                    name="location_description"
                    class="form-control"
                    rows="3"
                    required
                ></textarea>
            </div>


            <button type="submit" class="btn btn-danger">
                Создать конструкцию
            </button>
        </form>

            <p class="admin-dashboard__intro">
                Создание новой рекламной конструкции.
            </p>

            <a
                href="/admin/structures"
                class="btn btn-outline-secondary"
            >
                Назад к конструкциям
            </a>
        </section>
    </main>
</div>

<?php
$content = ob_get_clean();

require dirname(__DIR__, 2) . '/layouts/admin.php';