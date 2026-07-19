export function initCatalogPage() {
    const catalogPage = document.getElementById('catalog-page');
    if (!catalogPage) {
        return;
    }

    const drawer = document.getElementById('catalog-drawer');
    const drawerOpen = document.getElementById('catalog-filters-open');
    const drawerClose = document.getElementById('catalog-drawer-close');
    const drawerOverlay = document.getElementById('catalog-drawer-overlay');
    const sortSelect = document.getElementById('catalog-sort');
    const sortForm = document.getElementById('catalog-sort-form');
    const filterForms = catalogPage.querySelectorAll('.catalog-filters');

    const openDrawer = () => {
        drawer.hidden = false;
        requestAnimationFrame(() => drawer.classList.add('is-open'));
        document.body.style.overflow = 'hidden';
    };

    const closeDrawer = () => {
        drawer.classList.remove('is-open');
        document.body.style.overflow = '';
        window.setTimeout(() => {
            drawer.hidden = true;
        }, 200);
    };

    drawerOpen?.addEventListener('click', openDrawer);
    drawerClose?.addEventListener('click', closeDrawer);
    drawerOverlay?.addEventListener('click', closeDrawer);

    sortSelect?.addEventListener('change', () => {
        catalogPage.classList.add('is-loading');
        sortForm?.submit();
    });

    filterForms.forEach((form) => {
        form.addEventListener('submit', () => {
            catalogPage.classList.add('is-loading');
            closeDrawer();
        });
    });
}
