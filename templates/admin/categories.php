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
                    Портфолио
                </p>

                <h1 class="admin-topbar__title">
                    Категории
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

        <section class="admin-categories">
            <p class="admin-dashboard__intro">
                Управление категориями портфолио.
            </p>
            <form
                method="post"
                action="/admin/categories"
                class="admin-categories__create"
            >
                <input
                    type="hidden"
                    name="_csrf"
                    value="<?= e(csrf_token()) ?>"
                >

                <div class="admin-categories__create-field">
                    <label for="categoryTitle" class="visually-hidden">
                        Название категории
                    </label>

                    <input
                        type="text"
                        id="categoryTitle"
                        name="title"
                        class="form-control"
                        placeholder="Название новой категории"
                        required
                    >
                </div>

                <button
                    type="submit"
                    class="btn btn-danger"
                >
                    Добавить
                </button>
            </form>
            <div class="admin-categories__table-wrap">

                <table class="table admin-categories__table">
                    <thead>
                        <tr>
                            <th>Название</th>
                            <th>Статус</th>
                            <th class="text-end">Действия</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td>
                                    <form
                                        method="post"
                                        action="/admin/categories/update"
                                        class="admin-category-inline-form"
                                    >
                                        <input
                                            type="hidden"
                                            name="_csrf"
                                            value="<?= e(csrf_token()) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= e((string) ($category['id'] ?? '')) ?>"
                                        >

                                        <input
                                            type="text"
                                            name="title"
                                            class="form-control admin-category-inline-form__input"
                                            value="<?= e($category['title'] ?? '') ?>"
                                            required
                                        >

                                        <button
                                            type="submit"
                                            class="btn btn-light admin-category-inline-form__save"
                                        >
                                            Сохранить
                                        </button>
                                    </form>
                                </td>


                                <td>
                                    <form
                                        method="post"
                                        action="/admin/categories/toggle"
                                        class="admin-category-status-form"
                                    >
                                        <input
                                            type="hidden"
                                            name="_csrf"
                                            value="<?= e(csrf_token()) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= e((string) ($category['id'] ?? '')) ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="admin-status <?= !empty($category['active']) ? 'admin-status--active' : 'admin-status--inactive' ?>"
                                        >
                                            <?= !empty($category['active']) ? 'Активна' : 'Неактивна' ?>
                                        </button>
                                    </form>
                                </td>
                                <td class="text-end">
                                    <form
                                        method="post"
                                        action="/admin/categories/delete"
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
                                            value="<?= e((string) ($category['id'] ?? '')) ?>"
                                        >

                                        <button
                                            type="button"
                                            class="admin-table-action admin-table-action--danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteCategoryModal"
                                        >
                                            Удалить
                                        </button>
                                    </form>
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
    id="deleteCategoryModal"
    tabindex="-1"
    aria-labelledby="deleteCategoryModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h2
                    class="modal-title fs-5"
                    id="deleteCategoryModalLabel"
                >
                    Удалить категорию?
                </h2>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Закрыть"
                ></button>
            </div>

            <div class="modal-body">
                Категорию можно удалить только если она не используется ни в одной работе.
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
                    id="confirmDeleteCategory"
                >
                    Удалить
                </button>
            </div>

        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteModal = document.getElementById('deleteCategoryModal');
    const confirmButton = document.getElementById('confirmDeleteCategory');

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