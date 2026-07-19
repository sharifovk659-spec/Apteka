document.addEventListener('DOMContentLoaded', () => {
    const mainImage = document.getElementById('product-main-image');
    const thumbs = document.querySelectorAll('.product-page__thumb');

    if (!mainImage || thumbs.length === 0) {
        return;
    }

    thumbs.forEach((thumb) => {
        thumb.addEventListener('click', () => {
            const url = thumb.dataset.imageUrl;
            const alt = thumb.dataset.imageAlt || '';

            if (!url) {
                return;
            }

            mainImage.src = url;
            mainImage.alt = alt;

            thumbs.forEach((item) => item.classList.remove('is-active'));
            thumb.classList.add('is-active');
        });
    });
});
