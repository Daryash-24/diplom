<?php get_header(); ?>

    <h1>Проверка на работоспособность</h1>

<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <div class="entry-content">
                <?php the_excerpt(); // или the_content() ?>
            </div>
        </article>
    <?php endwhile; ?>
<?php else : ?>
    <p>Пока ничего нет.</p>
<?php endif; ?>

<?php get_footer(); ?>