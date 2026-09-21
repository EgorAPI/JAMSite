'use strict';
const header = document.querySelector('.site-header');

if (header) {
    const updateHeader = () => {
        header.classList.toggle('is-scrolled', window.scrollY > 24);
    };

    updateHeader();

    window.addEventListener('scroll', updateHeader, {
        passive: true
    });
}
const worksSlider = document.querySelector('.works-slider');

if (worksSlider) {
    const track = worksSlider.querySelector('.works-slider__track');
    const prevButton = worksSlider.querySelector('.works-slider__button--prev');
    const nextButton = worksSlider.querySelector('.works-slider__button--next');

    let autoScroll;

    const getScrollAmount = () => {
        const slide = track.querySelector('.works-slide');

        if (!slide) {
            return 0;
        }

        const gap = parseFloat(getComputedStyle(track).gap) || 0;

        return slide.getBoundingClientRect().width + gap;
    };

    const next = () => {
        const maxScroll = track.scrollWidth - track.clientWidth;

        if (track.scrollLeft >= maxScroll - 10) {
            track.scrollTo({
                left: 0,
                behavior: 'smooth'
            });
        } else {
            track.scrollBy({
                left: getScrollAmount(),
                behavior: 'smooth'
            });
        }
    };

    const startAutoScroll = () => {
        clearInterval(autoScroll);

        autoScroll = setInterval(next, 3000);
    };

    nextButton?.addEventListener('click', () => {
        next();
        startAutoScroll();
    });

    prevButton?.addEventListener('click', () => {
        track.scrollBy({
            left: -getScrollAmount(),
            behavior: 'smooth'
        });

        startAutoScroll();
    });

    worksSlider.addEventListener('mouseenter', () => {
        clearInterval(autoScroll);
    });

    worksSlider.addEventListener('mouseleave', () => {
        startAutoScroll();
    });

    startAutoScroll();
}


const portfolioFilters = document.querySelectorAll('.portfolio-filter');
const portfolioCards = document.querySelectorAll('.portfolio-card');

if (portfolioFilters.length && portfolioCards.length) {
    portfolioFilters.forEach((filterButton) => {
        filterButton.addEventListener('click', () => {
            const filterValue = filterButton.dataset.filter;

            portfolioFilters.forEach((button) => {
                button.classList.remove('is-active');
            });

            filterButton.classList.add('is-active');

            portfolioCards.forEach((card) => {
                const cardCategory = card.dataset.category;
                const shouldShow =
                    filterValue === 'all' ||
                    cardCategory === filterValue;

                card.classList.toggle('is-hidden', !shouldShow);
            });
        });
    });
}
const portfolioLightboxElement = document.getElementById('portfolioLightbox');

if (portfolioLightboxElement) {
    const lightboxImage = document.getElementById('portfolioLightboxImage');
    const lightboxTitle = document.getElementById('portfolioLightboxTitle');
    const lightboxCategory = document.getElementById('portfolioLightboxCategory');

    const portfolioLightbox = new bootstrap.Modal(portfolioLightboxElement);

    document.querySelectorAll('[data-lightbox]').forEach((card) => {
        card.addEventListener('click', () => {
            lightboxImage.src = card.dataset.image;
            lightboxImage.alt = card.dataset.title || '';

            lightboxTitle.textContent = card.dataset.title || '';
            lightboxCategory.textContent = card.dataset.categoryTitle || '';

            portfolioLightbox.show();
        });
    });
}

const mapElement = document.getElementById('contact-map');

if (mapElement && window.L) {
   const map = L.map(mapElement);
   map.attributionControl.setPrefix(false);

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }
    ).addTo(map);

    const jamMarkerIcon = L.divIcon({
    className: 'jam-map-marker',
    html: '<span></span>',
    iconSize: [28, 28],
    iconAnchor: [14, 28],
    popupAnchor: [0, -24]
});

L.marker(
    [53.722020, 91.447503],
    { icon: jamMarkerIcon }
)
    .addTo(map)
    .bindPopup('<strong>Офис «Джем»</strong>');

L.marker(
    [53.706847, 91.371890],
    { icon: jamMarkerIcon }
)
    .addTo(map)
    .bindPopup('<strong>Производственный цех</strong>');

const bounds = L.latLngBounds(
    [53.722020, 91.447503],
    [53.706847, 91.371890]
);

map.fitBounds(bounds, {
    padding: [40, 40]
});
map.zoomOut(1);
}