<?php

declare(strict_types=1);

$title = 'Конструкции';

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
                    Конструкции
                </h1>
            </div>
        </header>

        <section>
            <p class="admin-dashboard__intro">
                Управление рекламными конструкциями на карте.
            </p>

            <div class="mb-4 d-flex gap-2 flex-wrap">
                <a
                    href="/admin/structures/create"
                    class="btn btn-danger"
                >
                    Добавить конструкцию
                </a>

                <a
                    href="/admin/structures/import"
                    class="btn btn-outline-secondary"
                >
                    Импорт Excel
                </a>

                <a
                    href="/admin/structures/export"
                    class="btn btn-outline-secondary"
                >
                    Экспорт Excel
                </a>
            </div>

            <div class="row g-3 align-items-end mb-4">
                <div class="col-lg-5">
                    <label for="structures-admin-search" class="form-label">
                        Поиск
                    </label>

                    <input
                        type="search"
                        id="structures-admin-search"
                        class="form-control"
                        placeholder="Номер или местоположение"
                        autocomplete="off"
                    >
                </div>

                <div class="col-lg-3">
                    <label for="structures-admin-type" class="form-label">
                        Тип конструкции
                    </label>

                    <select
                        id="structures-admin-type"
                        class="form-select"
                    >
                        <option value="">Все типы</option>

                        <?php foreach ($structureTypes as $typeKey => $type): ?>
                            <option value="<?= e($typeKey) ?>">
                                <?= e((string) $type['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-lg-2">
                    <label for="structures-admin-sort" class="form-label">
                        Сортировка
                    </label>

                    <select
                        id="structures-admin-sort"
                        class="form-select"
                    >
                        <option value="number-asc">
                            По номеру — по возрастанию
                        </option>

                        <option value="number-desc">
                            По номеру — по убыванию
                        </option>

                        <option value="type-asc">
                            По типу — А–Я
                        </option>
                    </select>
                </div>
            </div>

            <p>
                Найдено конструкций: <?= count($structures) ?>
            </p>

            <div class="admin-slides__table-wrap">

                <table class="table admin-slides__table">
                    <thead>
                        <tr>
                            <th>Номер</th>
                            <th>Тип</th>
                            <th>Местоположение</th>
                            <th>Фото поверхности</th>
                            <th>Статус на сайте</th>
                            <th class="text-end">Действия</th>
                        </tr>
                    </thead>

                    <tbody id="structures-admin-table-body">
                        <?php foreach ($structures as $structure): ?>
                            <tr
                                data-structure-row
                                data-number="<?= e((string) ($structure['number'] ?? '')) ?>"
                                data-description="<?= e((string) ($structure['location_description'] ?? '')) ?>"
                                data-type="<?= e((string) ($structure['type'] ?? '')) ?>"
                                data-type-name="<?= e(
                                    (string) (
                                        $structureTypes[$structure['type']]['name']
                                        ?? $structure['type']
                                        ?? ''
                                    )
                                ) ?>"
                            >
                                <td>
                                    <strong>
                                        <?= e((string) ($structure['number'] ?? '')) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= e(
                                        $structureTypes[$structure['type']]['name']
                                        ?? (string) ($structure['type'] ?? '')
                                    ) ?>
                                </td>

                                <td>
                                    <?= e((string) ($structure['location_description'] ?? '')) ?>
                                </td>

                                <td>
                                    <?php foreach (($structure['surfaces'] ?? []) as $surface): ?>
                                        <div class="mb-1 text-nowrap">
                                            <?= e((string) ($surface['name'] ?? '')) ?>:

                                           <?php if (!empty($surface['image'])): ?>
                                                <strong class="text-success">✓ Есть</strong>
                                            <?php else: ?>
                                                <span class="text-muted">— Нет</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </td>

                                <td>
                                    <div class="form-check form-switch">
                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            data-quick-structure-visibility
                                            data-structure-id="<?= e((string) ($structure['id'] ?? '')) ?>"
                                            <?= !empty($structure['is_visible']) ? 'checked' : '' ?>
                                        >

                                        <label class="form-check-label">
                                            <?= !empty($structure['is_visible'])
                                                ? 'Опубликована'
                                                : 'Скрыта' ?>
                                        </label>
                                    </div>
                                </td>

                                <td class="text-end">
                                    <div class="admin-table-actions">
                                        <a
                                            href="/admin/structures/edit?id=<?= urlencode((string) ($structure['id'] ?? '')) ?>"
                                            class="admin-table-action"
                                        >
                                            Редактировать
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </section>

    </main>

</div>

<script>
window.structuresAdminCsrf = <?= json_encode(
    csrf_token(),
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
) ?>;
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('structures-admin-search');
    const typeSelect = document.getElementById('structures-admin-type');
    const sortSelect = document.getElementById('structures-admin-sort');
    const tableBody = document.getElementById('structures-admin-table-body');
    const rows = document.querySelectorAll('[data-structure-row]');

    if (!searchInput) {
        return;
    }

    function applyFilters() {
        const query = searchInput.value
            .trim()
            .toLowerCase();

        const selectedType = typeSelect
            ? typeSelect.value
            : '';

        rows.forEach(function (row) {
            const number = (row.dataset.number || '').toLowerCase();
            const description = (row.dataset.description || '').toLowerCase();
            const type = row.dataset.type || '';

            const matchesSearch =
                number.includes(query)
                || description.includes(query);

            const matchesType =
                selectedType === ''
                || type === selectedType;


            row.hidden = !(
                matchesSearch
                && matchesType
            );
        });
    }

    searchInput.addEventListener('input', applyFilters);

    if (typeSelect) {
        typeSelect.addEventListener('change', applyFilters);
    }


    function applySorting() {
        if (!sortSelect || !tableBody) {
            return;
        }

        const rowsArray = Array.from(
            tableBody.querySelectorAll('[data-structure-row]')
        );

        rowsArray.sort(function (a, b) {
            const sort = sortSelect.value;

            if (sort === 'number-desc') {
                return (b.dataset.number || '').localeCompare(
                    a.dataset.number || '',
                    'ru',
                    {
                        numeric: true,
                        sensitivity: 'base'
                    }
                );
            }

            if (sort === 'type-asc') {
                return (a.dataset.typeName || '').localeCompare(
                    b.dataset.typeName || '',
                    'ru',
                    {
                        sensitivity: 'base'
                    }
                );
            }

            return (a.dataset.number || '').localeCompare(
                b.dataset.number || '',
                'ru',
                {
                    numeric: true,
                    sensitivity: 'base'
                }
            );
        });

        rowsArray.forEach(function (row) {
            tableBody.appendChild(row);
        });
    }

    sortSelect?.addEventListener('change', applySorting);

    applySorting();


    const visibilitySwitches = document.querySelectorAll(
        '[data-quick-structure-visibility]'
    );

    visibilitySwitches.forEach(function (toggle) {
        toggle.addEventListener('change', async function () {
            const previousChecked = !toggle.checked;
            const label = toggle
                .closest('.form-check')
                ?.querySelector('.form-check-label');

            const formData = new FormData();

            formData.append('_csrf', window.structuresAdminCsrf);
            formData.append(
                'structure_id',
                toggle.dataset.structureId
            );
            formData.append(
                'is_visible',
                toggle.checked ? '1' : '0'
            );

            toggle.disabled = true;

            try {
                const response = await fetch(
                    '/admin/structures/visibility',
                    {
                        method: 'POST',
                        body: formData
                    }
                );

                if (!response.ok) {
                    throw new Error(
                        'Не удалось изменить видимость.'
                    );
                }

                if (label) {
                    label.textContent = toggle.checked
                        ? 'Опубликована'
                        : 'Скрыта';
                }
            } catch (error) {
                toggle.checked = previousChecked;

                alert('Не удалось изменить видимость.');
            } finally {
                toggle.disabled = false;
            }
        });
    });
});
</script>

<?php
$content = ob_get_clean();

require dirname(__DIR__, 2) . '/layouts/admin.php';