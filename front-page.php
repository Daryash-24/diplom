<?php get_header(); ?>

<!-- Hero-блок на всю ширину -->
<section class="hero-full">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>LIEDA — здоровье и процветание</h1>
        <p>Традиционная китайская медицина в современных продуктах</p>
        <a href="/catalog" class="btn-primary">Перейти в каталог</a>
    </div>
</section>

<!-- Хиты продаж (вывод реальных товаров) -->
<section class="hits">
    <div class="container">
        <h2>Хиты продаж</h2>
        <div class="products-grid">
            <?php
            $hits = new WP_Query( array(
                'post_type'      => 'product',
                'posts_per_page' => 3,
                'orderby'        => 'rand', // или 'date', 'meta_value_num' по продажам
            ) );
            if ( $hits->have_posts() ) :
                while ( $hits->have_posts() ) : $hits->the_post();
                    $price = get_post_meta( get_the_ID(), '_product_price', true );
                    ?>
                    <div class="product-card">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="product-image">
                                <?php the_post_thumbnail( 'medium' ); ?>
                            </div>
                        <?php endif; ?>
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <div class="product-price"><?php echo $price ? $price . ' руб.' : 'Цена не указана'; ?></div>
                        <button class="buy-button" 
                            data-id="<?php the_ID(); ?>" 
                            data-title="<?php echo esc_attr( get_the_title() ); ?>" 
                            data-price="<?php echo esc_attr( $price ); ?>"
                            data-thumb="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ); ?>">Купить</button>
                    </div>
                <?php endwhile;
                wp_reset_postdata();
            else : ?>
                <p>Товары временно отсутствуют.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Индивидуальный заказ-->
<section class="custom-order">
    <div class="container">
        <div class="custom-order-content">
            <h2>Не нашли нужный продукт?</h2>
            <p>Оформите индивидуальный заказ — подберём состав, дозировку и упаковку под ваши задачи.</p>
            <a href="tel:+71234567890" class="btn-secondary">Связаться с менеджером</a>
            <span class="small-note">Или напишите в Telegram / WhatsApp</span>
        </div>
    </div>
</section>

<!-- Преимущества (обновлённые) -->
<section class="advantages">
    <div class="container">
        <h2>Почему выбирают LIEDA</h2>
        <div class="advantages-grid">
            <div class="advantage-item">
                <h3>Научный подход</h3>
                <p>Продукты разработаны на основе ТКМ и современных исследований</p>
            </div>
            <div class="advantage-item">
                <h3>Натуральные компоненты</h3>
                <p>Высококачественное сырьё из экологически чистых регионов</p>
            </div>
            <div class="advantage-item">
                <h3>Прямые поставки</h3>
                <p>От производителя к потребителю – без посредников и наценок</p>
            </div>
            <div class="advantage-item">
                <h3>Поддержка 24/7</h3>
                <p>Поможем с выбором и ответим на вопросы</p>
            </div>
        </div>
    </div>
</section>

<!-- Отзывы (статический блок) -->
<section class="reviews">
    <div class="container">
        <h2>Отзывы наших клиентов</h2>
        <div class="reviews-grid">
            <div class="review-card">
                <p>“Принимаю БАДы LIEDA уже полгода – улучшилось самочувствие, прошла усталость. Рекомендую!”</p>
                <cite>— Елена, Москва</cite>
            </div>
            <div class="review-card">
                <p>“Отличная косметика на травах. Кожа стала чище и свежее. Быстрая доставка.”</p>
                <cite>— Анна, Новосибирск</cite>
            </div>
            <div class="review-card">
                <p>“Приборы для здоровья – качество на высоте. Пользуюсь всей семьёй.”</p>
                <cite>— Сергей, СПб</cite>
            </div>
        </div>
    </div>
</section>

<!-- Соцсети и подписка -->
<section class="social-subscribe">
    <div class="container">
        <div class="social-block">
            <h3>Будьте в курсе</h3>
            <p>Подписывайтесь на наш канал в ВКонтакте</p>
            <a href="https://vk.com/whieda" class="btn-vk" target="_blank">Перейти в ВК</a>
        </div>
    </div>
</section>

<script>
// Функция показа уведомления
function showNotification(message, type = 'success') {
    var container = document.getElementById('notification-toast');
    if (!container) {
        // Если контейнера нет, создаём его
        var newContainer = document.createElement('div');
        newContainer.id = 'notification-toast';
        newContainer.className = 'notification-toast';
        document.body.appendChild(newContainer);
        container = newContainer;
    }

    var toast = document.createElement('div');
    toast.className = 'toast-message';
    toast.textContent = message;

    if (type === 'error') {
        toast.style.borderLeftColor = '#e3348e';
    } else {
        toast.style.borderLeftColor = '#658a34';
    }

    container.appendChild(toast);

    setTimeout(function() {
        toast.classList.add('show');
    }, 10);

    setTimeout(function() {
        toast.classList.remove('show');
        setTimeout(function() {
            toast.remove();
        }, 300);
    }, 2000);
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof cart !== 'undefined') {
        // Находим все кнопки .buy-button на странице (включая хиты)
        const buyButtons = document.querySelectorAll('.buy-button');
        buyButtons.forEach(btn => {
            // Убираем старый обработчик, если был (чтобы не дублировать)
            btn.removeEventListener('click', window.buyHandler);
            // Создаём обработчик
            const handler = function(e) {
                const hasOptions = this.dataset.hasOptions === 'true';
                if (hasOptions) {
                    showNotification('У этого товара есть опции (размер, цвет и т.д.).\n\nПерейдите в карточку товара и выберите необходимые опции перед добавлением в корзину.');
                    return;
                }
                cart.add({
                    id: this.dataset.id,
                    title: this.dataset.title,
                    price: parseFloat(this.dataset.price),
                    thumb: this.dataset.thumb,
                    options: {},
                    optionsString: ''
                });
                showNotification('Товар добавлен в корзину!');
            };
            btn.addEventListener('click', handler);
            // Сохраняем обработчик для возможности удаления (необязательно)
            btn._buyHandler = handler;
        });
    } else {
        console.error('cart.js не загружен');
    }
});
</script>

<?php get_footer(); ?>