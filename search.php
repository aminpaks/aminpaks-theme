<?php get_header(); ?>

            <div class="header">
                <div class="header-holder">
                    <div class="header-wrapper">
                        
                        <?php echo do_shortcode('[headline title="Search results"]'); ?>

                        <div class="container">
                            <div class="search-box">
                                <div class="search-box-holder">
                                    <div class="search-box-wrapper">

                                        <form method="get" action="/" autocomplete="off">
                                            <input class="search-box-input" type="text" name="s" placeholder="Search" value="<?php echo get_query_var('s'); ?>">
                                            <input name="post_type" value="post" type="hidden">
                                            <button class="search-box-submit icon icon-facebook" type="submit">
                                                <em></em>
                                            </button>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div><!-- .header-wrapper -->
                </div><!-- .header-holder -->
            </div><!-- .header -->

            <?php get_template_part('templates/control-navigation'); ?>

            <div class="content">
                <div class="container">
                    <div class="content-holder">
                        <div class="content-wrapper">

                            <?php echo do_shortcode('[headline title="Results for" subtitle="' . get_query_var('s') . '"]'); ?>

                            <div class="content-entry-list">

                            <?php

                            $post_fix = get_query_var('category_name');

                            $templates = array();

                            if (!empty($post_fix)) {
                                $templates[] = 'templates/post-list-partial-' . $post_fix;
                                
                                $cat_obj = get_category_by_slug($post_fix);
                                
                                if (isset($cat_obj) && property_exists($cat_obj, 'term_id') && property_exists($cat_obj, 'slug')) {
                                    
                                    if (($cat_obj_en = icl_object($cat_obj->term_id, 'category', $cat_obj->slug))!== false && $post_fix !== $cat_obj_en->slug) {
                                        
                                        $templates[] = 'templates/post-list-partial-' . $cat_obj_en->slug;
                                    }
                                }
                            }

                            $templates[] = 'templates/post-list-partial';

                            while (have_posts()) :

                                the_post();

                                get_template_parts($templates);

                            endwhile; // end of the loop.
                            ?>

                            </div>
                            
                            <?php // get_template_part( 'templates/pagination' ); ?>

                        </div><!-- .content-wrapper -->
                    </div><!-- .content-holder -->
                </div><!-- .container -->
            </div><!-- .content -->

<?php get_footer();