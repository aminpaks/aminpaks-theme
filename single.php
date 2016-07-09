<?php get_header(); ?>

            <div class="header">
                <div class="header-holder">
                    <div class="header-wrapper">
                        <div class="container">
                            <?php //echo do_shortcode('[headline title="' . get_the_title() . '"]'); ?>
                        </div>
                    </div><!-- .header-wrapper -->
                </div><!-- .header-holder -->
            </div><!-- .header -->

            <?php get_template_part('templates/control-navigation'); ?>

            <div class="content with-sidebar">
                <div class="container">
                    <div class="content-holder">
                        <div class="content-wrapper">

                            <?php

                            $cat_slug = get_query_var('category_name');
                            
                            if (function_exists('icl_get_current_language') && icl_get_current_language() !== 'en') {
                            
                                if (($cat_obj = get_category_by_slug($cat_slug)) !== null) {
                            
                                    if (($en_cat_obj = icl_object($cat_obj->cat_ID, 'category')) !== false) {
                                        $cat_slug = $en_cat_obj->slug;
                                    }
                                }
                            }

                            $templates = array();

                            if (!empty($cat_slug)) {
                                $templates[] = 'templates/post-content-' . $cat_slug;
                            }

                            $templates[] = 'templates/post-content';

                            while (have_posts()) :

                                the_post();

                                get_template_parts($templates);

                            endwhile;
                            ?>

                        </div><!-- .content-wrapper -->
                    </div><!-- .content-holder -->
                </div><!-- .container -->
            </div><!-- .content -->

<?php get_footer();