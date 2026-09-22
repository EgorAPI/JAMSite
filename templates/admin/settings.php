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
                    Сайт
                </p>

                <h1 class="admin-topbar__title">
                    Настройки
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

        <section class="admin-settings">

            <p class="admin-dashboard__intro">
                Основные контактные данные рекламного агентства.
            </p>

            <form
                method="post"
                action="/admin/settings"
                class="admin-form-card"
            >
                <input
                    type="hidden"
                    name="_csrf"
                    value="<?= e(csrf_token()) ?>"
                >

                <div class="mb-4">
                    <label for="company_name" class="form-label">
                        Название компании
                    </label>

                    <input
                        type="text"
                        id="company_name"
                        name="company_name"
                        class="form-control"
                        value="<?= e($settings['company_name'] ?? '') ?>"
                    >
                </div>

                <div class="mb-4">
                    <label for="phone_1" class="form-label">
                        Телефон 1
                    </label>

                    <input
                        type="text"
                        id="phone_1"
                        name="phone_1"
                        class="form-control"
                        value="<?= e($settings['phone_1'] ?? '') ?>"
                    >
                </div>

                <div class="mb-4">
                    <label for="phone_2" class="form-label">
                        Телефон 2
                    </label>

                    <input
                        type="text"
                        id="phone_2"
                        name="phone_2"
                        class="form-control"
                        value="<?= e($settings['phone_2'] ?? '') ?>"
                    >
                </div>

                <div class="mb-4">
                    <label for="email" class="form-label">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="<?= e($settings['email'] ?? '') ?>"
                    >
                </div>

                <div class="mb-4">
                    <label for="office_address" class="form-label">
                        Адрес офиса
                    </label>

                    <input
                        type="text"
                        id="office_address"
                        name="office_address"
                        class="form-control"
                        value="<?= e($settings['office_address'] ?? '') ?>"
                    >
                </div>

                <div class="mb-4">
                    <label for="workshop_address" class="form-label">
                        Адрес цеха
                    </label>

                    <input
                        type="text"
                        id="workshop_address"
                        name="workshop_address"
                        class="form-control"
                        value="<?= e($settings['workshop_address'] ?? '') ?>"
                    >
                </div>

                <div class="mb-4">
                    <label for="working_hours" class="form-label">
                        Режим работы
                    </label>

                    <input
                        type="text"
                        id="working_hours"
                        name="working_hours"
                        class="form-control"
                        value="<?= e($settings['working_hours'] ?? '') ?>"
                    >
                </div>

                <div class="admin-form-actions">
                    <button type="submit" class="btn btn-danger">
                        Сохранить настройки
                    </button>
                </div>

            </form>

        </section>

    </main>

</div>