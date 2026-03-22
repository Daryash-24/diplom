<?php
// Подключаем стили напрямую через wp_head
function my_simple_styles() {
    echo '<link rel="stylesheet" href="' . get_template_directory_uri() . '/style.css">';
}
add_action('wp_head', 'my_simple_styles');