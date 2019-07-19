<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

add_action( 'wp_enqueue_scripts', function() {

    $parent_style = 'hello-elementor';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'ran-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        filemtime(__DIR__.'/style.css')
    );

    wp_enqueue_script(
        'ran-script',
        get_stylesheet_directory_uri() . '/scripts.js',
        array( 'jquery' ),
        filemtime(__DIR__.'/scripts.js')
    );

});


// use the accent-color in theme customizer for Mobile Browser-Header color
add_action('wp_head', function() {
    $scheme_colors = get_option('elementor_scheme_color');
    $accent = $scheme_colors[4];
    $primary = $scheme_colors[1];

    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
    echo '<meta name="theme-color" content="' . $primary. '">';
    echo '<meta name="msapplication-navbutton-color" content="' . $primary. '">';
    echo '<meta name="apple-mobile-web-app-status-bar-style" content="' . $primary. '">';
    echo "<style>a { color: $primary} a:hover, a:active { color: $accent} .woocommerce div.product p.price,.woocommerce div.product span.price, .woocommerce div.product .stock, .woocommerce ul.products li.product .price, #add_payment_method .cart-collaterals .cart_totals .discount td,.woocommerce-cart .cart-collaterals .cart_totals .discount td,.woocommerce-checkout .cart-collaterals .cart_totals .discount td {color: $accent; } .woocommerce span.onsale { background-color: $accent }</style>";

});

add_filter( 'wp_theme_editor_filetypes', function( $allowed_types, $theme ) {
        $allowed_types[] = 'js';
        return $allowed_types;
}, 10, 2 );


add_action( 'login_enqueue_scripts', function() {
    if ( !has_custom_logo() ) return;
    
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    $logo = wp_get_attachment_image_src( $custom_logo_id , 'full' );
    if (!$logo) return;
    ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo $logo; ?>);
                height:60px;
                width:320px;
                background-size: contain;
                background-repeat: no-repeat;
                padding-bottom: 30px;
        }
    </style>
    <?php 
});

add_filter( 'login_headerurl', function() {
    return home_url();
});

