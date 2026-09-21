<div class="admin-shell">

    <?php require __DIR__ . '/partials/sidebar.php'; ?>
    <?php
        $flash = $_SESSION['admin_flash'] ?? null;

        unset($_SESSION['admin_flash']);
    ?>

    <?php
        $categoryTitles = [];

        foreach ($categories as $category) {
            $categoryTitles[$category['id']] = $category['title'];
        }
    ?>

    <main class="admin-main">

        <header class="admin-topbar admin-topbar--works">

            <div>
                <p class="admin-topbar__eyebrow">
                    Портфолио
                </p>

                <h1 class="admin-topbar__title">
                    Работы
                </h1>
            </div>

            <a href="/admin/works/create" class="btn btn-danger admin-works__add">
                Добавить работу
            </a>

        </header>

        <?php if (!empty($flash)): ?>
            <div
                class="alert alert-<?= e($flash['type'] ?? 'success') ?>"
                role="alert"
            >
                <?= e($flash['message'] ?? '') ?>
            </div>
        <?php endif; ?>
        <section class="admin-works">

            <div class="admin-works__summary">
                <p class="admin-dashboard__intro">
                    Управление работами портфолио.
                </p>

                <span class="admin-works__count">
                    Всего: <?= count($works) ?>
                </span>
            </div>
            <div class="admin-works__filters">

                <div class="admin-works__search">
                    <label for="worksSearch" class="visually-hidden">
                        Поиск по работам
                    </label>

                    <input
                        type="search"
                        id="worksSearch"
                        class="form-control"
                        placeholder="Поиск по названию..."
                        autocomplete="off"
                    >
                </div>

                <div class="admin-works__filter-buttons">
                    <button
                        type="button"
                        class="admin-works__filter is-active"
                        data-featured-filter="all"
                    >
                        Все
                    </button>

                    <button
                        type="button"
                        class="admin-works__filter"
                        data-featured-filter="featured"
                    >
                        Избранные
                    </button>
                </div>

            </div>
            <div class="admin-works__table-wrap">

            <table class="table admin-works__table">
                <thead>
                    <tr>
                        <th>Работа</th>
                        <th>Категория</th>
                        <th>Статус</th>
                        <th>Избранное</th>
                        <th class="text-end">Действия</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($works)): ?>
                        <tr>
                            <td colspan="5" class="admin-works__empty">
                                Работ пока нет.
                            </td>
                        </tr>
                    <?php else: ?>

                        <?php foreach ($works as $work): ?>
                        <tr
                            data-work-row
                            data-title="<?= e(mb_strtolower((string) ($work['title'] ?? ''))) ?>"
                            data-featured="<?= !empty($work['featured']) ? '1' : '0' ?>"
                            >
                            <td>
                                <div class="admin-work-cell">
                                    <div class="admin-work-cell__thumb">
                                        <?php if (!empty($work['image'])): ?>
                                            <img
                                                src="<?= e($work['image']) ?>"
                                                alt="<?= e($work['title'] ?? '') ?>"
                                                class="admin-work-cell__image"
                                            >
                                        <?php endif; ?>
                                    </div>

                                    <strong class="admin-work-cell__title">
                                        <?= e($work['title'] ?? 'Без названия') ?>
                                    </strong>
                                </div>
                            </td>

                            <td>
                                <?php
                                $categoryId = $work['category'] ?? null;
                                ?>

                                <?= e(
                                    $categoryId !== null
                                        ? ($categoryTitles[$categoryId] ?? $categoryId)
                                        : '—'
                                ) ?>
                            </td>

                            <td>
                                <?php if (!empty($work['active'])): ?>
                                    <span class="admin-status admin-status--active">
                                        Активна
                                    </span>
                                <?php else: ?>
                                    <span class="admin-status admin-status--inactive">
                                        Неактивна
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?= !empty($work['featured']) ? 'Да' : 'Нет' ?>
                            </td>

                            <td class="text-end">
                                <div class="admin-table-actions">

                                    <a
                                        href="/admin/works/edit?id=<?= urlencode((string) ($work['id'] ?? '')) ?>"
                                        class="admin-table-action"
                                    >
                                        Редактировать
                                    </a>

                                    <form
                                        method="post"
                                        action="/admin/works/delete"
                                        class="admin-table-delete-form"
                                    >
                                        <input
                                            type="hidden"
                                            name="_csrf"
                                            value="<?= e(csrf_token()) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= e((string) ($work['id'] ?? '')) ?>"
                                        >

                                        <button
                                            type="button"
                                            class="admin-table-action admin-table-action--danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteWorkModal"
                                            data-work-id="<?= e((string) ($work['id'] ?? '')) ?>"
                                        >
                                            Удалить
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                     <?php endif; ?>
                </tbody>
            </table>

        </div>

        </section>

    </main>

</div>
<div
    class="modal fade"
    id="deleteWorkModal"
    tabindex="-1"
    aria-labelledby="deleteWorkModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h2
                    class="modal-title fs-5"
                    id="deleteWorkModalLabel"
                >
                    Удалить работу?
                </h2>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Закрыть"
                ></button>
            </div>

            <div class="modal-body">
                Это действие нельзя отменить.
            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal"
                >
                    Отмена
                </button>

                <button
                    type="button"
                    class="btn btn-danger"
                    id="confirmDeleteWork"
                >
                    Удалить
                </button>
            </div>

        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteModal = document.getElementById('deleteWorkModal');
    const confirmButton = document.getElementById('confirmDeleteWork');

    let deleteForm = null;

    deleteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        if (!button) {
            return;
        }

        deleteForm = button.closest('form');
    });

    confirmButton.addEventListener('click', function () {
        if (deleteForm) {
            deleteForm.submit();
        }
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('worksSearch');
    const filterButtons = document.querySelectorAll('[data-featured-filter]');
    const rows = document.querySelectorAll('[data-work-row]');

    let featuredFilter = 'all';

    function applyWorksFilters() {
        const query = searchInput.value.trim().toLowerCase();

        rows.forEach((row) => {
            const title = row.dataset.title || '';
            const isFeatured = row.dataset.featured === '1';

            const matchesSearch = title.includes(query);

            const matchesFeatured =
                featuredFilter === 'all'
                || (featuredFilter === 'featured' && isFeatured);

            row.style.display =
                matchesSearch && matchesFeatured
                    ? ''
                    : 'none';
        });
    }

    searchInput.addEventListener('input', applyWorksFilters);

    filterButtons.forEach((button) => {
        button.addEventListener('click', function () {
            featuredFilter = button.dataset.featuredFilter || 'all';

            filterButtons.forEach((item) => {
                item.classList.remove('is-active');
            });

            button.classList.add('is-active');

            applyWorksFilters();
        });
    });
});
</script>