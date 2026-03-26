<?php get_header(); ?>

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
                // Динамические опции (размер, цвет и т.д.)
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

                <button id="add-to-cart" class="order-button">В корзину</button>
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
document.getElementById('add-to-cart').addEventListener('click', function() {
    // Получаем выбранную категорию (взрослый/детский)
    const selectedSize = document.querySelector('input[name="size"]:checked');
    const size = selectedSize ? selectedSize.value : '';
    // Получаем выбранный цвет
    const selectedColor = document.querySelector('input[name="color"]:checked');
    const color = selectedColor ? selectedColor.value : '';

    const productId = <?php echo get_the_ID(); ?>;
    const productTitle = <?php echo json_encode(get_the_title()); ?>;
    const productPrice = parseFloat(<?php echo get_post_meta(get_the_ID(), '_product_price', true) ?: 0; ?>);

    cart.add({
        id: productId,
        title: productTitle,
        price: productPrice,
        size: size,
        color: color
    });
    alert('Товар добавлен в корзину');
});
</script>

<?php get_footer(); ?>