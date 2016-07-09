<?php
global $control_vars;

$vars = '';

$query_key = '_control_blog_post';

$query = wp_cache_get($query_key);

if ($query === false) {

    $query = new WP_Query(array(
        'post_type'     => 'post',
        'order'         => 'DESC',
        'orderby'       => 'date',
        'post__in' => get_option('sticky_posts'),
        'posts_per_page' => 3,
    ));

    wp_cache_set($query_key, $query);
}
?>

<div class="control blog-posts blog-posts-control">
    <div class="blog-posts-holder">
        <?php while ($query->have_posts()) : $query->the_post(); ?>

            <div class="post-entry<?php echo has_the_featured_image() ? '' : ' no-featured-image'; ?>">
                <div class="post-entry-holder">
                    <?php if (has_the_featured_image()) : ?>
                    <div class="item-image in-back">
                        <a href="<?php echo get_the_permalink(); ?>" title="<?php echo get_the_title(); ?>" style="background-image: url(<?php echo get_the_featured_image('medium'); ?>)"></a>
                    </div>
                    <?php endif; ?>
                    <div class="post-entry-wrapper">
                        <h1 class="item-title"><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h1>
                        <h5 class="item-date"><?php echo get_the_date(); ?></h5>
                        <div class="item-text"><?php echo get_the_excerpt_ex(320, '...'); ?></div>
                    </div>
                </div>
            </div>

        <?php endwhile; ?>
    </div>
</div>