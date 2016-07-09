<?php get_header(); ?>

            <div class="content">
                <div class="container">
                    <div class="content-holder">
                        <div class="content-wrapper">

                            <?php get_template_part('templates/breadcrumbs'); ?>
                            <?php get_template_part('templates/page-header'); ?>

                            <div class="client-entry-list">

                                <?php while (have_posts()) : the_post(); ?>

                                <div class="client-entry">
                                    <div class="client-entry-holder">
                                        <div class="client-entry-wrapper">
                                            <div class="client-image"><a href="<?php echo get_the_permalink(); ?>"><?php echo shortcode_unautop_ex(do_shortcode('[figure class="back-image" image="'. get_the_featured_image('large') .'"]')); ?></a></div>
                                            <div class="client-detail">
                                                <h1 class="client-name"><a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a></h1>
                                                <?php if (get_the_taxonomies()) : ?>
                                                <h5 class="client-tag"><?php echo implode(', ', get_the_taxonomies()); ?></h5>
                                                <?php endif; ?>
                                            </div>
                                        </div><!-- .client-entry-wrapper -->
                                    </div><!-- .client-entry-holder -->
                                </div><!-- .client-entry -->

                                <?php endwhile; ?>

                            </div><!-- .content-entry-list -->
                            
                        </div><!-- .content-wrapper -->
                    </div><!-- .content-holder -->
                </div><!-- .container -->
            </div><!-- .content -->
            
<?php get_footer();