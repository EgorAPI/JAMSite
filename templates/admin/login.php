<div class="admin-login">
    <div class="admin-login__card">

        <div class="admin-login__brand">
            <div class="admin-login__logo">
                ДЖЕМ
            </div>

            <div class="admin-login__subtitle">
                Панель администратора
            </div>
        </div>

        <h1 class="admin-login__title">
            Вход в админ-панель
        </h1>

        <?php if (!empty($error)): ?>
            <div
                class="alert alert-danger admin-login__alert"
                role="alert"
            >
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="/admin/login">

            <input
                type="hidden"
                name="_csrf"
                value="<?= e(csrf_token()) ?>"
            >

            <div class="mb-3">
                <label
                    for="username"
                    class="form-label"
                >
                    Логин
                </label>

                <input
                    type="text"
                    class="form-control"
                    id="username"
                    name="username"
                    autocomplete="username"
                    required
                >
            </div>

            <div class="mb-4">
                <label
                    for="password"
                    class="form-label"
                >
                    Пароль
                </label>

                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button
                type="submit"
                class="btn btn-danger w-100 admin-login__submit"
            >
                Войти
            </button>

        </form>

    </div>
</div>