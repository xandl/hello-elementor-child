<?php


function ran_handle_gfonts($src) {
    
    $transient_key = "ongema_webfonts_heroku";
    if (false === ($font_list = get_transient($transient_key))) {
        $api_url = 'https://google-webfonts-helper.herokuapp.com/api/fonts';
        $json_list = json_decode(file_get_contents($api_url));
        $font_list = [];
        foreach ($json_list as $font) {
            $font_list[$font->family] = $font;
        }
        set_transient($transient_key, $font_list);
    }
    $gpath = ABSPATH . '/wp-content/uploads/gfonts/';
    if (!file_exists($gpath)) {
        mkdir($gpath, 0777, true);
    }

    $targetcss = $gpath . md5($src) . '.css';
    if (file_exists($targetcss)) {
        return str_replace(ABSPATH, '', $targetcss);
    }

    $parts = parse_url($src);
    parse_str($parts['query'], $data);

    $families = explode('|', $data['family']);


    $css = "";

    foreach ($families as $family) {
        list($family, $sizes) = explode(':', $family);
        $font = $font_list[$family];

        $subsets = array_intersect(['latin-ext', 'latin'], $font->subsets);
        $sizes = $font->variants;

        $basename = $font->id . '-' . $font->version . '-' . implode('_', $subsets);
        $download = 'https://google-webfonts-helper.herokuapp.com/api/fonts/' . rawurlencode($font->id) . '?download=zip&subsets=' . implode(',', $subsets) . '&variants=' . implode(',', $sizes);


        $targetdir = $gpath . $basename . '/';
        if (!file_exists($targetdir)) {
            mkdir($targetdir, 0777, true);
        }
        $zip = $targetdir . '/' . $basename . '.zip';
        if (!file_exists($zip)) {
            $content = file_get_contents($download);
            file_put_contents($zip, $content);
            $archive = new ZipArchive();
            $archive->open($zip);
            $archive->extractTo($targetdir);
        }

        $targeturl = str_replace(ABSPATH, '', $targetdir);

        foreach ($sizes as $size) {
            $font_style = 'normal';
            $font_weight = $size;
            if (strpos($size, 'italic') !== false) {
                $font_style = 'italic';
                $font_weight = str_replace('italic', '', $size) ?: '400';
            }
            $font_css = "
                @font-face {
                    font-family: '" . $font->family . "'; 
                    font-style: " . $font_style . ";
                    font-weight: " . $font_weight . "; 
                    src: url('" . $targeturl . "-" . $font_weight . ".eot'); /* IE9 Compat Modes */
                ";
            $font_css .= " src: local('') \n";
            foreach (['eot' => 'embedded-opentype', 'svg' => 'svg', 'ttf' => 'truetype', 'woff' => 'woff', 'woff2' => 'woff2'] as $fileending => $format) {
                $font_file = $targetdir . $basename . '-' . $size . '.' . $fileending;

                if (file_exists($font_file)) {
                    $font_url = str_replace(ABSPATH, '', $font_file);
                    if ($fileending == "eot") {
                        $font_url .= '?#iefix';
                    } elseif ($fileending == "svg") {
                        $font_url .= '#' . $font->family;
                    }
                    $font_css .= ", url('" . $font_url . "') format('" . $format . "') \n";
                }
            }
            $font_css .= "; \n";
            $font_css .= "} \n";

            $css .= $font_css;
        }
    }
    file_put_contents($targetcss, $css);

    return str_replace(ABSPATH, '', $targetcss);
}


add_action( 'wp_print_styles',  function() {
    /** @var WP_Styles $wp_styles */
    global $wp_styles;
  
  
    foreach ($wp_styles->queue as $handle) {
      /** @var _WP_Dependency $reg_handle */
      $reg_handle = $wp_styles->registered[$handle];
  
      if (strpos($reg_handle->src, '/fonts.googleapis.com/') === false) continue;
  
      $wp_styles->registered[$handle]->src = ran_handle_gfonts($reg_handle->src);
    }
  
  });
