<?php get_header(); ?>

            <?php get_template_part('templates/page-header'); ?>

            <?php get_template_part('templates/control-navigation'); ?>

            <div class="content">
                <div class="container">
                    <div class="content-holder">
                        <div class="content-wrapper">

                            <div class="content-entry">
                            
                                <?php
                                while (have_posts()) :

                                    the_post();

                                    get_template_part('templates/page-content');

                                endwhile;
                                ?>

                            </div>
                            
                        </div><!-- .content-wrapper -->
                    </div><!-- .content-holder -->
                </div><!-- .container -->
            </div><!-- .content -->
            
<?php get_footer();