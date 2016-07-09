<?php

/**
 * Amin Paks website theme
 *
 * @package WordPress
 * @author Amin Paks <http://www.aminpaks.com>
 * @version 1.0
 */
/* * ==========================================================================* */


/**
 * INCLUDES
 */
require_once TEMPLATEPATH . '/includes/includes.php';

/**
 * SETUP
 */
function _theme_setup() {

    add_theme_support('post-thumbnails');

    //add_theme_support( 'title-tag' );

    register_nav_menus(array(
        'main_menu' => 'Main Menu',
    //'footer_menu' => 'Footer Menu'
    ));

    // Registering scripts
    add_action('wp_enqueue_scripts', '_theme_register_styles_scripts');

    // Registering admin scripts & styles
    add_action('admin_enqueue_scripts', '_theme_admin_register_styles_scripts');
}

/**
 * ADMIN STYLE & SCRIPTS
 */
function _theme_admin_register_styles_scripts() {
    if (is_admin()) {
        wp_register_script('admin-media-upload', get_template_directory_uri() . '/assets/admin.global.min.js',
        array(
            'jquery', 'media-upload', 'thickbox' ));

        wp_enqueue_script('admin-media-upload');
    }
}

/**
 * STYLES & SCRIPTS
 */
function _theme_register_styles_scripts() {
    //global $localize_object;

    if (!WP_DEBUG && !is_admin()) {
        wp_deregister_script('jquery-migrate');
        wp_deregister_script('jquery-core');
        wp_deregister_script('jquery');
    }

    wp_enqueue_style('main-font', '//fonts.googleapis.com/css?family=Raleway:300,400,500,600');

    wp_register_style('global-styles', get_template_directory_uri() . '/assets/global.css', array( 'main-font' ));

    if (!wp_script_is('jquery', 'registered')) {
        wp_register_script('handler', get_template_directory_uri() . '/assets/global.full.js');
    }
    else {
        wp_register_script('handler', get_template_directory_uri() . '/assets/global.min.js', array( 'jquery' ));
    }

    wp_enqueue_script('handler');
    wp_enqueue_style('global-styles');
}

function _theme_register_script_modification($tag, $handle/* , $src */) {
    if ($handle != 'handler') {
        return $tag;
    }

    $matches = array();

    if (preg_match('/<script\b([^>]*)>(.*?)<\/script>/', $tag, $matches)) {
        $tag = sprintf('<script async="async"%s></script>', $matches[ 1 ]);
    }
    //var_dump($matches);

    return $tag;
}

/**
 * SIDEBARS
 *
 * @since LRPH Main 1.0
 */
function _theme_widget_placeholder_init() {
    register_sidebar(array(
        'name'          => 'Header Sidebar',
        'description'   => 'Appears on Top of all pages in the Header.',
        'id'            => 'header-sidebar',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<div class="title"><h3>',
        'after_title'   => '</h3></div>',
    ));
    register_sidebar(array(
        'name'          => 'Post Sidebar',
        'description'   => 'These widgets will appear at right of all posts and pages.',
        'id'            => 'post-sidebar',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<div class="title"><h3>',
        'after_title'   => '</h3></div>',
    ));
    register_sidebar(array(
        'name'          => 'Footer Copyright',
        'description'   => 'Footer copyright placeholder.',
        'id'            => 'footer-copyright',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<div class="title"><h3>',
        'after_title'   => '</h3></div>',
    ));
}

function _theme_user_widget_placeholder_init() {
    $opt = _theme_options::get_instance();

    $user_sidebars = $opt->get_option('sidebar', 'template');

    $sidebar_list = explode(',', $user_sidebars);

    foreach ($sidebar_list as $sidebar) {
        $sidebar = trim($sidebar);

        if (empty($sidebar)) {
            continue;
        }

        $id = 'user_sidebar_' . strtolower($sidebar);

        register_sidebar(array(
            'name'          => $sidebar . ' Sidebar (Removable)',
            //'description'   => 'Footer copyright placeholder.',
            'id'            => $id,
            'before_widget' => '<div class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<div class="title"><h3>',
            'after_title'   => '</h3></div>',
        ));
    }
}

/**
 * REWRITE RULES
 */
function _theme_rewrite_rules() {
    /*
      add_rewrite_rule(
      'the-team/([^/]+)/?$', 'index.php?_team_member_role=$matches[1]', 'top');

      add_filter('query_vars', function( $vars ) {
      $vars[] = 'template_type';
      return $vars;
      });

      add_rewrite_rule(
      "announcements/([0-9]{4})/([0-9]{1,2})/([^/]+)(/[0-9]+)?/?$", 'index.php?template_type=announcements&year=$matches[1]&monthnum=$matches[2]&name=$matches[3]&page=$matches[4]', "top");

      add_rewrite_rule(
      'announcements/page/?([0-9]{1,})/?', 'index.php?category_name=announcements&paged=$matches[1]&template_type=announcements', 'top');

      add_rewrite_rule(
      'announcements/?', 'index.php?category_name=announcements&template_type=announcements', 'top');

      add_rewrite_rule(
      "([^/]+)/([0-9]{4})/([0-9]{1,2})/([^/]+)(/[0-9]+)?/?$", 'index.php?template_type=$matches[1]&year=$matches[2]&monthnum=$matches[3]&name=$matches[4]&page=$matches[5]', "top");

      add_rewrite_rule(
      "news/([^/]+)/([0-9]{4})/([0-9]{1,2})/([^/]+)(/[0-9]+)?/?$", 'index.php?template_type=$matches[1]&year=$matches[2]&monthnum=$matches[3]&name=$matches[4]&page=$matches[5]', "top");

      add_rewrite_rule(
      'media/page/?([0-9]{1,})/?', 'index.php?pagename=mediapage/?([0-9]{1,})/?', 'top');

      add_rewrite_rule(
      "faqs/([^/]+)/?$", 'index.php?_faq_post=$matches[1]', "top");

      /*
      add_rewrite_rule(
      'media/([^/]+)/page/?([0-9]{1,})/?',
      'index.php?page=$matches[1]&feed=$matches[2]',
      'top');

      add_rewrite_rule(
      'media/page/?([0-9]{1,})/?',
      'index.php?pagename=media&feed=$matches[1]',
      'top');

     */
}

function _theme_pre_get_posts($query) {
    if ($query->is_main_query() && $query->is_tax()) {
        $query->set('order', 'ASC');
        $query->set('orderby', 'menu_order');
        $query->set('posts_per_page', -1);
    }
    else
    if (!$query->is_admin && $query->is_search) {
        //post', 'page', 'attachment', '_team_member', '_faq_post', '_quote_post', '_media_post'
        $query->set('post_type', array( 'post', 'page', '_team_member' )); // id of page or post
    }
    return $query;
}

/**
 * ADMIN SETTING INITIALIZATION
 */
function _theme_options_init() {
    $opt = _theme_options::get_instance();

    $opt->add_section('template', 'Template Options');

    $opt->add_field('page_title', 'Page Title', '', 'text', 'template',
    array(
        //'placeholder' => '[sidebar-1 [, sidebar-2] [, sidebar-3]]...',
        'class' => 'regular-text'
    ));

    $opt->add_field('sidebar', 'Sidebars', '', 'text', 'template',
    array(
        'placeholder' => '[sidebar-1 [, sidebar-2] [, sidebar-3]]...',
        'class'       => 'regular-text'
    ));

    $opt->add_field('analytics', 'Google Analytics', '', 'textarea', 'template',
    array(
        'placeholder' => 'Paste your google analytics javascript snippet code here without <script> tag.',
        'class'       => 'large-text code',
        'rows'        => 10,
        'cols'        => 50
    ));

    $opt->add_section('contact', 'Contact Information');

    $opt->add_field('linkedin', 'LinkedIn Link', '', 'text', 'contact',
    array(
        'placeholder' => 'http://www.linkedin.com/account',
        'class'       => 'regular-text',
    ),
    array(
        'tagname' => 'a',
        'text'    => 'LinkedIn',
        'attrs'   => array( 'target' => '_blank' ),
    ));

    $opt->add_field('twitter', 'Twitter Link', '', 'text', 'contact',
    array(
        'placeholder' => 'https://twitter.com/account',
        'class'       => 'regular-text',
    ),
    array(
        'tagname' => 'a',
        'text'    => 'Twitter',
        'attrs'   => array( 'target' => '_blank' ),
    ));

    $opt->add_field('facebook', 'Facebook Link', '', 'text', 'contact',
    array(
        'placeholder' => 'https://facebook.com/account',
        'class'       => 'regular-text',
    ),
    array(
        'tagname' => 'a',
        'text'    => 'Facebook',
        'attrs'   => array( 'target' => '_blank' ),
    ));

    $opt->add_field('google_plus', 'Google+ Link', '', 'text', 'contact',
    array(
        'placeholder' => 'https://google.com/plus/account',
        'class'       => 'regular-text',
    ),
    array(
        'tagname' => 'a',
        'text'    => 'Google Plus',
        'attrs'   => array( 'target' => '_blank' ),
    ));

    $opt->add_field('email', 'Email Addresses', '', 'textarea', 'contact',
    array(
        'placeholder' => esc_attr('<a href="sales@domain-address.com">Sales</a> [ <a href="support@domain-address.com">Support</a> ...]'),
        'class'       => 'large-text code',
        'rows'        => 10,
        'cols'        => 50
    ));

    $opt->add_field('phone_no', 'Phone Numbers', '', 'textarea', 'contact',
    array(
        'placeholder' => '<span>Trademark: +1 (999) 555 1122</span> [ <span>Branch#2: +1 (888) 333 9977</span> ...]',
        'class'       => 'large-text code',
        'rows'        => 10,
        'cols'        => 50
    ));
}

/**
 * BODY CLASS
 */
function _theme_wp_title($title, $sep, $seplocation) {
    $main_title = false;

    if (class_exists('_theme_options')) {
        $opt        = _theme_options::get_instance();
        $main_title = $opt->get_option('page_title', 'template');
    }

    $title = trim(str_replace($sep, '', $title));

    if (!$title) {
        $title = 'Home';
    }

    $title = sprintf('%s %s %s', $title, $sep, $main_title);

    return $title;
}

/**
 * BODY CLASS
 */
function _theme_body_class($classes) {

    foreach ($classes as $k => $i) {
        if (strpos($i, '-_') !== false) {
            $classes[ $k ] = str_replace('-_', '-', $i);
        }
    }

    foreach ($classes as $idx => $class) {
        if (strpos($class, 'page-') !== false || strpos($class, 'postid') !== false) {
            unset($classes[ $idx ]);
        }
    }

    if (in_array('search-results', $classes) && strtolower(get_query_var('post_type')) === 'post') {
        $classes[] = 'search-blog-posts';
    }

    if (defined('ICL_LANGUAGE_CODE')) {
        $classes = array_merge(array( 'language-' . ICL_LANGUAGE_CODE ), $classes);
    }

    $extra_classes = get_post_meta(get_the_ID(), '_theme_post_extra_class', true);

    if (!empty($extra_classes)) {
        $new_classes = explode(' ', $extra_classes);

        $classes = array_merge($new_classes, $classes);
    }

    $cat_name = get_query_var('category_name');

    if (!empty($cat_name)) {

        $classes[] = $cat_name;
        $classes[] = 'category-' . $cat_name;

        $cat = get_category_by_slug($cat_name);


        if (isset($cat) && !empty($cat) && function_exists('icl_object_id')) {
            $cat_obj = icl_object($cat->term_id, $cat->taxonomy);

            $classes[] = $cat_obj->slug;
            $classes[] = 'category-' . $cat_obj->slug;
        }
    }

    $query = get_queried_object();

    if (isset($query) && property_exists($query, 'slug') && property_exists($query, 'taxonomy')) {

        $term_obj = icl_object($query->term_taxonomy_id, $query->taxonomy, $query->slug, 'en');

        if ($term_obj !== false) {
            $classes[] = $term_obj->slug;
        }
    }

    return $classes;
}

/**
 * THE FEATURED IMAGE
 */
function _theme_featured_image($img_src) {

    if ($img_src === false) {
        $img_src = get_template_directory_uri() . '/assets/image/noimage.png';
    }

    $home_url = home_url('/');

    if (strpos($img_src, $home_url) !== false) {
        $img_src = str_replace($home_url, '/', $img_src);
    }

    return $img_src;
}

/**
 * CUSTOMIZE BREADCRUMBS
 */
function _theme_breadcrumbs_parents($parents) {
    if (is_page() && count($parents) < 1) {
        $parents[] = array(
            'type'      => 'page',
            'object_id' => get_the_ID(),
            'title'     => get_the_title(),
        );
    }
    elseif (is_single() && get_post_type() === '_client') {
        $parents[] = array(
            'type'      => 'page',
            'object_id' => 55,
            'title'     => get_the_title(55),
        );
    }
    elseif (is_tag()) {
        $query = get_queried_object();
        if (isset($query)) {
            $parents[] = array(
                //'type'      => 'taxonomy',
                'type'  => 'custom',
                //'object_id' => $query->term_taxonomy_id,
                //'object'    => $query->taxonomy,
                'title' => sprintf(__('Tag [%s]', 'theme'), $query->name),
            );
        }
        $blog_id = get_option('page_for_posts');
        if ($blog_id) {
            $parents[] = array(
                'type'      => 'page',
                'object_id' => $blog_id,
                'title'     => get_the_title($blog_id),
            );
        }
    }
    elseif (is_tax() && get_query_var('taxonomy') === 'work-tag') {
        $query = get_queried_object();
        if (isset($query)) {
            $parents[] = array(
                //'type'      => 'taxonomy',
                'type'  => 'custom',
                //'object_id' => $query->term_taxonomy_id,
                //'object'    => 'work-tag',
                'title' => sprintf(__('Tag [%s]', 'theme'), $query->name),
            );
        }
        $parents[] = array(
            'type'      => 'page',
            'object_id' => 55,
            'title'     => get_the_title(55),
        );
    }
    elseif (is_home() && ($blog_id = get_option('page_for_posts')) !== false) {
        $parents[] = array(
            'type'      => 'page',
            'object_id' => $blog_id,
            'title'     => get_the_title($blog_id),
        );
    }
    elseif (is_search()) {
        $parents[] = array(
            'type'  => 'custom',
            'title' => __('Search results', 'theme'),
        );
    }
    return $parents;
}

function _theme_wp_nav_menu_items_mod($item_output, $item, $depth, $args) {

    /* $atts           = array();
      $atts[ 'title' ]  = !empty($item->attr_title) ? $item->attr_title : '';
      $atts[ 'target' ] = !empty($item->target) ? $item->target : '';
      $atts[ 'rel' ]    = !empty($item->xfn) ? $item->xfn : '';
      $atts[ 'href' ]   = !empty($item->url) ? $item->url : '';

      $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

      $attributes = '';
      foreach ($atts as $attr => $value) {
      if (!empty($value)) {
      $value = ( 'href' === $attr ) ? esc_url($value) : esc_attr($value);
      $attributes .= ' ' . $attr . '="' . $value . '"';
      }
      }

      $link_title = apply_filters('the_title', $item->title, $item->ID);// */

    /* return sprintf('<a%2$s><span class="item-text">%1$s</span><span aria-hidden="true" class="item-3d"><span class="item-3d-front">%1$s</span><span class="item-3d-back">%1$s</span></span></a>',
      $link_title, $attributes); // */

    if (in_array('loading', $item->classes)) {
        return '<img src="' . get_template_directory_uri() . '/assets/image/loading.gif" alt="Loading" >';
    }

    return $item_output;
}

function _theme_wp_nav_menu_items($menu) {

    /*
     * <ul class="sub-menu submenu-languages"><li class="menu-item menu-item-language menu-item-language-current"><a href="http://roiv2.ihome.com/fr/">Français</a></li><li class="menu-item menu-item-language menu-item-language-current"><a href="http://roiv2.ihome.com/de/">Deutsch</a></li><li class="menu-item menu-item-language menu-item-language-current"><a href="http://roiv2.ihome.com/ja/">日本語</a></li></ul>
     */

    $match = array();

    preg_match_all('/(<li\s+class="[\w\s-]*menu-item-language[^>]*>.*?<\/li>)/is', $menu, $match);

    foreach ($match[ 1 ] as $lang) {
        $delete = (strpos($lang, '/de/') !== false);

        if ($delete === true) {
            $menu = str_replace($lang, '', $menu);
        }
    }
    unset($lang);
    unset($delete);
    unset($match);

    return $menu;
}

function _theme_nav_menu_css_class($classes, $item) {

    if ((is_single() && get_post_type() === '_client') || (is_tax() && get_query_var('taxonomy') === 'work-tag')) {
        if (in_array('clients', $classes)) {
            $classes[] = 'current_page_parent';
        }
        elseif (($idx = array_search('current_page_parent', $classes)) !== false) {
            //var_dump($idx);
            unset($classes[ $idx ]);
            unset($idx);
        }
    }

    unset($item);

    return $classes;
}

/**
 * HANDLER FOR AJAX REQUESTS
 */
function _theme_ajax_global() {

    $post = filter_input_array(INPUT_POST);

    if (!isset($post[ 'id' ])) {
        echo json_encode(array(
            'result' => 0,
            'error'  => 'NO_ID'
        ));

        exit;
    }

    exit; //MUST EXECUTE 'EXIT' COMMAND
}

/**
 * AJAX ACTIONS
 */
add_action('wp_ajax_global', '_theme_ajax_global');
add_action('wp_ajax_nopriv_global', '_theme_ajax_global');

/**
 * ACTIONS
 */
add_action('init', '_theme_rewrite_rules');
add_action('init', '_theme_options_init');
add_action('init', '_theme_user_widget_placeholder_init');
//add_action('admin_menu', '_theme_options_admin_init');

add_action('after_setup_theme', '_theme_setup');
add_action('widgets_init', '_theme_widget_placeholder_init');

//add_action('admin_init', '_theme_admin_setting');


/**
 * FILTERS
 */
remove_filter('the_content', 'wpautop');
remove_filter('the_content', 'wptexturize');

add_filter('the_content', 'shortcode_unautop_ex', 99);

add_filter('wp_title', '_theme_wp_title', 10, 3);
add_filter('body_class', '_theme_body_class');
//add_filter('the_content_more_link', '_theme_post_readmore');
//add_filter('excerpt_more', '_theme_excerpt_readmore');
//add_filter('the_content', 'lrph_main_magic_content_class');
//add_filter('posts_where', '_theme_filter_query');
//add_filter('pre_get_posts', '_theme_pre_get_posts');
//add_filter('post_link', '_theme_post_link', 10, 3);
//add_filter('post_type_link', '_theme_post_type_link', 10, 3);
add_filter('the_featured_image', '_theme_featured_image', 10, 4);
add_filter('build_breadcrumbs_parents', '_theme_breadcrumbs_parents');
//add_filter('pre_social_share', '_theme_social_share', 10, 2);
add_filter('wp_nav_menu_items', '_theme_wp_nav_menu_items');
add_filter('walker_nav_menu_start_el', '_theme_wp_nav_menu_items_mod', 10, 4);
add_filter('nav_menu_css_class', '_theme_nav_menu_css_class', 10, 2);
add_filter('script_loader_tag', '_theme_register_script_modification', 10, 3);

/**
 * WIDGETS
 */
include_once 'includes/widgets/trademark.php';
include_once 'includes/widgets/newsletter.php';
include_once 'includes/widgets/latest_posts.php';
include_once 'includes/widgets/more_posts.php';
