<!DOCTYPE html>

<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <header>
            
        <nav>
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'container'      => false,   // чтобы не оборачивал в дополнительный div
                'menu_class'     => 'menu',  // класс для ul (можно переименовать)
            ) );
            ?>
        </nav>

    </header>
    <main>