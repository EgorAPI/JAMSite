<aside
        class="admin-sidebar offcanvas offcanvas-start"
        tabindex="-1"
        id="adminSidebar"
        aria-labelledby="adminSidebarLabel"
        >
        <div class="admin-sidebar__brand" id="adminSidebarLabel">
            <div>
                <div class="admin-sidebar__logo">
                    ДЖЕМ
                </div>

                <div class="admin-sidebar__label">
                    ADMIN
                </div>
            </div>

            <button
                type="button"
                class="btn-close d-lg-none"
                data-bs-dismiss="offcanvas"
                aria-label="Закрыть меню"
            ></button>
        </div>

        <nav class="admin-sidebar__nav" aria-label="Навигация админ-панели">
            <a
                href="/admin"
                class="admin-sidebar__link <?= ($_SERVER['REQUEST_URI'] ?? '') === '/admin' ? 'is-active' : '' ?>"
            >
                Главная
            </a>

            <a
                href="/admin/works"
                class="admin-sidebar__link <?= ($_SERVER['REQUEST_URI'] ?? '') === '/admin/works' ? 'is-active' : '' ?>"
            >
                Работы
            </a>

            <a
                href="/admin/categories"
                class="admin-sidebar__link <?= ($_SERVER['REQUEST_URI'] ?? '') === '/admin/categories' ? 'is-active' : '' ?>"
            >
                Категории
            </a>

            <a
                href="#"
                class="admin-sidebar__link"
            >
                Слайды
            </a>
            <div class="admin-sidebar__divider"></div>

            <a
                href="#"
                class="admin-sidebar__link"
                >
                Настройки
            </a>
        </nav>

        <div class="admin-sidebar__footer">
            <form method="post" action="/admin/logout">
                <input
                    type="hidden"
                    name="_csrf"
                    value="<?= e(csrf_token()) ?>"
                >

                <button
                    type="submit"
                    class="admin-sidebar__logout"
                >
                    Выйти
                </button>
            </form>
        </div>
    </aside>