            <div class="footer">
                <div class="container">

                    <?php
                    if (class_exists('_theme_options')) :
                        $opt = _theme_options::get_instance();

                        $socials = array();
                        
                        foreach($opt->get_options('contact') as $item => $meta) {
                            if (empty($meta[ 'value' ]) || !isset($meta[ 'extra' ][ 'attrs' ]) || !isset($meta[ 'extra' ][ 'text' ])) continue;
                            $socials[ $item ] = $meta;
                        }
                        
                        if (count($socials)) : ?>

                    <div class="socials">
                        <div class="socials-holder">
                            <div class="socials-wrapper">
                                <?php foreach($socials as $item => $meta) : ?>
                                <a href="<?php echo $meta['value']; ?>" class="icon icon-<?php echo $meta['name']; ?>" target="_blank"></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <?php
                        endif;
                    endif;
                    ?>

                    <?php if (is_active_sidebar('footer-copyright')) : ?>

                    <div class="footer-copyright">
                        <div class="footer-copyright-wrapper">

                            <?php dynamic_sidebar('footer-copyright'); ?>

                        </div>
                    </div>

                    <?php endif; ?>

                </div>
            </div><!-- .footer -->
            <div class="scroll-to-top invisible">
                <div class="scroll-to-top-holder">
                    <div class="scroll-to-top-wrapper">
                        <a href="javascript:void(0)"><span>Top</span></a>
                    </div>
                </div>
            </div><!-- .scroll-to-top -->
        </div><!-- .theme -->

        <?php wp_footer(); ?>
    </body>
</html>