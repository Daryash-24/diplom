<?php

add_theme_support('title-tag');

function my_vitrina_assets() {
    wp_enqueue_style('style-css', get_stylesheet_uri());
    
    wp_enqueue_style('main-css', get_template_directory_uri() . '/assets/css/main.css');
    wp_enqueue_style('header-css', get_template_directory_uri() . '/assets/css/header.css');
    wp_enqueue_style('footer-css', get_template_directory_uri() . '/assets/css/footer.css');
}
add_action('wp_enqueue_scripts', 'my_vitrina_assets');

function my_vitrina_menus() {
    register_nav_menus( array('primary' => 'Главное меню') );
}
add_action( 'after_setup_theme', 'my_vitrina_menus' );


?>