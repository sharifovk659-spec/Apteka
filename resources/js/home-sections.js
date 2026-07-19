export function initCategoryStrip() {
    const strip = document.getElementById('category-strip');

    if (!strip) {
        return;
    }

    const track = strip.querySelector('.category-strip__track');
    const prevBtn = strip.querySelector('.category-strip__arrow--prev');
    const nextBtn = strip.querySelector('.category-strip__arrow--next');

    if (!track) {
        return;
    }

    const scrollStep = () => Math.max(track.clientWidth * 0.7, 220);

    prevBtn?.addEventListener('click', () => {
        track.scrollBy({ left: -scrollStep(), behavior: 'smooth' });
    });

    nextBtn?.addEventListener('click', () => {
        track.scrollBy({ left: scrollStep(), behavior: 'smooth' });
    });
}

export function initPromoSlider() {
    const slider = document.getElementById('home-promo-slider');

    if (!slider) {
        return;
    }

    const track = slider.querySelector('.promo-slider__track');
    const prevBtn = slider.querySelector('.promo-slider__arrow--prev');
    const nextBtn = slider.querySelector('.promo-slider__arrow--next');

    if (!track) {
        return;
    }

    const getStep = () => {
        const item = track.querySelector('.promo-slider__item');

        if (!item) {
            return track.clientWidth;
        }

        const styles = getComputedStyle(track);
        const gap = parseFloat(styles.columnGap || styles.gap || '16') || 16;

        return item.offsetWidth + gap;
    };

    prevBtn?.addEventListener('click', () => {
        track.scrollBy({ left: -getStep(), behavior: 'smooth' });
    });

    nextBtn?.addEventListener('click', () => {
        track.scrollBy({ left: getStep(), behavior: 'smooth' });
    });
}
