import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const catalog = document.getElementById('header-catalog');
    const catalogToggle = document.getElementById('catalog-toggle');
    const catalogDropdown = document.getElementById('catalog-dropdown');
    const categoriesMore = document.getElementById('header-categories-more');
    const chipsNav = document.getElementById('header-chips');
    const chipsScrollBtn = document.getElementById('header-chips-scroll');
    const burger = document.getElementById('header-burger');
    const mobileMenu = document.getElementById('header-mobile-menu');
    const langButtons = document.querySelectorAll('.header-lang__btn');

    const openCatalogDropdown = () => {
        if (!catalog || !catalogToggle || !catalogDropdown) {
            return;
        }

        catalog.classList.add('is-open');
        catalogToggle.setAttribute('aria-expanded', 'true');
        catalogDropdown.hidden = false;
        catalogToggle.focus();
    };

    const closeCatalogDropdown = () => {
        if (!catalog || !catalogToggle || !catalogDropdown) {
            return;
        }

        catalog.classList.remove('is-open');
        catalogToggle.setAttribute('aria-expanded', 'false');
        catalogDropdown.hidden = true;
    };

    if (catalogToggle && catalogDropdown && catalog) {
        catalogToggle.addEventListener('click', () => {
            if (catalog.classList.contains('is-open')) {
                closeCatalogDropdown();
            } else {
                openCatalogDropdown();
            }
        });

        document.addEventListener('click', (event) => {
            if (!catalog.contains(event.target)) {
                closeCatalogDropdown();
            }
        });
    }

    categoriesMore?.addEventListener('click', openCatalogDropdown);

    chipsScrollBtn?.addEventListener('click', () => {
        if (!chipsNav) {
            return;
        }

        chipsNav.scrollBy({ left: 220, behavior: 'smooth' });
    });

    if (burger && mobileMenu) {
        burger.addEventListener('click', () => {
            const isOpen = burger.classList.toggle('is-active');
            burger.setAttribute('aria-expanded', String(isOpen));
            mobileMenu.hidden = !isOpen;
        });
    }

    langButtons.forEach((button) => {
        button.addEventListener('click', () => {
            langButtons.forEach((btn) => btn.classList.remove('is-active'));
            button.classList.add('is-active');
        });
    });
});
