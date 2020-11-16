<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

function ran_elementor_data() {
	$id = get_option('elementor_active_kit');
	$colors = [
		'primary' => '',
		'secondary' => '',
		'text' => '',
		'accent' => '',
	];
	$fonts = [];
	if (!$id) {
		$scheme_colors = get_option('elementor_scheme_color');
    	$colors = [
			'primary' => $scheme_colors[1],
			'secondary' => $scheme_colors[2],
			'text' => $scheme_colors[3],
			'accent' => $scheme_colors[4],
		]; ;
	} else {
		$data = get_post_meta($id, '_elementor_page_settings', true);
		foreach($data['system_colors'] as $color) {
			$colors[$color['_id']] = $color['color'];
		}
		foreach($data['custom_colors'] as $color) {
			$colors[$color['_id']] = $color['color'];
		}
	}
	
	return [
		'colors' => $colors,
		'fonts' => $fonts
	];
}

add_action( 'wp_enqueue_scripts', function() {

    $parent_style = 'hello-elementor';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );

    wp_enqueue_style( 'ran-style',
        get_stylesheet_directory_uri() . '/style.css',
        $parent_styles,
        filemtime(__DIR__.'/style.css')
    );
    $parent_styles[]= 'ran-style';
  

    wp_enqueue_script(
        'ran-script',
        get_stylesheet_directory_uri() . '/scripts.js',
        array( 'jquery' ),
        filemtime(__DIR__.'/scripts.js')
    );

    foreach(glob(__DIR__."/functions.d/*.css") as $include) {
	    wp_enqueue_style( 'ran-style-'.basename($include),
		get_stylesheet_directory_uri() . '/functions.d/'.basename($include),
		$parent_styles,
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
  
    $data  = ran_elementor_data();
	
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>';
    echo '<meta name="theme-color" content="' . $data['colors']['primary']. '">';
    echo '<meta name="msapplication-navbutton-color" content="' . $data['colors']['primary']. '">';
    echo '<meta name="apple-mobile-web-app-status-bar-style" content="' . $data['colors']['primary']. '">';

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

