<?php
/*
 * Template Name: Works Page
 * 
 * @package WordPress
 * @since Main 1.0
 */

$_query_key = '_works_page_query';

$_query = wp_cache_get($_query_key);

if ($_query === false) {

    $_query = new WP_Query(array(
        'post_type'      => '_work',
        'order'          => 'DESC',
        'orderby'        => 'date',
        'posts_per_page' => -1,
    ));

    wp_cache_set($_query_key, $_query);
}
unset($_query_key);

$_posts = array();
while ($_query->have_posts()) {
    $_query->the_post();
    $_posts[] = get_the_ID();
}
unset($_query);

the_post();

get_header(); ?>

    <?php get_template_part('templates/page-header'); ?>

    <?php get_template_part('templates/control-navigation'); ?>

    <div class="content">
        <div class="container">
            <div class="content-holder">
                <div class="content-wrapper">

                    <?php the_content(); ?>

                    <div class="work-entry-list">

                        <?php foreach ($_posts as $_post_id) :
                            $tax = get_the_term_list($_post_id, 'work-filter', '', ', ');
                        ?>

                        <div class="work-entry">
                            <div class="work-entry-inner">
                                <div class="work-entry-wrapper">
                                    <a class="work-link" href="<?php echo get_the_permalink($_post_id); ?>" title="<?php echo get_the_title($_post_id); ?>">
                                        <span class="work-title image-loader">
                                            <em class="image-loader-symbol"></em>
                                            <img src="<?php echo get_the_featured_image('', $_post_id); ?>" alt="<?php echo get_the_title($_post_id); ?>" />
                                        </span>
                                    </a>
                                    <div class="work-filter"><?php echo $tax; ?></div>
                                </div><!-- .work-entry-wrapper -->
                            </div><!-- .work-entry-inner -->
                        </div><!-- .work-entry -->

                        <?php endforeach; unset($_posts); unset($_post_id); unset($tax); ?>

                    </div><!-- .work-entry-list -->

                </div><!-- .content-wrapper -->
            </div><!-- .content-holder -->
        </div><!-- .container -->
    </div><!-- .content -->
            
<?php get_footer();