<div class="admin-shell">

    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <main class="admin-main">

        <header class="admin-topbar">

            <div>
                <p class="admin-topbar__eyebrow">
                    Портфолио
                </p>

                <h1 class="admin-topbar__title">
                    Редактировать работу
                </h1>
            </div>

            <a href="/admin/works" class="admin-topbar__site-link">
                ← Назад к работам
            </a>

        </header>

        <section class="admin-work-form">

            <p class="admin-dashboard__intro">
                Изменение данных работы портфолио.
            </p>

            <form
                class="admin-form-card"
                method="post"
                action="/admin/works/update"
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
                    value="<?= e($work['id']) ?>"
                >

                <div class="mb-4">
                    <label for="title" class="form-label">
                        Название работы
                    </label>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        class="form-control"
                        value="<?= e($work['title'] ?? '') ?>"
                        required
                    >
                </div>
                <div class="mb-4">
                    <label for="category" class="form-label">
                        Категория
                    </label>

                    <select
                        id="category"
                        name="category"
                        class="form-select"
                        required
                    >
                        <option value="">
                            Выберите категорию
                        </option>

                        <?php foreach ($categories as $category): ?>
                            <option
                                value="<?= e($category['id']) ?>"
                                <?= ($work['category'] ?? '') === $category['id'] ? 'selected' : '' ?>
                            >
                                <?= e($category['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label">
                        Текущее изображение
                    </label>

                    <?php if (!empty($work['image'])): ?>
                        <div class="admin-work-edit__preview">
                            <img
                                src="<?= e($work['image']) ?>"
                                alt="<?= e($work['title'] ?? '') ?>"
                                class="admin-work-edit__preview-image"
                            >
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <label for="image" class="form-label">
                        Заменить изображение
                    </label>

                    <input
                        type="file"
                        id="image"
                        name="image"
                        class="form-control"
                        accept="image/jpeg,image/png,image/webp"
                    >

                    <div class="form-text">
                        Оставьте пустым, если изображение менять не нужно.
                    </div>
                </div>
                <div class="mb-3 form-check">
                    <input
                        type="checkbox"
                        class="form-check-input"
                        id="active"
                        name="active"
                        value="1"
                        <?= !empty($work['active']) ? 'checked' : '' ?>
                    >

                    <label class="form-check-label" for="active">
                        Активна
                    </label>
                </div>

                <div class="mb-4 form-check">
                    <input
                        type="checkbox"
                        class="form-check-input"
                        id="featured"
                        name="featured"
                        value="1"
                        <?= !empty($work['featured']) ? 'checked' : '' ?>
                    >

                    <label class="form-check-label" for="featured">
                        Избранная работа
                    </label>
                </div>
                <div class="mb-4">
                    <label for="sort" class="form-label">
                        Порядок сортировки
                    </label>

                    <input
                        type="number"
                        id="sort"
                        name="sort"
                        class="form-control"
                        value="<?= e((string) ($work['sort'] ?? 0)) ?>"
                        min="0"
                        step="1"
                    >

                    <div class="form-text">
                        Чем меньше число, тем выше работа в списке.
                    </div>
                </div>
                <div class="admin-form-actions">
                    <a href="/admin/works" class="btn btn-light">
                        Отмена
                    </a>

                    <button type="submit" class="btn btn-danger">
                        Сохранить изменения
                    </button>
                </div>

            </form>

        </section>

    </main>

</div>