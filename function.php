<?php
function myvitrina_assets() {

    wp_enqueue_style( 'myvitrina-style', get_stylesheet_uri(), array(), '1.0' );
}

add_action( 'wp_enqueue_scripts', 'myvitrina_assets' );

// Поддержка миниатюр (изображений записей)
add_theme_support( 'post-thumbnails' );

// Регистрация меню
function myvitrina_menus() {
    register_nav_menus( array(
        'primary' => 'Главное меню'
    ) );
}
add_action( 'after_setup_theme', 'myvitrina_menus' );