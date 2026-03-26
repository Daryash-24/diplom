<?php /* Template Name: Каталог */ ?>
<?php get_header(); ?>

<div class="catalog-page">
    <h1><?php the_title(); ?></h1>

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
                    foreach ( $categories as $cat ) {
                        $active = ( isset( $_GET['cat'] ) && $_GET['cat'] == $cat->slug ) ? 'class="active"' : '';
                        echo '<li ' . $active . '><a href="' . esc_url( add_query_arg( 'cat', $cat->slug, get_permalink() ) ) . '">' . $cat->name . '</a></li>';
                    }
                    echo '<li ' . ( ! isset( $_GET['cat'] ) ? 'class="active"' : '' ) . '><a href="' . esc_url( get_permalink() ) . '">Все товары</a></li>';
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
                            <button class="buy-button" data-id="<?php the_ID(); ?>" data-title="<?php echo esc_attr(get_the_title()); ?>" data-price="<?php echo esc_attr($price); ?>" data-thumb="<?php echo esc_attr($thumb_url); ?>">Купить</button>
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
// Клиентский поиск по названию (без перезагрузки)
document.getElementById('catalog-search-button').addEventListener('click', function() {
    var searchTerm = document.getElementById('catalog-search-input').value.toLowerCase();
    var cards = document.querySelectorAll('.product-card');
    cards.forEach(function(card) {
        var title = card.getAttribute('data-title').toLowerCase();
        if (title.indexOf(searchTerm) !== -1) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});

// Добавление в корзину из каталога
if (typeof cart !== 'undefined') {
    document.querySelectorAll('.buy-button').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const id = this.dataset.id;
            const title = this.dataset.title;
            const price = parseFloat(this.dataset.price);
            const thumb = this.dataset.thumb;
            cart.add({
                id: id,
                title: title,
                price: price,
                thumb: thumb,
                options: {},
                optionsString: ''
            });
            alert('Товар добавлен в корзину');
        });
    });
}
</script>

<?php get_footer(); ?>