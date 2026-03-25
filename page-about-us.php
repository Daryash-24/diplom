<?php 
/* 
Template Name: О нас 
*/ 
?>

<?php get_header(); ?>

<div class="about-page">
    <section class="about-mission">
        <h1><?php the_title(); ?></h1>
        <div class="mission-text">
            <p>Наша миссия — делать качественные продукты на основе традиционной китайской медицины доступными для всех, кто заботится о своём здоровье.</p>
        </div>
    </section>

    <section class="about-history">
        <h2>История создания</h2>
        <p>WHIEDA создана по инициативе биотехнологической корпорации FOHERB и Евразийской Ассоциации. Старт на рынках СНГ — 2022 год, выход на мировой рынок — 2023 год.</p>
    </section>

    <section class="about-partners">
        <h2>Наши партнёры</h2>
        <div class="partners-grid">
            <div class="partner">FOHERB</div>
            <div class="partner">Евразийская Ассоциация</div>
            <div class="partner">Производители из Китая</div>
            <div class="partner">Научные центры ТКМ</div>
        </div>
    </section>

    <section class="about-team">
        <h2>Команда</h2>
        <div class="team-grid">
            <p>*Здесь может быть ифнормация о Вашей команде*</p>
            <!-- Добавь других членов команды по желанию -->
        </div>
    </section>

    <section class="about-contacts">
        <h2>Контакты</h2>
        <p>Email: </p>
        <p>Телефон: </p>
        <p>Адрес:</p>
    </section>
</div>

<?php get_footer(); ?>