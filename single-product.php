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

                <div class="sp-category">
                    <span>Категория</span>
                    <div>
                        <label><input type="radio" name="size" value="взрослый"> взрослый</label>
                        <label><input type="radio" name="size" value="детский"> детский</label>
                    </div>
                </div>

                <div class="sp-color">
                    <span>Выберите цвет:</span>
                    <div>
                        <label><input type="radio" name="color" value="белый"> белый</label>
                        <label><input type="radio" name="color" value="черный"> черный</label>
                        <label><input type="radio" name="color" value="серый"> серый</label>
                        <label><input type="radio" name="color" value="розовый"> розовый</label>
                    </div>
                </div>

                <div class="sp-price">
                    Розничная цена за единицу:<br>
                    <strong><?php echo get_post_meta(get_the_ID(), '_product_price', true) ?: 'Цена не указана'; ?> руб.</strong>
                </div>

                <button class="sp-order-button">В корзину</button>
                <div class="sp-price-note">Цены представлены для ознакомления, точную стоимость уточняйте у менеджера.</div>

                <div class="sp-description">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>