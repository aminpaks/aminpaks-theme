<?php
global $control_vars;

$vars = '';

$query_key = '_control_network';

$query = wp_cache_get($query_key);

if ($query === false) {

    $query = new WP_Query(array(
        'post_type'     => '_team_member',
        'order'         => 'ASC',
        'orderby'       => 'menu_order',
        'posts_per_page' => -1,
    ));

    wp_cache_set($query_key, $query);
}

$alt = true;

?>

<div class="my-network">
    <div class="my-netowrk-holder">
        <?php while ($query->have_posts()) :
            $query->the_post();
            $member_title = get_post_meta(get_the_ID(), '_team_member_position_title', true);
            if (empty($member_title)) {
                $member_title = false;
            }
            $alt = !$alt;
        ?>
        
        <div class="person<?php echo has_the_featured_image() ? '' : ' no-featured-image' . ' person-' . esc_attr(strtolower(str_replace(' ', '-', get_the_title()))) . ($alt ? ' right' : ' left'); ?>">
            <div class="person-inner">
                <div class="person-picture"><div class="person-picture-inner"><span style="background-image: url(<?php echo get_the_featured_image(''); ?>)"></span></div></div>
                <div class="person-info">
                    <div class="person-info-inner">
                        <div class="person-info-wrapper">
                            <h1 class="person-name"><?php echo get_the_title(); ?></h1>
                            <?php if ($member_title) : ?>
                            <h3 class="person-title"><?php echo $member_title; ?></h3>
                            <?php endif; ?>
                            <div class="person-story"><div class="person-story-inner">
                                <?php echo get_the_content(); ?>
                            </div></div>
                            
                            <div class="person-social">

                            <?php foreach (array('website', 'email', 'facebook', 'linkedin', 'twitter') as $idx => $meta) :
                                $meta_value = get_post_meta(get_the_ID(), '_team_member_' . $meta, true);

                                if (isset($meta_value) && !empty($meta_value)) :
                                    if ($meta === 'email') {
                                        $meta_value = 'mailto:' . $meta_value;
                                    } ?>

                                <a href="<?php echo $meta_value ?>" <?php echo ($meta != 'email' ? ' target="_blank"' : ''); ?> class="icon icon-<?php echo $meta; ?>"><em></em></a>

                                <?php endif;
                            endforeach; ?>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php endwhile; ?>
    </div>
</div>