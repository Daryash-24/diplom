<?php 
/* 
Template Name: Каталог 
*/ 
?>

<?php /* Template Name: Каталог */ ?>
<?php get_header(); ?>

<div class="catalog-wrapper">
    <h1><?php the_title(); ?></h1>

    <!-- Форма поиска -->
    <form method="get" action="<?php echo esc_url( get_permalink() ); ?>" class="catalog-search">
        <input type="text" name="s" placeholder="Поиск товара..." value="<?php echo esc_attr( get_search_query() ); ?>" />
        <button type="submit">Найти</button>
    </form>

    <!-- Фильтр по категориям -->
    <div class="catalog-filters">
        <h3>Категории</h3>
        <?php
        $categories = get_terms( array(
            'taxonomy'   => 'product_category',
            'hide_empty' => true,
        ) );
        if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
            echo '<ul class="category-list">';
            foreach ( $categories as $cat ) {
                $active = ( isset( $_GET['cat'] ) && $_GET['cat'] == $cat->slug ) ? 'class="active"' : '';
                echo '<li ' . $active . '><a href="' . esc_url( add_query_arg( 'cat', $cat->slug, get_permalink() ) ) . '">' . $cat->name . '</a></li>';
            }
            // Ссылка "Все товары"
            $all_active = ! isset( $_GET['cat'] ) ? 'class="active"' : '';
            echo '<li ' . $all_active . '><a href="' . esc_url( get_permalink() ) . '">Все товары</a></li>';
            echo '</ul>';
        } else {
            echo '<p>Категории не созданы.</p>';
        }
        ?>
    </div>

    <!-- Список товаров -->
    <div class="products-grid">
        <?php
        // Параметры запроса товаров
        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => 6,
            'paged'          => $paged,
        );

        // Поиск по названию
        if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) {
            $args['s'] = sanitize_text_field( $_GET['s'] );
        }

        // Фильтр по категории
        if ( isset( $_GET['cat'] ) && ! empty( $_GET['cat'] ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_category',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field( $_GET['cat'] ),
                ),
            );
        }

        $products_query = new WP_Query( $args );

        if ( $products_query->have_posts() ) :
            while ( $products_query->have_posts() ) : $products_query->the_post();
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
                    <div class="product-price"><?php echo $price ? $price . ' руб.' : 'Цена не указана'; ?></div>
                    <div class="product-stock"><?php
                        switch ( $stock ) {
                            case 'in_stock': echo 'В наличии'; break;
                            case 'out_of_stock': echo 'Нет в наличии'; break;
                            case 'preorder': echo 'Под заказ'; break;
                            default: echo '';
                        }
                    ?></div>
                    <button class="buy-button">Купить</button>
                </div>
            <?php endwhile; ?>

            <!-- Пагинация -->
            <div class="pagination">
                <?php
                echo paginate_links( array(
                    'total'     => $products_query->max_num_pages,
                    'current'   => $paged,
                    'format'    => '?paged=%#%',
                    'prev_text' => '«',
                    'next_text' => '»',
                ) );
                ?>
            </div>

        <?php else : ?>
            <p>Товары не найдены.</p>
        <?php endif; wp_reset_postdata(); ?>
    </div>
</div>

<?php get_footer(); ?>