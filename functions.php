<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

add_action( 'wp_enqueue_scripts', function() {

    $parent_style = 'hello-elementor';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );

    $colors = (get_option('elementor_disable_color_schemes') != "yes");
    $typo = (get_option('elementor_disable_typography_schemes') != "yes");

    if ($colors && $typo) {
        wp_enqueue_style( 'ran-style',
            get_stylesheet_directory_uri() . '/style.css',
            array( $parent_style ),
            filemtime(__DIR__.'/style.css')
        );
    }

    wp_enqueue_script(
        'ran-script',
        get_stylesheet_directory_uri() . '/scripts.js',
        array( 'jquery' ),
        filemtime(__DIR__.'/scripts.js')
    );

    foreach(glob(__DIR__."/functions.d/*.css") as $include) {
	    wp_enqueue_style( 'ran-style-'.basename($include),
		get_stylesheet_directory_uri() . '/functions.d/'.basename($include),
		array( $parent_style, 'ran-style' ),
		filemtime($include)
	    );
    }
    foreach(glob(__DIR__."/functions.d/*.js") as $include) {
	    wp_enqueue_script(
		'ran-script-'.basename($include),
		get_stylesheet_directory_uri() . '/functions.d/'.basename($include),
		array( 'jquery', 'ran-script' ),
		filemtime($include)
	    );
    }


});


add_action('wp_head', function() {
    $colors = (get_option('elementor_disable_color_schemes') != "yes");

    if ($colors) {
        $scheme_colors = get_option('elementor_scheme_color');

        $accent = $scheme_colors[4];
        $primary = $scheme_colors[1];
        $secondary = $scheme_colors[2];
        $text = $scheme_colors[3];


        echo '<meta name="theme-color" content="' . $primary. '">';
        echo '<meta name="msapplication-navbutton-color" content="' . $primary. '">';
        echo '<meta name="apple-mobile-web-app-status-bar-style" content="' . $primary. '">';

        echo "<style>:root {
            --elementor-color-accent: $accent;
            --elementor-color-primary: $primary;
            --elementor-color-secondary: $secondary;
            --elementor-color-text: $text;
        }</style>";
    }


    $typo = (get_option('elementor_disable_typography_schemes') != "yes");
    if ($typo) {
        $typography = get_option('elementor_scheme_typography');

        echo "<style>:root {
            --elementor-typo-family-header: '".$typography[1]['font_family']."';
            --elementor-typo-family-secondary: '".$typography[2]['font_family']."';
            --elementor-typo-family-body: '".$typography[3]['font_family']."';
            --elementor-typo-family-accent: '".$typography[4]['font_family']."';

            --elementor-typo-weight-header: ".$typography[1]['font_weight'].";
            --elementor-typo-weight-secondary: ".$typography[2]['font_weight'].";
            --elementor-typo-weight-body: ".$typography[3]['font_weight'].";
            --elementor-typo-weight-accent: ".$typography[4]['font_weight'].";
        }</style>";
    }


});

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style('font-awesome');
}, 100);

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
            background-image: url(<?php echo $logo[0]; ?>);
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

foreach(glob(__DIR__."/functions.d/*.php") as $include) {
	require_once($include);
}

