<?php get_header(); ?>


<div class="single-product-wrapper">
    <?php while ( have_posts() ) : the_post(); ?>
        <div class="product-main">
            <div class="product-gallery">
                <?php if ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail( 'large', array( 'class' => 'product-image' ) ); ?>
                <?php endif; ?>
            </div>
            <div class="product-info">
                <h1><?php the_title(); ?></h1>
                
                <!-- Блок выбора размера (статический) -->
                <div class="product-size">
                    <span>Категория</span>
                    <div class="size-options">
                        <label><input type="radio" name="size" value="взрослый"> взрослый</label>
                        <label><input type="radio" name="size" value="десткий"> детский</label>
                    </div>
                </div>
                
                <!-- Блок выбора цвета (статический) -->
                <div class="product-color">
                    <span>Выберите цвет:</span>
                    <div class="color-options">
                        <label><input type="radio" name="color" value="белый"> белый</label>
                        <label><input type="radio" name="color" value="черный"> черный</label>
                        <label><input type="radio" name="color" value="серый"> серый</label>
                        <label><input type="radio" name="color" value="розовый"> розовый</label>
                    </div>
                </div>
                
                <div class="product-price">
                    Розничная цена за единицу:<br>
                    <strong><?php echo get_post_meta( get_the_ID(), '_product_price', true ) ?: 'Цена не указана'; ?> руб.</strong>
                </div>
                
                <button class="order-button">В корзину</button>
                <div class="price-note">Цены представлены для ознакомления, точную стоимость уточняйте у менеджера.</div>
                
                <div class="product-description">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>