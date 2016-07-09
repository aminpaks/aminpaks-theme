<?php
/**
 * @package WordPress
 * @author Amin Paks <http://www.aminpaks.com>
 */

if (class_exists('_theme_options')) {
    $opt = _theme_options::get_instance();
}
?><!DOCTYPE html>
<!--[if lt IE 9]><html <?php language_attributes(); ?> class="no-js ie old-ie"> <![endif]-->
<!--[if (gte IE 9)]><html <?php language_attributes(); ?> class="no-js ie"> <!--<![endif]-->
<!--[if !(IE)]><html <?php language_attributes(); ?> class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <title><?php wp_title(); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="canonical" href="<?php echo home_url(); ?>">
        
        <?php if (file_exists(ABSPATH . '/favicon.ico') === true) { ?><link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico"><?php } ?>

        <?php if (($google_analytics = $opt->get_option('analytics', 'template')) !== '') : ?>

        <script async="async" type="text/javascript"><?php echo $google_analytics; ?></script>

        <?php endif; ?>
        <script type="text/javascript">var a = document.getElementsByTagName('html');
            null !== a && a.length > 0 && (a[0].className = 'js-preload');</script>

        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
        <div class="theme">
