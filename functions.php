<?php

add_theme_support('title-tag');

function my_vitrina_assets() {
    wp_enqueue_style('style-css', get_stylesheet_uri());
    
    wp_enqueue_style('main-css', get_template_directory_uri() . '/assets/css/main.css');
    wp_enqueue_style('header-css', get_template_directory_uri() . '/assets/css/header.css');
    wp_enqueue_style('footer-css', get_template_directory_uri() . '/assets/css/footer.css');
    wp_enqueue_style('single-product-css', get_template_directory_uri() . '/assets/css/single-product.css');
    wp_enqueue_script('cart-js', get_template_directory_uri() . '/assets/js/cart.js', array(), '1.0', false);

}
// Подключаем скрипт для страницы товара
    if ( is_singular('product') ) {
        wp_enqueue_script('single-product-js', get_template_directory_uri() . '/assets/js/single-product.js', array(), '1.0', true);
    }
add_action('wp_enqueue_scripts', 'my_vitrina_assets');

function my_vitrina_menus() {
    register_nav_menus( array('primary' => 'Главное меню') );
}
add_action( 'after_setup_theme', 'my_vitrina_menus' );


// 1. Регистрация типа записей "Товар"
function register_product_post_type() {
    $labels = array(
        'name'               => 'Товары',
        'singular_name'      => 'Товар',
        'menu_name'          => 'Товары',
        'add_new'            => 'Добавить новый',
        'add_new_item'       => 'Добавить новый товар',
        'edit_item'          => 'Редактировать товар',
        'new_item'           => 'Новый товар',
        'view_item'          => 'Просмотр товара',
        'search_items'       => 'Поиск товаров',
        'not_found'          => 'Товаров не найдено',
        'not_found_in_trash' => 'В корзине нет товаров',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'product' ), // URL товаров будет /product/название/
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-cart', // иконка в админке
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
    );

    register_post_type( 'product', $args );
}
add_action( 'init', 'register_product_post_type' );

// 2. Регистрация таксономии "Категория товара" (иерархическая)
function register_product_category_taxonomy() {
    $labels = array(
        'name'              => 'Категории товаров',
        'singular_name'     => 'Категория',
        'search_items'      => 'Поиск категорий',
        'all_items'         => 'Все категории',
        'parent_item'       => 'Родительская категория',
        'parent_item_colon' => 'Родительская категория:',
        'edit_item'         => 'Редактировать категорию',
        'update_item'       => 'Обновить категорию',
        'add_new_item'      => 'Добавить новую категорию',
        'new_item_name'     => 'Имя новой категории',
        'menu_name'         => 'Категории',
    );

    $args = array(
        'hierarchical'      => true, // иерархическая, как рубрики
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'product-category' ),
    );

    register_taxonomy( 'product_category', array( 'product' ), $args );
}
add_action( 'init', 'register_product_category_taxonomy' );

// Добавляем мета-бокс для цены и наличия
function add_product_meta_boxes() {
    add_meta_box(
        'product_details',
        'Детали товара',
        'display_product_meta_box',
        'product',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'add_product_meta_boxes' );

function display_product_meta_box( $post ) {
    $price = get_post_meta( $post->ID, '_product_price', true );
    $stock = get_post_meta( $post->ID, '_product_stock', true );
    ?>
    <p>
        <label for="product_price">Цена (руб):</label>
        <input type="number" step="0.01" name="product_price" id="product_price" value="<?php echo esc_attr( $price ); ?>" style="width: 150px;" />
    </p>
    <p>
        <label for="product_stock">Наличие:</label>
        <select name="product_stock" id="product_stock">
            <option value="in_stock" <?php selected( $stock, 'in_stock' ); ?>>В наличии</option>
            <option value="out_of_stock" <?php selected( $stock, 'out_of_stock' ); ?>>Нет в наличии</option>
            <option value="preorder" <?php selected( $stock, 'preorder' ); ?>>Под заказ</option>
        </select>
    </p>
    <?php
}

// Сохраняем мета-поля
function save_product_meta( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;
    if ( isset( $_POST['product_price'] ) ) {
        update_post_meta( $post_id, '_product_price', sanitize_text_field( $_POST['product_price'] ) );
    }
    if ( isset( $_POST['product_stock'] ) ) {
        update_post_meta( $post_id, '_product_stock', sanitize_text_field( $_POST['product_stock'] ) );
    }
}
add_action( 'save_post_product', 'save_product_meta' );

add_theme_support('post-thumbnails', array('post', 'product'));

// Добавляем метабокс для дополнительных опций
function add_product_options_meta_box() {
    add_meta_box(
        'product_options',
        'Дополнительные характеристики',
        'display_product_options_meta_box',
        'product',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_product_options_meta_box');

function display_product_options_meta_box($post) {
    $options = get_post_meta($post->ID, '_product_options', true);
    ?>
    <p>Введите опции в формате: <strong>Название: значение1, значение2, значение3</strong><br>
    Каждую опцию с новой строки.</p>
    <textarea name="product_options" rows="5" style="width:100%;"><?php echo esc_textarea($options); ?></textarea>
    <?php
}

// Сохраняем поле
function save_product_options_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (isset($_POST['product_options'])) {
        update_post_meta($post_id, '_product_options', sanitize_textarea_field($_POST['product_options']));
    }
}
add_action('save_post_product', 'save_product_options_meta');

// Отключаем канонический редирект для страницы каталога (по ID)
add_filter( 'redirect_canonical', function( $redirect, $requested_url ) {
    if ( is_page( 'catalog' ) ) { 
        return false;
    }
    return $redirect;
}, 10, 2 );

function force_product_search( $query ) {
    if ( ! is_admin() && $query->is_main_query() && is_page( 'catalog' ) && isset( $_GET['s'] ) ) {
        $query->set( 'post_type', 'product' );
    }
}
add_action( 'pre_get_posts', 'force_product_search' );

// AJAX обработчик отправки заказа из корзины

add_action('wp_ajax_send_order', 'handle_send_order');
add_action('wp_ajax_nopriv_send_order', 'handle_send_order');

function handle_send_order() {
    
    if (!wp_verify_nonce($_POST['security'] ?? '', 'send_order_nonce')) {
        wp_send_json_error('Ошибка безопасности. Обновите страницу.');
    }

    $name     = sanitize_text_field($_POST['customer_name'] ?? '');
    $phone    = sanitize_text_field($_POST['customer_phone'] ?? '');
    $email    = sanitize_email($_POST['customer_email'] ?? '');
    $comment  = sanitize_textarea_field($_POST['customer_comment'] ?? '');
    
    $cart_json = $_POST['cart_items'] ?? '[]';
    $cart_items = json_decode(stripslashes($cart_json), true);

    if (empty($cart_items) || !is_array($cart_items)) {
        wp_send_json_error('Корзина пуста');
    }

    if (empty($phone) && empty($email)) {
        wp_send_json_error('Укажите телефон или email для связи');
    }

    // Формируем письмо
    $subject = 'Новый заказ с сайта - ' . get_bloginfo('name');
    
    $message = "<h2>Новый заказ с веб-витрины</h2>";
    $message .= "<p><strong>Дата:</strong> " . date('d.m.Y H:i') . "</p>";
    
    if ($name)  $message .= "<p><strong>Имя:</strong> " . esc_html($name) . "</p>";
    if ($phone) $message .= "<p><strong>Телефон:</strong> " . esc_html($phone) . "</p>";
    if ($email) $message .= "<p><strong>Email:</strong> " . esc_html($email) . "</p>";
    if ($comment) $message .= "<p><strong>Комментарий:</strong><br>" . nl2br(esc_html($comment)) . "</p>";
    
    $message .= "<hr><h3>Состав заказа:</h3>";
    $message .= "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
    $message .= "<tr><th>Товар</th><th>Параметры</th><th>Кол-во</th><th>Цена</th><th>Сумма</th></tr>";

    $total = 0;
    foreach ($cart_items as $item) {
        $options = $item['optionsString'] ?? '';
        if (empty($options) && ($item['size'] || $item['color'])) {
            $options = "Размер: " . ($item['size'] ?? '—') . " | Цвет: " . ($item['color'] ?? '—');
        }
        
        $item_total = ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        $total += $item_total;

        $message .= "<tr>";
        $message .= "<td><strong>" . esc_html($item['title'] ?? 'Без названия') . "</strong></td>";
        $message .= "<td>" . esc_html($options) . "</td>";
        $message .= "<td style='text-align:center;'>" . ($item['quantity'] ?? 1) . "</td>";
        $message .= "<td>" . number_format($item['price'] ?? 0, 0, ',', ' ') . " ₽</td>";
        $message .= "<td>" . number_format($item_total, 0, ',', ' ') . " ₽</td>";
        $message .= "</tr>";
    }
    $message .= "</table>";
    $message .= "<p><strong>Итого к оплате: " . number_format($total, 0, ',', ' ') . " ₽</strong></p>";

    $to = 'daryaaleksandrovna.work@gmail.com';
    $headers = ['Content-Type: text/html; charset=UTF-8'];

    // Пытаемся отправить письмо
    $sent = wp_mail($to, $subject, $message, $headers);

    if ($sent) {
        wp_send_json_success('Заказ успешно отправлен');
    } else {
        // Если не отправилось — возвращаем подробную ошибку
        global $phpmailer;
        $error_info = $phpmailer ? $phpmailer->ErrorInfo : 'Неизвестная ошибка wp_mail()';
        wp_send_json_error('Не удалось отправить письмо. Ошибка: ' . $error_info);
    }
}
?>