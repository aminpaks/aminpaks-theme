<div>
    <?php echo do_shortcode('[headline title="Nothing found!"]'); ?>
    <?php if (is_search()) : ?>
        <p><?php _e('It looks like nothing was found. Maybe try another search?', 'theme'); ?></p>
    <?php else : ?>
        <p><?php _e('It looks like nothing was found at this location. Maybe try a search?', 'theme'); ?></p>
    <?php endif; ?>
    <?php get_search_form(); ?>
</div><!-- article -->
