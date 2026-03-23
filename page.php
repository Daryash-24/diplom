<?php get_header(); ?>


<?php if ( is_page( 'about-us' ) ) : ?>
    <section class="page-section about">
        <h1><?php the_title(); ?></h1>
        <div class="content">
            <?php the_content(); ?>
        </div>
        <!-- Дополнительный блок, если нужен, например, фото команды -->
        <div class="team">
            <h2>*Здесь может быть ваша команда</h2>
            <!-- <div class="team-photo">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/team.jpg" alt="Команда">
            </div> -->
        </div>
    </section>

<?php elseif ( is_page( 'catalog' ) ) : ?>
    <section class="page-section catalog">
        <h1><?php the_title(); ?></h1>
        <div class="catalog-items">
            <?php the_content(); ?>
            
            <div class="product-grid">
                <?php
                // Запрос товаров
                $products = new WP_Query( array(
                    'post_type'      => 'product',
                    'posts_per_page' => -1,  // вывести все товары
                    'post_status'    => 'publish'
                ) );

                if ( $products->have_posts() ) :
                    while ( $products->have_posts() ) : $products->the_post();
                        $price = get_post_meta( get_the_ID(), '_product_price', true );
                        $stock = get_post_meta( get_the_ID(), '_product_stock', true );
                        ?>
                        <div class="product-card">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="product-image">
                                    <?php the_post_thumbnail( 'medium' ); ?>
                                </div>
                            <?php endif; ?>
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <div class="product-price">
                                <?php echo $price ? $price . ' руб.' : 'Цена не указана'; ?>
                            </div>
                            <div class="product-stock">
                                <?php
                                switch ( $stock ) {
                                    case 'in_stock': echo 'В наличии'; break;
                                    case 'out_of_stock': echo 'Нет в наличии'; break;
                                    case 'preorder': echo 'Под заказ'; break;
                                    default: echo '';
                                }
                                ?>
                            </div>
                            <button class="buy-button">Купить</button>
                        </div>
                    <?php endwhile;
                    wp_reset_postdata();
                else : ?>
                    <p>Товаров пока нет. Добавьте товары в админке.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php else : ?>
    <article>
        <h1><?php the_title(); ?></h1>
        <?php the_content(); ?>
    </article>
<?php endif; ?>


<?php get_footer(); ?>