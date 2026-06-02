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
                    $current_cat_id = isset( $_GET['cat_id'] ) ? intval( $_GET['cat_id'] ) : 0;
                    foreach ( $categories as $cat ) {
                        $active = ( $current_cat_id === $cat->term_id ) ? 'active' : '';
                        echo '<li class="' . $active . '"><a href="' . esc_url( add_query_arg( 'cat_id', $cat->term_id, get_permalink() ) ) . '">' . esc_html( $cat->name ) . '</a></li>';
                    }
                    // Ссылка "Все товары"
                    $all_active = ( $current_cat_id === 0 ) ? 'active' : '';
                    echo '<li class="' . $all_active . '"><a href="' . esc_url( get_permalink() ) . '">Все товары</a></li>';
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
                // Выводим ВСЕ товары без пагинации
                $args = array(
                    'post_type'      => 'product',
                    'posts_per_page' => -1,
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
                        $options_text = get_post_meta( get_the_ID(), '_product_options', true );
                        $has_options = !empty( trim( $options_text ) );
                        ?>
                        <div class="product-card" data-title="<?php echo esc_attr( get_the_title() ); ?>">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <div class="product-image">
                                    <?php the_post_thumbnail( 'medium' ); ?>
                                </div>
                            <?php endif; ?>
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <div class="product-price"><?php echo $price ? $price . ' руб.' : 'Цена не указана'; ?></div>
                            <?php if ( $has_options ) : ?>
                                <a href="<?php the_permalink(); ?>" class="buy-button choose-options-btn">Выбрать опции</a>
                            <?php else : ?>
                                <button class="buy-button" 
                                    data-id="<?php the_ID(); ?>" 
                                    data-title="<?php echo esc_attr( get_the_title() ); ?>" 
                                    data-price="<?php echo esc_attr( $price ); ?>"
                                    data-thumb="<?php echo esc_url( $thumb_url ); ?>">Купить</button>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                <?php else : ?>
                    <p class="no-products-found">Товары не найдены.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Поиск по названию (клиентский, по всем карточкам)
document.getElementById('catalog-search-button').addEventListener('click', function() {
    var searchTerm = document.getElementById('catalog-search-input').value.toLowerCase();
    var cards = document.querySelectorAll('.product-card');
    var found = false;
    cards.forEach(function(card) {
        var title = card.getAttribute('data-title').toLowerCase();
        if (title.indexOf(searchTerm) !== -1) {
            card.style.display = '';
            found = true;
        } else {
            card.style.display = 'none';
        }
    });
    // Проверяем, есть ли сообщение "Товары не найдены" и показываем/скрываем
    var noProductsMsg = document.querySelector('.no-products-found');
    if (!found && cards.length > 0) {
        if (!noProductsMsg) {
            var msg = document.createElement('p');
            msg.className = 'no-products-found';
            msg.textContent = 'Товары не найдены. Попробуйте изменить запрос.';
            document.querySelector('.products-grid').after(msg);
        } else {
            noProductsMsg.style.display = 'block';
        }
    } else if (noProductsMsg) {
        noProductsMsg.style.display = 'none';
    }
});

// Добавление в корзину 
if (typeof cart !== 'undefined') {
    document.querySelectorAll('.buy-button').forEach(btn => {
        if (btn.tagName === 'A') return;
        btn.addEventListener('click', function(e) {
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