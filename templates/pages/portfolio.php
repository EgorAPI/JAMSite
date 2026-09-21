<section class="portfolio-page" aria-labelledby="portfolio-title">
    <div class="container">

        <header class="portfolio-header">
            <p class="portfolio-eyebrow">
                Портфолио
            </p>

            <h1 id="portfolio-title" class="portfolio-title">
                Наши работы
            </h1>

            <p class="portfolio-intro">
                Вывески, баннеры, оформление автомобилей
                и другие проекты рекламного агентства «Джем».
            </p>
        </header>

        <div class="portfolio-filters" aria-label="Фильтр работ">

    <button
        class="portfolio-filter is-active"
        type="button"
        data-filter="all"
    >
        Все
    </button>

    <?php foreach ($categories as $category): ?>
        <button
            class="portfolio-filter"
            type="button"
            data-filter="<?= htmlspecialchars($category['id'], ENT_QUOTES, 'UTF-8') ?>"
        >
            <?= htmlspecialchars($category['title'], ENT_QUOTES, 'UTF-8') ?>
        </button>
    <?php endforeach; ?>

</div>
        <div class="portfolio-grid">
          <?php foreach ($portfolioItems as $item): ?>

    <article
        class="portfolio-card"
        data-category="<?= htmlspecialchars($item['category'], ENT_QUOTES, 'UTF-8') ?>"
    >
        <button
            class="portfolio-card__button"
            type="button"
            data-lightbox
            data-image="<?= htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8') ?>"
            data-title="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>"
            data-category-title="<?= htmlspecialchars($item['category_title'], ENT_QUOTES, 'UTF-8') ?>"
        >
            <div class="portfolio-card__media">
                <img
                    src="<?= htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>"
                    loading="lazy"
                >
            </div>

            <div class="portfolio-card__content">
                <h2 class="portfolio-card__title">
                    <?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>
                    <span aria-hidden="true">↗</span>
                </h2>

                <p class="portfolio-card__category">
                    <?= htmlspecialchars($item['category_title'], ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>
        </button>
    </article>

<?php endforeach; ?>

            

        </div>

    </div>
</section>
<div
    class="modal fade portfolio-lightbox"
    id="portfolioLightbox"
    tabindex="-1"
    aria-labelledby="portfolioLightboxTitle"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">

            <button
                type="button"
                class="btn-close portfolio-lightbox__close"
                data-bs-dismiss="modal"
                aria-label="Закрыть"
            ></button>

            <div class="modal-body">
                <div class="portfolio-lightbox__media">
                    <img
                        src=""
                        alt=""
                        id="portfolioLightboxImage"
                        class="portfolio-lightbox__image"
                    >
                </div>

                <div class="portfolio-lightbox__info">
                    <h2
                        id="portfolioLightboxTitle"
                        class="portfolio-lightbox__title"
                    ></h2>

                    <p
                        id="portfolioLightboxCategory"
                        class="portfolio-lightbox__category"
                    ></p>
                </div>
            </div>

        </div>
    </div>
</div>
