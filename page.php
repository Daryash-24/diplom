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

<?php else : ?>
    <article>
        <h1><?php the_title(); ?></h1>
        <?php the_content(); ?>
    </article>
<?php endif; ?>

<?php get_footer(); ?>