<?php
/*
 * Template Name: Blog Page
 * 
 * @package WordPress
 * @since Main 1.0
 */


get_header(); ?>

            <div class="content">
                <div class="container">
                    <div class="content-holder">
                        <div class="content-wrapper">

                            <?php get_template_part('templates/breadcrumbs'); ?>
                            
                            <?php echo do_shortcode('[headline title="Words from the field"]'); ?>

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