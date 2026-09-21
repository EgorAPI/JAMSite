<div class="admin-shell">

    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <main class="admin-main">
        <header class="admin-topbar">
            <button
                class="admin-menu-toggle d-lg-none"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#adminSidebar"
                aria-controls="adminSidebar"
                aria-label="Открыть меню"
            >
                <span></span>
                <span></span>
                <span></span>
            </button>

            <div>
                <p class="admin-topbar__eyebrow">
                    Администрирование
                </p>

                <h1 class="admin-topbar__title">
                    Панель управления
                </h1>
            </div>
            <a
                href="/"
                class="admin-topbar__site-link"
                target="_blank"
                rel="noopener"
                >
                Открыть сайт ↗
            </a>
        </header>

        <section class="admin-dashboard">
            <p class="admin-dashboard__intro">
                Управляйте основными разделами сайта рекламного агентства «Джем».
            </p>

            <div class="admin-dashboard__grid">

                <a href="#" class="admin-dashboard-card">
                    <div class="admin-dashboard-card__meta">
                        Раздел
                    </div>

                    <h2 class="admin-dashboard-card__title">
                        Работы
                    </h2>

                    <p class="admin-dashboard-card__text">
                        Управление портфолио и избранными работами.
                    </p>

                    <div class="admin-dashboard-card__action">
                        Открыть раздел →
                    </div>
                </a>

                <a href="#" class="admin-dashboard-card">
                    <div class="admin-dashboard-card__meta">
                        Раздел
                    </div>

                    <h2 class="admin-dashboard-card__title">
                        Категории
                    </h2>

                    <p class="admin-dashboard-card__text">
                        Управление категориями портфолио.
                    </p>

                    <div class="admin-dashboard-card__action">
                        Открыть раздел →
                    </div>
                </a>

                <a href="#" class="admin-dashboard-card">
                    <div class="admin-dashboard-card__meta">
                        Раздел
                    </div>

                    <h2 class="admin-dashboard-card__title">
                        Слайды
                    </h2>

                    <p class="admin-dashboard-card__text">
                        Управление слайдами главной страницы.
                    </p>

                    <div class="admin-dashboard-card__action">
                        Открыть раздел →
                    </div>
                </a>

            </div>
        </section>
    </main>

</div>