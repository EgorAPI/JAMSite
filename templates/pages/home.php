
<section class="hero" aria-labelledby="hero-title">
    <div class="container">
        <div class="row align-items-center g-5 hero__row">

            <div class="col-lg-5">
                <div class="hero__content">

                    <p class="hero__eyebrow">
                        Рекламное агентство <span aria-hidden="true">•</span> Абакан
                    </p>

                    <h1 id="hero-title" class="hero__title">
                        Наружная реклама в Абакане
                    </h1>

                    <p class="hero__text">
                        От идеи и разработки макета до изготовления и монтажа.
                    </p>

                    <div class="hero__actions">
                        <a href="/portfolio" class="btn-jam-primary">
                            Посмотреть работы
                        </a>

                        <a href="#contact" class="hero__link">
                            Обсудить проект
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>

                </div>
            </div>

            <div class="col-lg-7">
    <div class="hero-media">

        <div
            id="heroCarousel"
            class="carousel slide"
            data-bs-ride="carousel"
            data-bs-interval="6000"
            data-bs-pause="hover"
        >
            <div class="carousel-inner">

    <?php foreach ($slides as $index => $slide): ?>
        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
            <div class="hero-media__image">
                <img
                    src="<?= htmlspecialchars($slide['image'], ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($slide['title'], ENT_QUOTES, 'UTF-8') ?>"
                >
            </div>
        </div>
    <?php endforeach; ?>

</div>

            <div class="hero-media__controls">

            <div class="hero-carousel-indicators">
    <?php foreach ($slides as $index => $slide): ?>
        <button
            type="button"
            data-bs-target="#heroCarousel"
            data-bs-slide-to="<?= $index ?>"
            class="<?= $index === 0 ? 'active' : '' ?>"
            <?= $index === 0 ? 'aria-current="true"' : '' ?>
            aria-label="Слайд <?= $index + 1 ?>"
        ></button>
    <?php endforeach; ?>
</div>

                <div class="hero-carousel-arrows">
                    <button
                        type="button"
                        data-bs-target="#heroCarousel"
                        data-bs-slide="prev"
                        aria-label="Предыдущий слайд"
                    >
                        ←
                    </button>

                    <button
                        type="button"
                        data-bs-target="#heroCarousel"
                        data-bs-slide="next"
                        aria-label="Следующий слайд"
                    >
                        →
                    </button>
                </div>

            </div>

        </div>

    </div>
</div>

        </div>
    </div>
</section>


<section id="services" class="py-5" aria-labelledby="services-title">
    <div class="container">

        <div class="row mb-4">
            <div class="col-lg-8">

                <h2 id="services-title" class="display-6 fw-bold">
                    Наши услуги
                </h2>
            </div>
        </div>

        <div class="row g-4">

            <div class="col-md-6 col-lg-4">
                <article class="h-100">
                    <div class="ratio ratio-4x3 mb-3">
                        <img
                            src="/assets/images/services/service-banners.webp"
                            alt="Печать и монтаж рекламных баннеров"
                            class="service-image"
                            loading="lazy"
                        >
                    </div>

                    <h3 class="h4">
                        Баннеры
                    </h3>

                    <p class="mb-0">
                        Печать и монтаж рекламных баннеров.
                    </p>
                </article>
            </div>


            <div class="col-md-6 col-lg-4">
                <article class="h-100">
                    <div class="ratio ratio-4x3 mb-3">
                        <img
                            src="/assets/images/services/service-signs.webp"
                            alt="Изготовление рекламных вывесок"
                            class="service-image"
                            loading="lazy"
                        >
                    </div>

                    <h3 class="h4">
                        Вывески
                    </h3>

                    <p class="mb-0">
                        Обычные и световые рекламные вывески.
                    </p>
                </article>
            </div>


            <div class="col-md-6 col-lg-4">
                <article class="h-100">
                    <div class="ratio ratio-4x3 mb-3">
                        <img
                            src="/assets/images/services/service-cars.webp"
                            alt="Брендирование автомобилей"
                            class="service-image"
                            loading="lazy"
                        >
                    </div>

                    <h3 class="h4">
                        Брендирование авто
                    </h3>

                    <p class="mb-0">
                        Рекламное оформление автомобилей.
                    </p>
                </article>
            </div>


            <div class="col-md-6 col-lg-4">
                <article class="h-100">
                    <div class="ratio ratio-4x3 mb-3">
                        <img
                            src="/assets/images/services/service-plates.webp"
                            alt="Изготовление рекламных и информационных табличек"
                            class="service-image"
                            loading="lazy"
                        >
                    </div>

                    <h3 class="h4">
                        Таблички
                    </h3>

                    <p class="mb-0">
                        Информационные и рекламные таблички.
                    </p>
                </article>
            </div>


            <div class="col-md-6 col-lg-4">
                <article class="h-100">
                    <div class="ratio ratio-4x3 mb-3">
                        <img
                            src="/assets/images/services/service-stands.webp"
                            alt="Изготовление рекламных и информационных стендов"
                            class="service-image"
                            loading="lazy"
                        >
                    </div>

                    <h3 class="h4">
                        Стенды
                    </h3>

                    <p class="mb-0">
                        Изготовление информационных и рекламных стендов.
                    </p>
                </article>
            </div>


            <div class="col-md-6 col-lg-4">
                <article class="h-100">
                    <div class="ratio ratio-4x3 mb-3">
                        <img
                            src="/assets/images/services/service-print.webp"
                            alt="Печать на одежде"
                            class="service-image"
                            loading="lazy"
                        >
                    </div>

                    <h3 class="h4">
                        Печать на одежде
                    </h3>

                    <p class="mb-0">
                        Печать на одежде и другой рекламной продукции.
                    </p>
                </article>
            </div>

        </div>
    </div>
</section>


<section id="about" class="py-5 bg-light" aria-labelledby="about-title">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                          <div class="about-media">
                    <img
                        src="/assets/images/about/about-company.webp"
                        alt="Рекламное агентство «Джем»"
                        class="about-image"
                        loading="lazy"
                    >
                </div>
            </div>

            <div class="col-lg-6">
                <p class="text-uppercase mb-2">
                    О компании
                </p>

                <h2 id="about-title" class="display-6 fw-bold">
                    Рекламное агентство «Джем»
                </h2>

                <p class="lead">
                    Производим наружную рекламу в Абакане:
                    от разработки макета до изготовления и монтажа.
                </p>

                <p class="mb-0">
                    Рекламное агентство «Джем» – одно из крупнейших агентств на рынке наружной рекламы. 
                    Это большое количество рекламных поверхностей в Республике Хакасия  и юге Красноярского края.
                    Для изготовления наружной рекламы компания владеет собственной производственной базой и всем необходимым оборудованием.
                </p>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($featuredPortfolioItems)): ?>
<section id="works" class="py-5" aria-labelledby="works-title">
    <div class="container">

        <div class="row align-items-end mb-4">
            <div class="col-md">
                <p class="text-uppercase mb-2">
                    Портфолио
                </p>

                <h2 id="works-title" class="display-6 fw-bold mb-0">
                    Избранные работы
                </h2>
            </div>

            <div class="col-md-auto mt-3 mt-md-0">
                <a href="/portfolio" class="btn btn-outline-dark">
                    Все работы
                </a>
            </div>
        </div>
        <div class="works-slider">
            <div class="works-slider__track">


<?php foreach ($featuredPortfolioItems as $item): ?>

    <article class="works-slide">
        <a href="/portfolio" class="works-slide__link">
            <div class="works-slide__image">
                <img
                    src="<?= htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>"
                    loading="lazy"
                >
            </div>

            <div class="works-slide__content">
                <h3 class="works-slide__title">
                    <?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>
                </h3>

                <p class="works-slide__category">
                    <?= htmlspecialchars($item['category_title'], ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>
        </a>
    </article>

<?php endforeach; ?>

                

            </div>

            <div class="works-slider__controls">
                <button
                    class="works-slider__button works-slider__button--prev"
                    type="button"
                    aria-label="Предыдущие работы"
                >
                    ←
                </button>

                <button
                    class="works-slider__button works-slider__button--next"
                    type="button"
                    aria-label="Следующие работы"
                >
                    →
                </button>
            </div>
        </div>

    </div>
</section>
<?php endif; ?>


<section id="contact" class="py-5 bg-dark text-white" aria-labelledby="cta-title">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 id="cta-title" class="display-6 fw-bold">
                    Обсудим ваш проект?
                </h2>

                <p class="lead mb-4">
                    Расскажите, что нужно изготовить, и мы свяжемся с вами.
                </p>
            </div>
        </div>
    </div>
</section>


<section id="contact-form" class="py-5" aria-labelledby="contacts-title">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <p class="text-uppercase mb-2">
                    Связаться с нами
                </p>

                <h2 id="contacts-title" class="display-6 fw-bold">
                    Контакты
                </h2>

                <address class="mt-4">
                    <p>
                        <strong>Рекламное агентство «Джем»</strong><br>
                        г. Абакан
                    </p>

                    <p>
                        Адрес будет добавлен позже
                    </p>

                    <p>
                        <a href="tel:+7XXXXXXXXXX">
                            +7 XXX XXX-XX-XX
                        </a>
                    </p>

                    <p>
                        <a href="mailto:example@example.ru">
                            example@example.ru
                        </a>
                    </p>
                </address>
            </div>

            <div class="col-lg-7">
                <h3 class="h4 mb-4">
                    Оставить заявку
                </h3>
                <?php if (!empty($_SESSION['contact_errors'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            <?php foreach ($_SESSION['contact_errors'] as $error): ?>
                                <li><?= e($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <?php unset($_SESSION['contact_errors']); ?>
                <?php endif; ?>

                <?php if (!empty($_SESSION['contact_success'])): ?>
                    <div class="alert alert-success" role="alert">
                        <?= e($_SESSION['contact_success']) ?>
                    </div>

                    <?php unset($_SESSION['contact_success']); ?>
                <?php endif; ?>

                <?php
                    $contactOld = $_SESSION['contact_old'] ?? [];
                    unset($_SESSION['contact_old']);
                ?>

                <form action="/contact-request" method="post">
                    <input
                        type="hidden"
                        name="_csrf"
                        value="<?= e(csrf_token()) ?>"
                    >
                    <div class="d-none" aria-hidden="true">
                        <label for="website">Website</label>
                        <input
                            type="text"
                            id="website"
                            name="website"
                            tabindex="-1"
                            autocomplete="off"
                        >
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">
                                Ваше имя
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="name"
                                name="name"
                                value="<?= e((string) ($contactOld['name'] ?? '')) ?>"
                                autocomplete="name"
                            >
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">
                                Телефон
                            </label>

                            <input
                                type="tel"
                                class="form-control"
                                id="phone"
                                name="phone"
                                value="<?= e((string) ($contactOld['phone'] ?? '')) ?>"
                                autocomplete="tel"
                            >
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">
                                Email
                            </label>

                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                value="<?= e((string) ($contactOld['email'] ?? '')) ?>"
                                autocomplete="email"
                            >
                        </div>
                        <div class="col-12">
                            <label for="message" class="form-label">
                                Что нужно сделать?
                            </label>

                            <textarea
                                class="form-control"
                                id="message"
                                name="message"
                                rows="5"
                            ><?= e((string) ($contactOld['message'] ?? '')) ?></textarea>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="privacy"
                                    name="privacy"
                                    <?= !empty($contactOld['privacy']) ? 'checked' : '' ?>
                                >

                                <label class="form-check-label" for="privacy">
                                    Я согласен с обработкой персональных данных и принимаю
                                    <a href="/privacy">политику конфиденциальности</a>
                                </label>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Отправить заявку
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <div
                    id="contact-map"
                    class="contact-map"
                    aria-label="Карта с офисом и производством рекламного агентства «Джем»"
                ></div>
            </div>
        </div>
    </div>
</section>
