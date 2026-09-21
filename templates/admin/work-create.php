<div class="admin-shell">

    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <main class="admin-main">

        <header class="admin-topbar">

            <div>
                <p class="admin-topbar__eyebrow">
                    Портфолио
                </p>

                <h1 class="admin-topbar__title">
                    Добавить работу
                </h1>
            </div>

            <a href="/admin/works" class="admin-topbar__site-link">
                ← Назад к работам
            </a>

        </header>

        <section class="admin-work-form">

            <p class="admin-dashboard__intro">
                Создание новой работы портфолио.
            </p>

            <form
                class="admin-form-card"
                method="post"
                action="/admin/works"
                enctype="multipart/form-data"
            >
                <input
                    type="hidden"
                    name="_csrf"
                    value="<?= e(csrf_token()) ?>"
                >

                <div class="mb-4">
                    <label for="title" class="form-label">
                        Название работы
                    </label>

                    <div class="mb-4">
                        <label for="category" class="form-label">
                            Категория
                        </label>

                        <select
                            id="category"
                            name="category"
                            class="form-select"
                            >
                            <option value="">
                                Выберите категорию
                            </option>

                            <?php foreach ($categories as $category): ?>
                                <option value="<?= e($category['id']) ?>">
                                    <?= e($category['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="image" class="form-label">
                            Изображение
                        </label>

                        <input
                            type="file"
                            id="image"
                            name="image"
                            class="form-control"
                            accept="image/jpeg,image/png,image/webp"
                            required
                        >

                        <div class="form-text">
                            JPG, PNG или WebP.
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input
                            type="checkbox"
                            class="form-check-input"
                            id="active"
                            name="active"
                            value="1"
                            checked
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
                            value="0"
                            min="0"
                            step="1"
                        >

                        <div class="form-text">
                            Чем меньше число, тем выше работа в списке.
                        </div>
                    </div>

                    <input
                        type="text"
                        id="title"
                        name="title"
                        class="form-control"
                        placeholder="Например: Вывеска для магазина"
                    >
                </div>

                <div class="admin-form-actions">
                    <a href="/admin/works" class="btn btn-light">
                        Отмена
                    </a>

                    <button type="submit" class="btn btn-danger">
                        Сохранить работу
                    </button>
                </div>
            </form>

        </section>

    </main>

</div>