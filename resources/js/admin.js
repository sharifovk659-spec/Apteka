document.addEventListener('DOMContentLoaded', () => {
    const body = document.getElementById('admin-body');
    const sidebar = document.getElementById('admin-sidebar');
    const collapseBtn = document.getElementById('admin-sidebar-collapse');
    const mobileToggle = document.getElementById('admin-mobile-toggle');
    const overlay = document.getElementById('admin-overlay');
    const profile = document.getElementById('admin-profile');
    const profileToggle = document.getElementById('admin-profile-toggle');
    const profileMenu = document.getElementById('admin-profile-menu');

    const applySidebarState = (collapsed) => {
        document.documentElement.classList.toggle('admin-sidebar-collapsed', collapsed);
        localStorage.setItem('sabth-admin-sidebar', collapsed ? 'collapsed' : 'expanded');
    };

    if (localStorage.getItem('sabth-admin-sidebar') === 'collapsed') {
        document.documentElement.classList.add('admin-sidebar-collapsed');
    }

    collapseBtn?.addEventListener('click', () => {
        const collapsed = !document.documentElement.classList.contains('admin-sidebar-collapsed');
        applySidebarState(collapsed);
    });

    const closeMobileSidebar = () => {
        body?.classList.remove('admin-mobile-open');
        overlay?.setAttribute('hidden', 'hidden');
    };

    const openMobileSidebar = () => {
        body?.classList.add('admin-mobile-open');
        overlay?.removeAttribute('hidden');
    };

    mobileToggle?.addEventListener('click', () => {
        if (body?.classList.contains('admin-mobile-open')) {
            closeMobileSidebar();
        } else {
            openMobileSidebar();
        }
    });

    overlay?.addEventListener('click', closeMobileSidebar);

    profileToggle?.addEventListener('click', () => {
        const isOpen = profile?.classList.toggle('is-open');
        profileToggle.setAttribute('aria-expanded', String(isOpen));
        if (profileMenu) {
            profileMenu.hidden = !isOpen;
        }
    });

    document.addEventListener('click', (event) => {
        if (profile && !profile.contains(event.target)) {
            profile.classList.remove('is-open');
            profileToggle?.setAttribute('aria-expanded', 'false');
            if (profileMenu) {
                profileMenu.hidden = true;
            }
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 992) {
            closeMobileSidebar();
        }
    });

    initDeleteModal();
});

function initDeleteModal() {
    const modal = document.getElementById('delete-modal');
    const confirmBtn = document.getElementById('delete-modal-confirm');
    const nameEl = document.getElementById('delete-modal-name');
    let pendingForm = null;

    if (!modal || !confirmBtn) {
        return;
    }

    const closeModal = () => {
        modal.hidden = true;
        modal.setAttribute('hidden', 'hidden');
        document.body.style.overflow = '';
        document.body.classList.remove('admin-modal-open');
        pendingForm = null;
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Удалить';
    };

    const openModal = (button) => {
        const formId = button.getAttribute('data-delete-form');
        pendingForm = formId ? document.getElementById(formId) : null;

        if (!pendingForm) {
            return;
        }

        if (nameEl) {
            nameEl.textContent = button.getAttribute('data-delete-name') || '';
        }

        modal.hidden = false;
        modal.removeAttribute('hidden');
        document.body.style.overflow = 'hidden';
        document.body.classList.add('admin-modal-open');
        confirmBtn.focus();
    };

    document.querySelectorAll('[data-delete-open]').forEach((button) => {
        button.addEventListener('click', () => openModal(button));
    });

    confirmBtn.addEventListener('click', () => {
        if (!pendingForm) {
            closeModal();
            return;
        }

        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Удаление…';
        pendingForm.submit();
    });

    modal.querySelectorAll('[data-modal-close]').forEach((element) => {
        element.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.hidden) {
            closeModal();
        }
    });
}
