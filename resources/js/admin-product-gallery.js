document.addEventListener('DOMContentLoaded', () => {
    const gallery = document.getElementById('admin-product-gallery');
    const uploadInput = document.getElementById('admin-gallery-upload');
    const list = document.getElementById('admin-gallery-list');
    const newContainer = document.getElementById('admin-gallery-new');

    if (!gallery || !uploadInput || !list) {
        return;
    }

    const maxImages = Number(gallery.dataset.maxImages || 10);
    let newFileIndex = 0;

    const countImages = () => {
        const existing = list.querySelectorAll('.admin-gallery__item:not(.is-marked-delete)').length;
        const pending = newContainer.querySelectorAll('.admin-gallery__item').length;

        return existing + pending;
    };

    const refreshOrderInputs = () => {
        list.querySelectorAll('.admin-gallery__item').forEach((item) => {
            const id = item.dataset.imageId;
            const orderInput = item.querySelector('input[name="image_order[]"]');

            if (orderInput && id) {
                orderInput.value = id;
            }
        });
    };

    list.querySelectorAll('input[name="delete_image_ids[]"]').forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            checkbox.closest('.admin-gallery__item')?.classList.toggle('is-marked-delete', checkbox.checked);
        });
    });

    uploadInput.addEventListener('change', () => {
        const files = Array.from(uploadInput.files || []);

        files.forEach((file) => {
            if (countImages() >= maxImages) {
                return;
            }

            const index = newFileIndex++;
            const item = document.createElement('div');
            item.className = 'admin-gallery__item admin-gallery__item--new';
            item.draggable = true;

            const previewUrl = URL.createObjectURL(file);
            item.innerHTML = `
                <img src="${previewUrl}" alt="" width="120" height="120">
                <div class="admin-gallery__item-actions">
                    <span class="admin-gallery__badge">Новое</span>
                    <button type="button" class="admin-link admin-link--danger" data-remove-new>Убрать</button>
                </div>
            `;

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'file';
            hiddenInput.name = `gallery_images[${index}]`;
            hiddenInput.hidden = true;

            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            hiddenInput.files = dataTransfer.files;

            item.appendChild(hiddenInput);
            newContainer.appendChild(item);

            item.querySelector('[data-remove-new]')?.addEventListener('click', () => {
                URL.revokeObjectURL(previewUrl);
                item.remove();
            });
        });

        uploadInput.value = '';
    });

    let draggedItem = null;

    list.addEventListener('dragstart', (event) => {
        const item = event.target.closest('.admin-gallery__item');

        if (!item || item.classList.contains('is-marked-delete')) {
            return;
        }

        draggedItem = item;
        item.classList.add('is-dragging');
    });

    list.addEventListener('dragend', () => {
        draggedItem?.classList.remove('is-dragging');
        draggedItem = null;
        refreshOrderInputs();
    });

    list.addEventListener('dragover', (event) => {
        event.preventDefault();
        const target = event.target.closest('.admin-gallery__item');

        if (!target || !draggedItem || target === draggedItem || target.classList.contains('is-marked-delete')) {
            return;
        }

        const rect = target.getBoundingClientRect();
        const after = event.clientX > rect.left + rect.width / 2;

        if (after) {
            target.after(draggedItem);
        } else {
            target.before(draggedItem);
        }
    });
});
