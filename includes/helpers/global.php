<?php

/**
 * Get all parents of a post
 * 
 * @param integer $id
 * @param array $parents
 * @return array
 */
function get_post_parents($id, &$parents = array()) {
    $parent_id = wp_get_post_parent_id($id);

    if ($parent_id === false) {
        return false;
    }

    $parents[] = $id;

    if (!empty($parent_id)) {
        get_post_parents($parent_id, $parents);
    }

    return true;
}

/**
 * A customized get_the_excerpt()
 */
function get_the_excerpt_ex($length = 800, $link = false) {
    $post = get_post();

    $excerpt     = '';
    $trim_needed = true;
    $more_needed = false;

    if (!empty($post->post_excerpt)) {
        $html = $post->post_excerpt;

        $trim_needed = false;
    }
    else {
        $html = $post->post_content;

        if (preg_match('/<!--more(.*?)?-->/', $html)) {
            $html = get_the_content('', true);

            $trim_needed = false;
        }
    }

    $text = strip_tags(strip_shortcodes($html));

    if ($length !== null && (int) $length > 10) {

        if ($trim_needed || strlen($text) > $length) {
            $letters = preg_split("//u", $text);

            for ($i = 1; $i <= $length; $i++) {

                if ($i >= count($letters)) {
                    break;
                }

                $excerpt .= $letters[ $i - 1 ];
            }

            if ($i < count($letters)) {
                $more_needed = true;

                if (($words = preg_split("/\s/", $excerpt)) !== false && count($words) > 1) {
                    array_pop($words);

                    $excerpt = implode(' ', $words);
                }

                if ($link === false) {
                    $excerpt .= '&hellip;';
                }
            }
        }
        else {
            $excerpt = $text;

            $more_needed = true;
        }
    }
    else {
        $excerpt = wp_trim_words($text);
    }

    if ($link !== false && $more_needed) {

        if ($link !== true && gettype($link) === 'string') {
            $excerpt_more = _theme_excerpt_readmore(null, $link);
        }
        else {
            $excerpt_more = apply_filters('excerpt_more', ' ' . '[&hellip;]');
        }

        $excerpt .= $excerpt_more;
    }

    return $excerpt;
}

function the_content_ex($post_id) {
    $post = get_post($post_id);
    $content      = $post->post_content;
    $content      = apply_filters('the_content', $content);
    $content      = str_replace(']]>', ']]&gt;', $content);
    echo $content;
}

/**
 * Limit string caracters
 */
function strlmt($str, $length) {
    $letters = preg_split("//u", $str);

    $result = '';

    for ($i = 1; $i < $length; $i++) {
        $result .= $letters[ $i ];
    }

    return $result;
}

/**
 * Title and a bit of Content of post
 */
function get_the_title_plus($length = 800, $link = false, $post_id = 0) {
    $text = strip_tags(get_the_title($post_id));
    $text .= '; ' . strip_tags(strip_shortcodes(get_the_content($post_id)));

    $excerpt     = '';
    $more_needed = false;

    if ($length !== null && (int) $length > 10) {
        $letters = preg_split("//u", $text);

        for ($i = 1; $i < $length; $i++) {
            $excerpt .= $letters[ $i ];
        }

        if ($i < count($letters)) {
            $more_needed = true;

            if ($link === false) {
                $excerpt .= '&hellip;';
            }
        }
    }
    else {
        $excerpt = wp_trim_words($text);
    }

    if ($link !== false && $more_needed) {

        if ($link !== true && gettype($link) === 'string') {
            $excerpt_more = _theme_excerpt_readmore(null, $link);
        }
        else {
            $excerpt_more = apply_filters('excerpt_more', ' ' . '[&hellip;]');
        }

        $excerpt .= $excerpt_more;
    }

    return $excerpt;
}

/**
 * add permalink to body classes to make friendly classes
 */
function _theme_body_friendly_class($classes) {
    global $post;

    $parents = array();

    if (!isset($post) || get_post_parents($post->ID, $parents) === false) {
        return $classes;
    }

    $parents = array_reverse($parents);

    $full_name = '';

    $new_list = array();

    foreach ($parents as $post_item) {
        $url = get_permalink($post_item);
        $tmp = explode('/', trim($url, '/'));

        $name = end($tmp);

        if ($name) {
            $word = explode('-', $name);

            if (count($word) > 5) {
                $word = array_slice($word, 0, 5);
            }

            $class = implode('-', $word);

            $full_name .= $class . '-';

            $new_list[] = $class;
        }
    }

    $new_list[] = trim($full_name, '-');

    $custom_classes = get_post_meta($post->ID, 'page_classes', true);

    if (!empty($custom_classes)) {
        $new_list = array_merge(explode(' ', strtolower($custom_classes)), $new_list);
    }

    $unique_list = array_unique($new_list);

    foreach ($unique_list as $cls) {
        $classes[] = $cls;
    }

    return $classes;
}

function make_restrict_manage_select($taxonomies, $title, $slug) {
    $html = '';
    $html .= '<select value="' . $slug . '">';
    $html .= "<option value=\"0\">" . __('Show All') . " {$title}</option>";
    foreach ($taxonomies as $term) {
        $html.= "<option value=\"{$term->slug}\">{$term->name}</option>";
    }
    $html .= '</select>';

    return $html;
}

/**
 * CUSTOM READ MORE LINK FOR POST
 */
function _theme_post_readmore() {
    return '<a class="read-more" href="' . get_permalink() . '">' . __('Read more', 'global_helper') . '</a>';
}

/**
 * CUSTOM READ MORE LINK FOR EXCERPT
 */
function _theme_excerpt_readmore($more = null, $format = null) {

    if (isset($format) && gettype($format) === 'string') {
        return sprintf($format, get_permalink());
    }

    return '... <a class="read-more" href="' . get_permalink() . '">' . __('Read more', 'global_helper') . '</a>';
}

/**
 * CUSTOM IMPLODE PHP FUNCTION
 * 
 * @since LRPH Main 1.0
 */
function implode_with_key($assoc, $inglue = '>', $outglue = ',') {
    $return = '';

    foreach ($assoc as $tk => $tv) {
        $return .= $outglue . $tk . $inglue . $tv;
    }

    return trim($return, $outglue);
}

/**
 * STANDARD HTML STYLES
 * 
 * @since LRPH Main 1.0
 */
function make_standard_html_style($style_str) {

    if (empty($style_str)) {
        return false;
    }

    $styles = explode(';', esc_attr($style_str));

    $clean_return_style = array();

    foreach ($styles as $item) {
        $style = explode(':', $item, 2);

        $clean_return_style[ trim($style[ 0 ]) ] = trim($style[ 1 ]);
    }

    return $clean_return_style;
}

/**
 * STYLE ARRAY TO STRING
 * 
 * @since LRPH Main 1.0
 */
function array_style($style_array) {

    if (count($style_array)) {
        $styles_str = implode_with_key($style_array, ':', '; ');

        $return_str = ' style="' . trim($styles_str) . '"';

        return $return_str;
    }
    else {
        return false;
    }
}

/**
 * HTML ELEMENT CLASS
 * 
 * @since LRPH Main 1.0
 */
function make_html_class_string($class_str) {
    $classes = explode(' ', esc_attr($class_str));

    if (!empty($classes)) {
        $class_str_return = '';

        foreach ($classes as $class) {
            $class_str_return .= $class . ' ';
        }

        return trim($class_str_return);
    }
    else {
        return false;
    }
}

/**
 * REMOVE DOMAIN NAME FROM URL
 */
function url_without_domain($url, $domain = null) {
    if (empty($url)) {
        return false;
    }

    if (empty($domain)) {
        $domain = get_home_url();
    }

    $tmp    = strtolower($url);
    $domain = strtolower($domain);

    $domain_pos = strpos($tmp, $domain);

    if ($domain_pos !== false && $domain_pos >= 0) {
        $url = substr($url, strlen($domain));
    }

    return $url;
}

/**
 * a customized version of wp_get_attachment_image_src
 */
function wp_get_attachment_image_src_ex($attachment_id, $size = 'thumbnail') {
    $img_url          = wp_get_attachment_url($attachment_id);
    $meta             = wp_get_attachment_metadata($attachment_id);
    $img_url_basename = wp_basename($img_url);

    if ($img_url === false) {
        return false;
    }


    if (!is_array($size)) {
        if (isset($meta[ 'sizes' ][ strtolower($size) ])) {
            $img_info = $meta[ 'sizes' ][ strtolower($size) ];
            return array( str_replace($img_url_basename, $img_info[ 'file' ], $img_url),
                $img_info[ 'width' ],
                $img_info[ 'height' ], false );
        }
        else {
            $img_info = array_pop($meta[ 'sizes' ]);
            return array( $img_url,
                $img_info[ 'width' ],
                $img_info[ 'height' ], false );
        }
    }
}

/**
 *  THE POST/PAGE HAS FEATURED IMAGE?
 */
function has_the_featured_image($post_id = null) {
    $attach_id = get_post_thumbnail_id($post_id);

    return (bool) wp_get_attachment_image_src_ex($attach_id);
}

/**
 * THE POST/PAGE FEATUERED IMAGE
 */
function get_the_featured_image($size = 'thumbnail', $post_id = null) {
    $attachment_id = get_post_thumbnail_id($post_id);
    $img_src       = wp_get_attachment_image_src_ex($attachment_id, $size);

    if ($img_src === false) {
        $return = apply_filters('the_featured_image', false, $attachment_id, $size, $post_id);
        return $return;
    }
    else {
        $return = apply_filters('the_featured_image', $img_src[ 0 ], $attachment_id, $size, $post_id);
        return $return;
    }
}

/**
 * GET FAMILIAR FILESIZE
 * 
 * @since LRPH Main 1.0
 */
function get_filesize_measured($size_in_bytes) {
    if ($size_in_bytes >= 1073741824) {
        $size_in_bytes = number_format($size_in_bytes / 1073741824, 2, '.', ',') . ' GB';
    }
    elseif ($size_in_bytes >= 1048576) {
        $size_in_bytes = number_format($size_in_bytes / 1048576, 1) . ' MB';
    }
    elseif ($size_in_bytes >= 1024) {
        $size_in_bytes = number_format($size_in_bytes / 1024, 0) . ' KB';
    }
    elseif ($size_in_bytes > 1) {
        $size_in_bytes = $size_in_bytes . ' bytes';
    }
    elseif ($size_in_bytes == 1) {
        $size_in_bytes = $size_in_bytes . ' byte';
    }
    else {
        $size_in_bytes = '0 bytes';
    }

    return $size_in_bytes;
}

/**
 * GET SINGLE TITLE: PRIORITY IN ORDER BY POST, PAGE, CATEGORY, SEARCH, 404
 */
function get_the_global_title($term_id = null) {

    if (is_404()) {
        return __('Error!', 'global_helper');
    }

    $query_obj = get_queried_object();

    $title = get_the_title($term_id);

    if (is_search()) {
        return __('Search result', 'global_helper');
    }

    if (is_tax()) {
        $tax = get_query_var('taxonomy');

        if (isset($query_obj)) {
            $tax_slug = $query_obj->slug;
        }

        $tax_objs = get_the_terms(get_the_ID(), $tax);

        if (isset($tax_objs) && !empty($tax_objs)) {

            foreach ($tax_objs as $obj) {

                if (isset($tax_slug) && $tax_slug === $obj->slug) {
                    return $obj->name;
                }
            }
        }
    }

    if (is_single() && get_post_type() !== 'post') {
        $title = __('Unknown', 'global_helper');

        $the_title = get_the_title($term_id);

        if (!empty($the_title)) {
            return $the_title;
        }
    }

    if (is_single() && get_post_type() === 'post') {
        $cat = get_the_category($term_id);

        if (isset($cat) && count($cat) > 0) {
            return $cat[ 0 ]->name;
        }
    }

    if (is_category()) {
        $cat = get_category(get_query_var('cat'));

        if (isset($cat)) {
            return $cat->name;
        }
    }

    return $title;
}

/**
 * Don't auto-p wrap shortcodes that stand alone
 *
 * Ensures that shortcodes are not wrapped in <<p>>...<</p>>.
 *
 * @since 2.9.0
 *
 * @param string $content The content.
 * @return string The filtered content.
 */
function shortcode_unautop_ex($content) {
    $new_content      = '';
    $pattern_full     = '{(\[raw\].*?\[/raw\])}is';
    $pattern_contents = '{\[raw\](.*?)\[/raw\]}is';
    $pieces           = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

    foreach ($pieces as $piece) {
        if (preg_match($pattern_contents, $piece, $matches)) {
            $new_content .= $matches[ 1 ];
        }
        else {
            $new_content .= wptexturize(wpautop($piece));
        }
    }

    return $new_content;
}

/**
 * GET TERMS IN LANGUAGE <!-- WPML IS REQUIRED -->
 */
function icl_object($id, $type, $slug = null, $language = null) {

    if (function_exists('icl_object_id')) {

        if ($language === null) {
            $language = icl_get_default_language();
        }

        $obj_id = icl_object_id($id, $type, true, $language);

        if (isset($obj_id) && !empty($obj_id)) {

            global $icl_adjust_id_url_filter_off;
            $icl_adjust_id_url_filter_off = true;

            switch ($type) {
                case 'category':

                    $obj = get_category($obj_id);

                    break;

                case 'taxonomy':

                    $obj = get_term_by('slug', $slug, 'the-team');

                    break;

                case 'post':

                    $obj = get_post($obj_id);

                    break;

                default:

                    $obj = get_term_by('id', $obj_id, $type);

                    break;
            }

            $icl_adjust_id_url_filter_off = false;

            if (isset($obj) && !empty($obj) && !is_wp_error($obj)) {

                return $obj;
            }
        }
    }

    return false;
}

/**
 * SITE MAP / BREADCRUMBS
 */
function get_site_map($location = 'main_menu') {

    if (!has_nav_menu($location)) {
        return false;
    }

    $menus = get_nav_menu_locations();

    if (isset($menus) && isset($menus[ $location ])) {

        $args = array(
            'order'      => 'ASC',
            'orderby'    => 'menu_order',
            //'post_type'              => 'nav_menu_item',
            //'post_status'            => 'publish',
            'output'     => ARRAY_A,
            'output_key' => 'menu_order',
        //'nopaging'               => true,
        //'update_post_term_cache' => false
        );

        $menu_items = wp_get_nav_menu_items($menus[ $location ], $args);

        $menu        = array();
        $menu_output = array();
        $breadcrumbs = '';

        foreach ($menu_items as $item) {
            $menu[ $item->ID ] = (array) $item;
        }

        sort_menu_items($menu, $menu_output);

        build_breadcrumbs($menu_output, $breadcrumbs);

        return $breadcrumbs;
    }
}

function sort_menu_items($menu_items, &$output) {

    foreach ($menu_items as $item) {

        if ($item[ 'menu_item_parent' ] == 0) {

            $output[ $item[ 'ID' ] ][ 0 ] = $item;
        }
        else
        if (!assign_parent_menu_item($item, $output)) {

            $output[ $item[ 'menu_item_parent' ] ][ $item[ 'ID' ] ][ 0 ] = $item;
        }
    }
}

function assign_parent_menu_item($child, & $menu_items) {
    foreach ($menu_items as $index => & $item) {

        if ($child[ 'menu_item_parent' ] == $index) {
            $item[ $child[ 'ID' ] ][ 0 ] = $child;
            return true;
        }

        if ($index === 0 || !isset($item[ 0 ])) {
            continue;
        }

        if (assign_parent_menu_item($child, $item)) {
            return true;
        }
    }

    return false;
}

function build_breadcrumbs($menu_items, & $output, $link_format = '<a%s>%s</a>', $splitter = '<span> > </span>') {

    $parents = array();

    if (is_page() && get_post_type() === 'page') {
        get_parents($parents, $menu_items, (int) get_the_ID());
    }
    else
    if (is_single()) {
        $parents[] = array(
            'type'      => 'single',
            'object_id' => get_the_ID(),
            'title'     => get_the_title(),
        );

        $cats = get_the_category();

        foreach ($cats as $cat) {
            if (get_parents($parents, $menu_items, (int) $cat->cat_ID)) {

                break;
            }
        }
    }
    else
    if (is_category() || is_tax()) {
        $query = get_queried_object();

        if (isset($query)) {
            $tax_id = $query->term_taxonomy_id;

            get_parents($parents, $menu_items, (int) $tax_id);
        }
    }
    else
    if (is_tag()) {
        $query = get_queried_object();
        if (isset($query)) {
            $tax_id = $query->term_taxonomy_id;

            get_parents($parents, $menu_items, (int) $tax_id);
        }
    }

    $parents   = apply_filters('build_breadcrumbs_parents', $parents);
    $parents[] = array(
        'type'  => 'custom',
        'link'  => home_url('/'),
        'title' => __('Home', 'global_helper'),
    );

    if (count($parents) > 0) {
        $parents = array_reverse($parents);

        for ($i = 0; $i < count($parents); $i++) {

            $current   = $parents[ $i ];
            $term_link = isset($current[ 'link' ]) ? $current[ 'link' ] : null;

            switch ($current[ 'type' ]) {
                case 'custom':

                    break;

                case 'taxonomy':

                    $term_link = get_term_link((int) $current[ 'object_id' ], $current[ 'object' ]);
                    break;

                default:

                    $term_link = get_permalink((int) $current[ 'object_id' ]);
                    break;
            }

            if (gettype($term_link) === 'string') {
                $url_current = ' href="' . $term_link . '"';
            }
            else {
                $url_current = ' class="no-link" href="javascript:void(0)"';
            }

            $title = $current[ 'title' ];

            $output .= sprintf($link_format, $url_current, $title);

            if ($i < count($parents) - 1) {
                $output .= $splitter;
            }
        }
    }
    elseif (!(is_front_page() || is_home())) {
        $title = get_the_global_title();

        $output .= sprintf($link_format, ' class="no-link" href="javascript:void(0)"', $title);
    }
}

/**
 * Find an id within menu items and build an array of the item's parents
 *  
 * @param array This will be output
 * @param array List of menu items to search in
 * @param int The ID of object to search for
 * @return boolean True if it finds any parents, or the item
 */
function get_parents(& $output, $items, $id) {

    foreach ($items as $index => $item) {

        if (isset($item[ 0 ]) && count($item) > 1) {

            if (get_parents($output, $item, $id)) {

                $output[] = $item[ 0 ];

                return true;
            }

            continue;
        }

        if ($index === 0) {
            continue;
        }

        $item_id = (int) $item[ 0 ][ 'object_id' ];

        /* switch ($item[ 0 ][ 'type' ]) {
          case 'taxonomy':

          $item_id = (int) $item[ 0 ][ 'object_id' ];
          break;

          default:
          $item_id = (int) $item[ 0 ][ 'object_id' ];
          break;
          } */

        if ($item_id === $id) {

            $output[] = $item[ 0 ];
            return true;
        }
    }

    return false;
}

/**
 * Load a template part into a template
 *  
 * @param Array $template_slugs An array of possible template parts.
 */
function get_template_parts($template_slugs) {

    foreach ($template_slugs as & $slug) {
        do_action("get_template_part_{$slug}", $slug);

        $slug .= '.php';
    }

    locate_template($template_slugs, true, false);
}

/**
 * Get an object of social share
 */
function get_social_share($socials = '', $customize = array(), $post_id = null) {

    $list = array_merge(
    array(
        'facebook'    => array(
            'title'  => 'Facebook',
            'url'    => '//www.facebook.com/sharer.php?u=%1$s',
            'icon'   => 'facebook',
            'target' => '_blank',
        ),
        'twitter'     => array(
            'title'  => 'Twitter',
            'url'    => '//twitter.com/share?url=%1$s',
            'icon'   => 'twitter',
            'target' => '_blank',
        ),
        'google-plus' => array(
            'title'  => 'Google +1',
            'url'    => '//plusone.google.com/_/+1/confirm?hl=en&url=%1$s',
            'icon'   => 'google-plus',
            'target' => '_blank',
        ),
        'email'       => array(
            'title'  => 'Email',
            'url'    => 'mailto:?subject=%2$s&amp;body=%3$s %1$s',
            'icon'   => 'envelope',
            'target' => '',
        ),
        'print'       => array(
            'title'  => 'Print',
            'url'    => '',
            'icon'   => 'print',
            'target' => '',
        ),
    ), $customize);

    $selection_list = explode(' ', $socials);

    if (count($selection_list) > 0) {

        $selection = array();

        foreach ($selection_list as $value) {
            if (array_key_exists($value, $list)) {
                $selection[ $value ] = $list[ $value ];
            }
        }

        $list = $selection;
    }

    foreach ($list as $id => & $item) {

        $item = apply_filters('pre_social_share', $item, $id);

        if (preg_match('/(%(\d+\$)?s)/is', $item[ 'url' ])) {
            $item[ 'url' ] = sprintf($item[ 'url' ], get_the_permalink($post_id), get_the_title($post_id),
            get_the_excerpt_ex(100, false, $post_id));
        }
    }

    return $list;
}

function curl($options, &$result) {
    $ch = curl_init();

    $args = array_replace_recursive(array(
        CURLOPT_HEADER         => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (iPhone; CPU iPhone OS 7_0 like Mac OS X) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11A465 Safari/9537.53',
    ), $options);

    $args[ CURLINFO_HEADER_OUT ] = true;

    curl_setopt_array($ch, $args);

    $data = curl_exec($ch);

    $result = array(
        'error'    => curl_error($ch),
        'errno'    => curl_errno($ch),
        'info'     => (array) curl_getinfo($ch),
        'response' => $data,
    );

    curl_close($ch);

    return $result[ 'errno' ] === 0;
}

/*
 * Replacement for get_adjacent_post()
 *
 * This supports only the custom post types you identify and does not
 * look at categories anymore. This allows you to go from one custom post type
 * to another which was not possible with the default get_adjacent_post().
 * Orig: wp-includes/link-template.php 
 * 
 * @param string $direction: Can be either 'prev' or 'next'
 * @param multi $post_types: Can be a string or an array of strings
 */

function get_adjacent_post_ex($direction = 'prev', $post_types = 'post') {
    global $post, $wpdb;

    if (empty($post))
        return NULL;
    if (!$post_types)
        return NULL;

    if (is_array($post_types)) {
        $txt = '';
        for ($i = 0; $i <= count($post_types) - 1; $i++) {
            $txt .= "'" . $post_types[ $i ] . "'";
            if ($i != count($post_types) - 1)
                $txt .= ', ';
        }
        $post_types = $txt;
    } else {
        $post_types = "'$post_types'";
    }

    $current_post_date = $post->post_date;

    $join                = '';
    $in_same_cat         = FALSE;
    $excluded_categories = '';
    $adjacent            = $direction == 'prev' ? 'previous' : 'next';
    $op                  = $direction == 'prev' ? '<' : '>';
    $order               = $direction == 'prev' ? 'DESC' : 'ASC';

    $join  = apply_filters("get_{$adjacent}_post_join", $join, $in_same_cat, $excluded_categories);
    $where = apply_filters("get_{$adjacent}_post_where",
    $wpdb->prepare("WHERE p.post_date $op %s AND p.post_type IN({$post_types}) AND p.post_status = 'publish'",
    $current_post_date), $in_same_cat, $excluded_categories);
    $sort  = apply_filters("get_{$adjacent}_post_sort", "ORDER BY p.post_date $order LIMIT 1");

    $query     = "SELECT p.* FROM $wpdb->posts AS p $join $where $sort";
    $query_key = 'adjacent_post_' . md5($query);
    $result    = wp_cache_get($query_key, 'counts');
    if (false !== $result)
        return $result;

    $result = $wpdb->get_row("SELECT p.* FROM $wpdb->posts AS p $join $where $sort");
    if (null === $result)
        $result = '';

    wp_cache_set($query_key, $result, 'counts');
    return $result;
}
