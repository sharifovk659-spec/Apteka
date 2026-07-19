<footer class="site-footer">
    <div class="container site-footer__grid">
        <div class="site-footer__col">
            <a href="{{ route('home') }}" class="site-footer__logo">
                <span class="header-logo__mark">+</span>
                <span>{{ $storeName }}</span>
            </a>
            <ul class="site-footer__contacts">
                <li><a href="tel:{{ preg_replace('/\s+/', '', config('store.phone')) }}">{{ config('store.phone') }}</a></li>
                <li><a href="mailto:{{ $storeEmail }}">{{ $storeEmail }}</a></li>
                <li>г. Душанбе, пр. Рудаки, 95</li>
            </ul>
        </div>

        <div class="site-footer__col">
            <h3 class="site-footer__title">Покупателям</h3>
            <ul class="site-footer__links">
                <li><a href="#">Как сделать заказ</a></li>
                <li><a href="#">Доставка и оплата</a></li>
                <li><a href="#">E-рецепты</a></li>
                <li><a href="#">Мобильное приложение</a></li>
                <li><a href="#">Возврат товара</a></li>
            </ul>
        </div>

        <div class="site-footer__col">
            <h3 class="site-footer__title">Категории</h3>
            <ul class="site-footer__links">
                @foreach($footerCategories as $category)
                    <li><a href="{{ route('catalog.index', ['category' => $category->slug]) }}">{{ $category->name }}</a></li>
                @endforeach
            </ul>
        </div>

        <div class="site-footer__col">
            <h3 class="site-footer__title">Способы оплаты</h3>
            <div class="site-footer__payments">
                <span class="payment-badge">Наличные</span>
                <span class="payment-badge">Банковская карта</span>
                <span class="payment-badge">Онлайн</span>
            </div>
            <p class="site-footer__note">Безопасная оплата при получении или онлайн</p>
        </div>
    </div>

    <div class="site-footer__bottom">
        <div class="container site-footer__bottom-inner">
            <p class="site-footer__copy">&copy; {{ date('Y') }} {{ $storeName }}. {{ $storeTagline }}. Все права защищены.</p>
            <p class="site-footer__disclaimer">Информация на сайте не является медицинской рекомендацией.</p>
        </div>
    </div>
</footer>
