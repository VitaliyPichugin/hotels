<?php
function my_scripts_method() {
    wp_deregister_script( 'jquery2' );
    wp_register_script( 'jquery2', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js');
    wp_enqueue_script( 'jquery2' );
}

add_action( 'wp_enqueue_scripts', 'my_scripts_method' );
// правильный способ подключить стили и скрипты
add_action( 'wp_enqueue_scripts', 'theme_name_scripts' );
// add_action('wp_print_styles', 'theme_name_scripts'); // можно использовать этот хук он более поздний
function theme_name_scripts() {

    wp_register_style( 'jquery-ui', 'http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );
    wp_enqueue_style( 'jquery-ui' );

    wp_enqueue_style('allhotels', home_url().'/wp-content/plugins/allhotels/css/allhotels.css');

    wp_enqueue_script('datepicker3', home_url().'/wp-content/plugins/allhotels/js/datepicker3.js', array('jquery-ui-datepicker'));
    wp_enqueue_script('datepicker4', home_url().'/wp-content/plugins/allhotels/js/datepicker4.js', array('jquery-ui-datepicker'));

    wp_enqueue_script('table', home_url().'/wp-content/plugins/allhotels/js/table.js', array('jquery2'), '1.0.0', true);

    $country = Array("obj" => $_SESSION['data']);
    wp_enqueue_script( 'myscript1', home_url().'/wp-content/plugins/allhotels/js/search_hotels.js',  array('jquery'), '1.0.0', true);
    wp_localize_script( 'myscript1', 'hotel_name', $country);
}

