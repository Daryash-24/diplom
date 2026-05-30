<?php /* Template Name: Каталог */ ?>
<?php get_header(); ?>

<div class="catalog-page">

    <div class="catalog-search-wrapper">
        <form class="catalog-search" onsubmit="return false;">
            <input type="text" id="catalog-search-input" placeholder="Поиск товара..." />
            <button type="button" id="catalog-search-button">Найти</button>
        </form>
    </div>

    <div class="catalog-layout">
        <div class="catalog-sidebar">
            <div class="catalog-filters">
                <h3>Категории</h3>
                <?php
                $categories = get_terms( array(
                    'taxonomy'   => 'product_category',
                    'hide_empty' => true,
                ) );
                if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
                    echo '<ul class="category-list-vertical">';
                    // Получаем текущий ID категории из URL (если есть)
                    $current_cat_id = isset( $_GET['cat_id'] ) ? intval( $_GET['cat_id'] ) : 0;
                    foreach ( $categories as $cat ) {
                        $active_class = ( $current_cat_id === $cat->term_id ) ? 'active' : '';
                        // Передаём в URL параметр cat_id = числовой ID категории
                        $url = add_query_arg( 'cat_id', $cat->term_id, get_permalink() );
                        echo '<li><a href="' . esc_url( $url ) . '" class="' . $active_class . '">' . esc_html( $cat->name ) . '</a></li>';
                    }
                    // Ссылка "Все товары" (без параметра cat_id)
                    $all_active_class = ( $current_cat_id === 0 ) ? 'active' : '';
                    echo '<li><a href="' . esc_url( get_permalink() ) . '" class="' . $all_active_class . '">Все товары</a></li>';
                    echo '</ul>';
                } else {
                    echo '<p>Категории не созданы.</p>';
                }
                ?>
            </div>
        </div>
        <div class="catalog-content">
            <div class="products-grid">
                <?php
                $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
                $args = array(
                    'post_type'      => 'product',
                    'posts_per_page' => 6,
                    'paged'          => $paged,
                );
                if ( isset( $_GET['cat_id'] ) && ! empty( $_GET['cat_id'] ) ) {
                    $args['tax_query'] = array(
                        array(
                            'taxonomy' => 'product_category',
                            'field'    => 'term_id',
                            'terms'    => intval( $_GET['cat_id'] ),
                        ),
                    );
                }
                $products_query = new WP_Query( $args );
                if ( $products_query->have_posts() ) :
                    while ( $products_query->have_posts() ) : $products_query->the_post();
                        $price = get_post_meta( get_the_ID(), '_product_price', true );
                        $thumb_url = get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' );
                        ?>
                        <div class="product-card" data-title="<?php echo esc_attr(get_the_title()); ?>">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="product-image">
                                    <?php the_post_thumbnail( 'medium' ); ?>
                                </div>
                            <?php endif; ?>
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <div class="product-price"><?php echo $price ? $price . ' руб.' : 'Цена не указана'; ?></div>
                            <?php 
                            $options_text = get_post_meta(get_the_ID(), '_product_options', true);
                            $has_options = !empty(trim($options_text));
                            ?>
                            <button class="buy-button" 
                                    data-id="<?php the_ID(); ?>" 
                                    data-title="<?php echo esc_attr(get_the_title()); ?>" 
                                    data-price="<?php echo esc_attr($price); ?>" 
                                    data-thumb="<?php echo esc_attr($thumb_url); ?>"
                                    data-has-options="<?php echo $has_options ? 'true' : 'false'; ?>">
                                <?php echo $has_options ? 'Выбрать опции' : 'Купить'; ?>
                            </button>
                        </div>
                    <?php endwhile; ?>
                    <div class="pagination">
                        <?php echo paginate_links( array(
                            'total'     => $products_query->max_num_pages,
                            'current'   => $paged,
                            'format'    => '?paged=%#%',
                            'prev_text' => '«',
                            'next_text' => '»',
                        ) ); ?>
                    </div>
                <?php else : ?>
                    <p>Товары не найдены.</p>
                <?php endif; wp_reset_postdata(); ?>
            </div>
        </div>
    </div>
</div>

<script>
// Поиск по названию
document.getElementById('catalog-search-button').addEventListener('click', function() {
    var searchTerm = document.getElementById('catalog-search-input').value.toLowerCase();
    var cards = document.querySelectorAll('.product-card');
    cards.forEach(function(card) {
        var title = card.getAttribute('data-title').toLowerCase();
        card.style.display = title.indexOf(searchTerm) !== -1 ? '' : 'none';
    });
});

// Добавление в корзину из каталога + проверка опций
if (typeof cart !== 'undefined') {
    document.querySelectorAll('.buy-button').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const hasOptions = this.dataset.hasOptions === 'true';

            if (hasOptions) {
                showNotification('У этого товара есть опции (размер, цвет и т.д.).\n\nПерейдите в карточку товара и выберите необходимые опции перед добавлением в корзину.');
                return;
            }

            // Добавляем товар без опций
            cart.add({
                id: this.dataset.id,
                title: this.dataset.title,
                price: parseFloat(this.dataset.price),
                thumb: this.dataset.thumb,
                options: {},
                optionsString: ''
            });

            showNotification('Товар добавлен в корзину!');
        });
    });
}
</script>

<?php get_footer(); ?>