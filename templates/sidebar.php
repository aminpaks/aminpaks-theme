<?php

$sidebar_id = 'post-sidebar';

// This value is one of post/page meta data, which has been added by _theme_post_extra class
$custom_sidebar = get_post_meta(get_the_ID(), '_theme_post_extra_sidebar', true);

if (!empty($custom_sidebar) && is_active_sidebar($custom_sidebar)) {
    $sidebar_id = $custom_sidebar;
}

if (is_active_sidebar($sidebar_id)) :
?>

                        <div class="sidebar">
                            <div class="sidebar-holder">
                                <div class="sidebar-wrapper">

                                    <?php dynamic_sidebar($sidebar_id); ?>

                                </div><!-- .sidebar-wrapper -->
                            </div><!-- .sidebar-holder -->
                        </div><!-- .sidebar -->

<?php endif;