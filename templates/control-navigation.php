            <div class="navigation">
                <div class="navigation-holder">
                    <div class="container">
                        <div class="navigation-wrapper">
                            <div class="logo">
                                <div class="logo-holder">
                                    <div class="logo-wrapper">
                                        <a href="<?php echo home_url(); ?>" title="<?php bloginfo('description'); ?>">
                                            <span class="icon icon-logo"></span><span class="logo-title"><?php _e('Amin Paks', 'theme'); ?></span><span class="link-home">Home</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="parts">
                                <div class="menu">
                                    <div class="menu-holder">
                                        <div class="menu-wrapper">

                                            <?php
                                            wp_nav_menu(array(
                                                'theme_location'  => 'main_menu',
                                                'container'       => '',
                                                'container_class' => '',
                                                'items_wrap'      => '<ul>%3$s</ul>',
                                            ));
                                            ?>

                                        </div>
                                    </div>
                                </div>

                                <?php if (is_active_sidebar('header-sidebar')) : ?>

                                <div class="widgets">

                                <?php dynamic_sidebar('header-sidebar'); ?>

                                </div>

                                <?php endif; ?>

                            </div>
                        </div><!-- .navigation-wrapper -->
                    </div>
                </div><!-- .navigation-holder -->
            </div><!-- .navigation -->
