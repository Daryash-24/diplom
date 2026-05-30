<?php get_header(); ?>

<div class="back-to-catalog">
    <a href="<?php echo esc_url( get_permalink( get_page_by_path('catalog') ) ); ?>" class="back-button">
        ← Назад в каталог
    </a>
</div>

<div class="sp-product">
    <?php while ( have_posts() ) : the_post(); ?>
        <div class="sp-main">
            <div class="sp-gallery">
                <?php if ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail('large', array('class' => 'sp-image')); ?>
                <?php endif; ?>
            </div>
            <div class="sp-info">
                <h1><?php the_title(); ?></h1>

                <?php
                // Динамические опции из мета-поля _product_options
                $options_text = get_post_meta(get_the_ID(), '_product_options', true);
                $options = array();
                if (!empty($options_text)) {
                    $lines = explode("\n", $options_text);
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (strpos($line, ':') !== false) {
                            list($label, $values) = explode(':', $line, 2);
                            $label = trim($label);
                            $values = array_map('trim', explode(',', $values));
                            $options[] = array('label' => $label, 'values' => $values);
                        }
                    }
                }
                ?>

                <?php foreach ($options as $option) : ?>
                <div class="sp-option">
                    <span><?php echo esc_html($option['label']); ?></span>
                    <div>
                        <?php foreach ($option['values'] as $value) : ?>
                            <label>
                                <input type="radio" name="option_<?php echo sanitize_title($option['label']); ?>" value="<?php echo esc_attr($value); ?>">
                                <?php echo esc_html($value); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="sp-price">
                    Розничная цена за единицу:<br>
                    <strong><?php echo get_post_meta(get_the_ID(), '_product_price', true) ?: 'Цена не указана'; ?> руб.</strong>
                </div>

                <button id="add-to-cart" class="sp-order-button">В корзину</button>
                <div class="sp-price-note">Цены представлены для ознакомления, точную стоимость уточняйте у менеджера.</div>
            </div>
        </div>

        <!-- Вкладки -->
        <div class="product-tabs">
            <div class="tabs-nav">
                <button class="tab-link active" data-tab="description">Описание</button>
                <button class="tab-link" data-tab="reviews">Отзывы</button>
            </div>
            <div class="tabs-content">
                <div class="tab-pane active" id="tab-description">
                    <?php the_content(); ?>
                </div>
                <div class="tab-pane" id="tab-reviews">
                    <p>Здесь скоро появятся отзывы покупателей. Оставьте свой отзыв первым!</p>
                </div>
            </div>
        </div>

    <?php endwhile; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof cart === 'undefined') {
        console.error('cart.js не загружен');
        return;
    }

    const addButton = document.getElementById('add-to-cart');
    if (!addButton) return;

    // Проверка, выбраны ли все опции
    function areAllOptionsSelected() {
        const optionGroups = document.querySelectorAll('.sp-option');
        if (optionGroups.length === 0) return true; // опций нет — можно добавлять

        for (let group of optionGroups) {
            const selected = group.querySelector('input[type="radio"]:checked');
            if (!selected) return false;
        }
        return true;
    }

    // Добавление в корзину
    addButton.addEventListener('click', function() {
        // Если есть опции и они не выбраны — показываем предупреждение
        if (!areAllOptionsSelected()) {
            showNotification('Пожалуйста, выберите все необходимые опции товара (размер, цвет и т.д.)');
            return;
        }

        const productId = <?php echo get_the_ID(); ?>;
        const productTitle = <?php echo json_encode(get_the_title()); ?>;
        const productPrice = parseFloat(<?php echo get_post_meta(get_the_ID(), '_product_price', true) ?: 0; ?>);
        const productThumb = <?php echo json_encode(get_the_post_thumbnail_url(get_the_ID(), 'medium')); ?> || 
                    <?php echo json_encode(get_the_post_thumbnail_url(get_the_ID(), 'thumbnail')); ?> || '';

        // Собираем выбранные опции
        const options = {};
        let optionsString = '';

        document.querySelectorAll('.sp-option').forEach(optionDiv => {
            const label = optionDiv.querySelector('span').innerText.trim();
            const selectedRadio = optionDiv.querySelector('input[type="radio"]:checked');
            if (selectedRadio) {
                options[label] = selectedRadio.value;
            }
        });

        if (Object.keys(options).length > 0) {
            optionsString = Object.entries(options).map(([k, v]) => `${k}: ${v}`).join(', ');
        }

        const optionsKey = JSON.stringify(options); // options – объект выбранных опций

        cart.add({
            id: productId,
            title: productTitle,
            price: productPrice,
            options: options,
            optionsString: optionsString,
            thumb: productThumb,
            optionsKey: optionsKey   // <-- добавляем
        });

        showNotification('Товар добавлен в корзину!');
    });

});

// Вкладки
document.querySelectorAll('.tab-link').forEach(link => {
    link.addEventListener('click', function(e) {
        const tabId = this.dataset.tab;
        document.querySelectorAll('.tab-link').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
        document.getElementById(`tab-${tabId}`).classList.add('active');
    });
});
</script>

<?php get_footer(); ?>