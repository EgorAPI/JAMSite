<?php

declare(strict_types=1);

$title = 'Импорт конструкций из Excel';

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
                    Импорт конструкций из Excel
                </h1>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger mt-3" role="alert">
                        <?= e((string) $error) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success mt-3" role="alert">
                        <?= e((string) $success) ?>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <section>
            <div class="alert alert-warning" role="alert">
                <strong>Внимание!</strong>
                Импорт полностью заменит текущий список конструкций.
                Текущие статусы поверхностей и фотографии будут сброшены.
            </div>

            <p class="admin-dashboard__intro">
                Перед заменой данных система полностью проверит Excel-файл
                и создаст резервную копию текущего списка конструкций.
            </p>

            <form
                action="/admin/structures/import"
                method="post"
                enctype="multipart/form-data"
                class="mb-4"
                onsubmit="return confirm('Импорт полностью заменит текущие конструкции, статусы и фотографии. Продолжить?');"
            >
                <input
                    type="hidden"
                    name="_csrf"
                    value="<?= e(csrf_token()) ?>"
                >

                <div class="mb-3">
                    <label for="excel_file" class="form-label">
                        Excel-файл
                    </label>

                    <input
                        type="file"
                        class="form-control"
                        id="excel_file"
                        name="excel_file"
                        accept=".xlsx"
                        required
                    >

                    <div class="form-text">
                        Допускается файл формата .xlsx.
                    </div>
                </div>

                <button type="submit" class="btn btn-danger">
                    Импортировать и заменить текущие данные
                </button>
            </form>

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