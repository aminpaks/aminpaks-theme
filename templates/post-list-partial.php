<div class="content-entry<?php echo has_the_featured_image() ? '' : ' no-featured-image'; ?>">
    <div class="content-entry-holder">
        <?php if (has_the_featured_image()) : ?>
        <div class="entry-image">
            <a id="article-image-link-<?php echo get_the_ID(); ?>" href="<?php echo get_the_permalink(); ?>" title="<?php echo get_the_title(); ?>"><img src="<?php echo get_the_featured_image('medium'); ?>" alt="<?php echo get_the_title(); ?>"></a>
        </div>
        <?php endif; ?>
        <div class="content-entry-wrapper">
            <h1 class="entry-title"><a id="article-title-link-<?php echo get_the_ID(); ?>" href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h1>
            <h5 class="entry-date"><?php echo get_the_date(); ?></h5>
            <?php if (get_the_tags()) : ?>
            <h5 class="entry-tags"><span><?php _e('Tags:', 'theme'); ?></span>&nbsp;<?php echo get_the_tag_list('', ', '); ?></h5>
            <?php endif; ?>
        </div>
    </div>
</div>