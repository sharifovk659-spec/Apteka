export function initHomeBannerSlider() {
    const slider = document.getElementById('home-banner-slider');

    if (!slider) {
        return;
    }

    const slides = Array.from(slider.querySelectorAll('.banner-slider__slide'));
    const dots = Array.from(slider.querySelectorAll('.banner-slider__dot'));
    const prevBtn = slider.querySelector('.banner-slider__arrow--prev');
    const nextBtn = slider.querySelector('.banner-slider__arrow--next');

    if (slides.length <= 1) {
        return;
    }

    let current = 0;

    const showSlide = (index) => {
        current = (index + slides.length) % slides.length;

        slides.forEach((slide, slideIndex) => {
            slide.classList.toggle('is-active', slideIndex === current);
        });

        dots.forEach((dot, dotIndex) => {
            const isActive = dotIndex === current;
            dot.classList.toggle('is-active', isActive);
            dot.setAttribute('aria-current', isActive ? 'true' : 'false');
        });
    };

    prevBtn?.addEventListener('click', () => showSlide(current - 1));
    nextBtn?.addEventListener('click', () => showSlide(current + 1));

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            const target = Number(dot.dataset.slideTo);
            if (!Number.isNaN(target)) {
                showSlide(target);
            }
        });
    });
}
