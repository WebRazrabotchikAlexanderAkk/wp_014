<?php
$ver = "1.03";

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('main-style', get_template_directory_uri() . '/assets/css/style.min.css',
        array(),
        $GLOBALS['ver']);

    // wp_deregister_script('jquery');
    // wp_register_script('jquery', get_template_directory_uri() . '/assets/js/libs.min.js');
    // //wp_register_script('jquery', 'https://code.jquery.com/jquery-3.6.0.min.js');
    // wp_enqueue_script('jquery');

    wp_enqueue_script(
        'main-script',
        get_template_directory_uri() . '/assets/js/app.min.js',
        array(),
        'null',
        true
    );
    wp_enqueue_script(
        'input-script',
        get_template_directory_uri() . '/assets/js/input.js',
        array(),
        'null',
        true
    );
});

add_theme_support('post-thumbnails');
add_theme_support('title-tag');
add_theme_support('custom-logo');

add_filter('upload_mimes', 'svg_upload_allow');

add_action('after_setup_theme', function () {
    register_nav_menus([
        'header_menu' => 'Меню в шапке',
        'actions_menu' => 'Меню действий',
        'footer_menu' => 'Меню в подвале'
    ]);
});

// Тема поддерживает Woocommerce
add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
add_theme_support( 'woocommerce' );
}



// Хлебные крошки
function the_breadcrumb() {
    echo '<div id="breadcrumb"><ul><li><a href="/">Главная</a></li><li></li>';
    if ( is_category() || is_single() ) {
        $cats = get_the_category();
        $cat = $cats[0];
        echo '<li><a href="'.get_category_link($cat->term_id).'">'.$cat->name.'</a></li><li></li>';
    }
    if(is_single()){
        echo '<li>';
        the_title();
        echo '</li>';
    }
    if(is_page()){
        echo '<li>';
        the_title();
        echo '</li>';
    }
    echo '</ul><div class="clear"></div></div>';
}

// Ссылки на пост
add_filter( 'excerpt_more', 'new_excerpt_more' );
function new_excerpt_more( $more ){
	global $post;
	return '<a href="'. get_permalink($post) . '">Подробнее</a>';
}
// Количество слов в превью поста
add_filter( 'excerpt_length', function(){
	return 40;
} );
// Окончание слов в превью поста
add_filter( 'excerpt_more', function( $more ) {
	return '...';
} );


# Добавляет SVG в список разрешенных для загрузки файлов.
function svg_upload_allow($mimes)
{
    $mimes['svg'] = 'image/svg+xml';

    return $mimes;
}

add_filter('wp_check_filetype_and_ext', 'fix_svg_mime_type', 10, 5);

# Исправление MIME типа для SVG файлов.
function fix_svg_mime_type($data, $file, $filename, $mimes, $real_mime = '')
{

    // WP 5.1 +
    if (version_compare($GLOBALS['wp_version'], '5.1.0', '>=')) {
        $dosvg = in_array($real_mime, ['image/svg', 'image/svg+xml']);
    } else {
        $dosvg = ('.svg' === strtolower(substr($filename, -4)));
    }

    // mime тип был обнулен, поправим его
    // а также проверим право пользователя
    if ($dosvg) {

        // разрешим
        if (current_user_can('manage_options')) {

            $data['ext'] = 'svg';
            $data['type'] = 'image/svg+xml';
        }
        // запретим
        else {
            $data['ext'] = $type_and_ext['type'] = false;
        }
    }

    return $data;
}
