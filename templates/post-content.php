<div class="content-entry">
    <?php if (has_the_featured_image()) : ?>
    <div class="entry-image">
        <span style="background-image: url(<?php echo get_the_featured_image(''); ?>)"></span>
    </div>
    <?php endif; ?>
    <div class="content-entry-holder">
        <div class="content-entry-wrapper">
            <div class="entry-content">
                <h1 class="entry-title"><?php echo get_the_title(); ?></h1>
                <h5 class="entry-date"><?php echo get_the_date(); ?></h5>
                <div class="entry-text"><?php the_content(); ?></div>
                <?php if (get_the_tags()) : ?>
                <div class="entry-tags">
                    <span class="tags-title"><?php _e('Tags:', 'theme'); ?></span>&nbsp;<?php echo get_the_tag_list('', ', '); ?>
                </div>
                <?php endif; ?>
                <?php if (function_exists('get_social_share')) : ?>
                <div class="entry-share">
                    <div class="entry-share-holder">
                        <div class="entry-share-wrapper">
                            <ul>
                            <?php foreach (get_social_share('facebook twitter email') as $share) : ?>
                                <li class="share-item share-<?php echo $share['icon']; ?>"><a href="<?php echo $share['url']; ?>" class="icon icon-<?php echo $share['icon']; ?>" target="<?php echo $share['target']; ?>" data-no-ajax="1"><em></em></a></li>
                            <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div><!-- .content-entry-wrapper -->

        <?php get_template_part('templates/sidebar'); ?>

    </div><!-- .content-entry-holder -->

    <?php get_template_part('templates/post-link'); ?>

</div><!-- .content-entry -->
