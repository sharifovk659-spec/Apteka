document.addEventListener('DOMContentLoaded', () => {
    const totals = window.checkoutTotals;

    if (!totals) {
        return;
    }

    const deliveryField = document.querySelectorAll('input[name="delivery_type"]');
    const deliveryEl = document.getElementById('checkout-delivery-price');
    const totalEl = document.getElementById('checkout-total-price');

    const formatPrice = (value) => `${new Intl.NumberFormat('ru-RU').format(value)} смн`;

    const updateTotals = () => {
        const selected = document.querySelector('input[name="delivery_type"]:checked');
        const delivery = selected?.value === 'courier' ? totals.delivery : 0;

        if (deliveryEl) {
            deliveryEl.textContent = delivery > 0 ? formatPrice(delivery) : 'Бесплатно';
        }

        if (totalEl) {
            totalEl.textContent = formatPrice(totals.subtotal + delivery);
        }
    };

    deliveryField.forEach((input) => {
        input.addEventListener('change', updateTotals);
    });

    updateTotals();
});
