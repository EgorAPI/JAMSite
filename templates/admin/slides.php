<div class="admin-shell">

    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <?php
    $flash = $_SESSION['admin_flash'] ?? null;

    unset($_SESSION['admin_flash']);
    ?>

    <main class="admin-main">

        <header class="admin-topbar">
            <div>
                <p class="admin-topbar__eyebrow">
                    Главная страница
                </p>

                <h1 class="admin-topbar__title">
                    Слайды
                </h1>
            </div>
        </header>
        <?php if (!empty($flash)): ?>
            <div
                class="alert alert-<?= e($flash['type'] ?? 'success') ?>"
                role="alert"
            >
                <?= e($flash['message'] ?? '') ?>
            </div>
        <?php endif; ?>
        <section class="admin-slides">
            <p class="admin-dashboard__intro">
                Управление слайдами hero-блока.
            </p>
            <form
                method="post"
                action="/admin/slides"
                enctype="multipart/form-data"
                class="admin-slides__upload"
            >
                <input
                    type="hidden"
                    name="_csrf"
                    value="<?= e(csrf_token()) ?>"
                >

                <div class="admin-slides__upload-info">
                    <strong>Добавить слайд</strong>

                    <p>
                        Рекомендуемый размер: 1600×1280 px, соотношение сторон 5:4.
                        JPG, PNG или WebP, до 10 МБ.
                        Изображение будет автоматически сохранено в WebP.
                    </p>
                </div>

                <div class="admin-slides__upload-controls">
                    <input
                        type="file"
                        name="image"
                        class="form-control"
                        accept="image/jpeg,image/png,image/webp"
                        required
                    >

                    <button
                        type="submit"
                        class="btn btn-danger"
                    >
                        Добавить слайд
                    </button>
                </div>
            </form>
            <div class="admin-slides__table-wrap">

                <table class="table admin-slides__table">
                    <thead>
                        <tr>
                            <th>Слайд</th>
                            <th>Статус</th>
                            <th>Сортировка</th>
                            <th class="text-end">Действия</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($slides as $slide): ?>
                            <tr>
                                <td>
                                    <div class="admin-work-cell">

                                        <div class="admin-work-cell__thumb">
                                            <?php if (!empty($slide['image'])): ?>
                                                <img
                                                    src="<?= e($slide['image']) ?>"
                                                    alt="<?= e($slide['title'] ?? '') ?>"
                                                    class="admin-work-cell__image"
                                                >
                                            <?php endif; ?>
                                        </div>

                                        <strong class="admin-work-cell__title">
                                            <?= e($slide['title'] ?? 'Без названия') ?>
                                        </strong>

                                    </div>
                                </td>

                                <td>
                                    <td>
                                        <form
                                            method="post"
                                            action="/admin/slides/toggle"
                                            class="admin-slide-status-form"
                                        >
                                            <input
                                                type="hidden"
                                                name="_csrf"
                                                value="<?= e(csrf_token()) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= e((string) ($slide['id'] ?? '')) ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="admin-status <?= !empty($slide['active']) ? 'admin-status--active' : 'admin-status--inactive' ?>"
                                            >
                                                <?= !empty($slide['active']) ? 'Активен' : 'Неактивен' ?>
                                            </button>
                                        </form>
                                    </td>
                                </td>

                                <td>
                                    <form
                                        method="post"
                                        action="/admin/slides/sort"
                                        class="admin-slide-sort-form"
                                    >
                                        <input
                                            type="hidden"
                                            name="_csrf"
                                            value="<?= e(csrf_token()) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= e((string) ($slide['id'] ?? '')) ?>"
                                        >

                                        <input
                                            type="number"
                                            name="sort_order"
                                            class="form-control admin-slide-sort-form__input"
                                            value="<?= e((string) ($slide['sort_order'] ?? 0)) ?>"
                                            min="0"
                                            step="1"
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-light admin-slide-sort-form__save"
                                        >
                                            OK
                                        </button>
                                    </form>
                                </td>

                                <td class="text-end">
                                    <div class="admin-table-actions">

                                        <form
                                            method="post"
                                            action="/admin/slides/delete"
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
                                                value="<?= e((string) ($slide['id'] ?? '')) ?>"
                                            >

                                            <button
                                                type="button"
                                                class="admin-table-action admin-table-action--danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteSlideModal"
                                            >
                                                Удалить
                                            </button>
                                        </form>

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
<div
    class="modal fade"
    id="deleteSlideModal"
    tabindex="-1"
    aria-labelledby="deleteSlideModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h2
                    class="modal-title fs-5"
                    id="deleteSlideModalLabel"
                >
                    Удалить слайд?
                </h2>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Закрыть"
                ></button>
            </div>

            <div class="modal-body">
                Слайд и его изображение будут удалены без возможности восстановления.
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
                    id="confirmDeleteSlide"
                >
                    Удалить
                </button>
            </div>

        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteModal = document.getElementById('deleteSlideModal');
    const confirmButton = document.getElementById('confirmDeleteSlide');

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