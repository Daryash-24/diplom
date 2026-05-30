<?php 
/* Template Name: О нас */ 
get_header(); 
?>

<div class="about-page">

    <!-- Основной контент (редактируется через админку) -->
    <section class="about-content">
        <div class="container">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <div class="about-text">
                    <?php the_content(); ?>
                </div>
            <?php endwhile; endif; ?>
        </div>
    </section>

    <!-- Миссия (можно оставить как есть или тоже вывести через редактор) -->
    <section class="about-mission">
        <div class="container">
            <h2>Наша миссия</h2>
            <p>Делать качественные продукты на основе традиционной китайской медицины доступными для всех, кто заботится о своём здоровье.</p>
        </div>
    </section>

    <!-- История -->
    <section class="about-history">
        <div class="container">
            <h2>История создания</h2>
            <p>LIEDA создана по инициативе биотехнологической корпорации FOHERB и Евразийской Ассоциации. Старт на рынках СНГ — 2022 год, выход на мировой рынок — 2023 год.</p>
        </div>
    </section>

    <!-- Партнёры -->
    <section class="about-partners">
        <div class="container">
            <h2>Наши партнёры</h2>
            <div class="partners-grid">
                <div class="partner">FOHERB</div>
                <div class="partner">Евразийская Ассоциация</div>
                <div class="partner">Производители из Китая</div>
                <div class="partner">Научные центры ТКМ</div>
            </div>
        </div>
    </section>

    <!-- Команда -->
    <section class="about-team">
        <div class="container">
            <h2>Команда</h2>
            <div class="team-grid">
                <!-- Здесь можно добавить реальные фото и имена -->
                <div class="team-member">👤 Руководитель направления</div>
                <div class="team-member">👤 Главный специалист по ТКМ</div>
                <div class="team-member">👤 Менеджер по развитию</div>
            </div>
        </div>
    </section>

    <!-- Контакты -->
    <section class="about-contacts">
        <div class="container">
            <h2>Контакты</h2>
            <p>Email: <a href="mailto:info@whieda.com">example@gmail.com</a></p>
            <p>Телефон: <a href="tel:+71234567890">+7 (123) 456-78-90</a></p>
            <p>Адрес: г. Новосибирск, ул. Примерная, д. 1</p>
        </div>
    </section>
</div>

<?php get_footer(); ?>