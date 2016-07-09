<?php
/**
 *  Plugin Name: Customized About/Contact Widget
 *  Description: A widget to display About/Contact info
 *  Version: 1.0
 *  Author: Amin Pakseresht
 *  Author URI: http://www.aminpaks.com
 */
/**
 * Add function to widgets_init that'll load our widget.
 */
add_action('widgets_init', '_theme_load_trademark_widget');

function _theme_load_trademark_widget() {
    register_widget('_theme_trademark_widget');
}

class _theme_trademark_widget extends WP_Widget {

    /**
     * Registrering with WordPress
     */
    function __construct() {
        parent::__construct(
        '_theme_trademark_widget', '+ Company Trademark', array(
            'description' => 'A widget to display company\'s trademark, contact, address.'
        ));
    }

    /**
     * Front-end display of widget
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {

        $title = apply_filters('widget_title', empty($instance[ 'title' ]) ? '' : $instance[ 'title' ], $instance, $this->id_base);
        
        $text = apply_filters('widget_text', $instance[ 'text' ]);

        $logo    = apply_filters('widget_text', $instance[ 'logo' ]);
        //$socials = (bool) $instance[ 'socials' ];

        $address  = apply_filters('widget_text', $instance[ 'address' ]);
        $phone    = apply_filters('widget_text', $instance[ 'phone' ]);
        $email    = apply_filters('widget_text', $instance[ 'email' ]);
        $linkedin = apply_filters('widget_text', $instance[ 'linkedin' ]);
        $twitter  = apply_filters('widget_text', $instance[ 'twitter' ]);
        $facebook = apply_filters('widget_text', $instance[ 'facebook' ]);

        $args[ 'before_widget' ] = str_replace(array( '\'', '"' ), '"', $args[ 'before_widget' ]);
        $matches = null;
        if (preg_match('/(class="[^"]+)/i', $args[ 'before_widget' ], $matches)) {
            $args[ 'before_widget' ] = str_replace(
            $matches[ 1 ], $matches[ 1 ] . ' trademark-widget', $args[ 'before_widget' ]);
        }
        else {
            $args[ 'before_widget' ] = str_replace('>', ' class="trademark-widget">', $args[ 'before_widget' ]);
        }

        echo $args[ 'before_widget' ];
        if (!empty($title)) {
            echo $args[ 'before_title' ] . $title . $args[ 'after_title' ];
        }
        ?>
        <div class="widget-content">
            <ul>

                <?php if (!empty($logo)) : ?>

                    <li>
                        <div class="trademark-logo">
                            <div class="trademark-logo-image icon icon-logo"></div>
                        </div>
                    </li>

                <?php endif; ?>
                <?php if (!empty($text)) : ?>

                <li>
                    <div class="trademark-text"><?php echo $text; ?></div>
                </li>

                <?php endif; ?>
                <?php if (!empty($address)) : ?>

                    <li>
                        <div class="trademark-address icon-address00">
                            <span><?php echo $address; ?></span>
                        </div>
                    </li>

                <?php endif ?>
                <?php if (!empty($phone)) { ?>

                    <li>
                        <div class="trademark-phone icon-phone00">
                            T:&nbsp;&nbsp;<span><?php echo $phone; ?></span>
                        </div>
                    </li>

                <?php } ?>
                <?php if (!empty($email)) { ?>

                    <li>
                        <div class="trademark-email icon-email00">
                            E:&nbsp;&nbsp;<a href="mailto:<?php echo $email; ?>"><span><?php echo $email; ?></span></a>
                        </div>
                    </li>

                <?php } ?>
                <?php if (!(empty($linkedin) && empty($twitter))) : ?>

                    <li>
                        <div class="trademark-socials">

                            <?php if (!empty($linkedin)) : ?>

                            <a href="<?php echo $linkedin; ?>" class="trademark-socials-linkedin icon icon-linkedin" target="_blank"></a>

                            <?php endif; ?>
                            <?php if (!empty($twitter)) : ?>

                            <a href="<?php echo $twitter; ?>" class="trademark-socials-twitter icon icon-twitter" target="_blank"></a>

                            <?php endif; ?>
                            <?php if (!empty($facebook)) : ?>

                            <a href="<?php echo $facebook; ?>" class="trademark-socials-facebook icon icon-facebook" target="_blank"></a>

                            <?php endif; ?>
                        </div>
                    </li>

                <?php endif; ?>
            </ul>
        </div>
        <?php
        echo $args[ 'after_widget' ];
    }

    /**
     * Back-end widget form.
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance) {

        $instance = wp_parse_args((array) $instance, array(
            'title'    => '',
            'logo'     => '',
            'text'     => '',
            'address'  => '',
            'phone'    => '',
            'email'    => '',
            'linkedin' => '',
            'twitter'  => '',
            'facebook'  => '',
        ));

        $title    = strip_tags($instance[ 'title' ]);
        $logo     = strip_tags($instance[ 'logo' ]);
        $text     = esc_textarea($instance[ 'text' ]);
        $address  = strip_tags($instance[ 'address' ]);
        $phone    = strip_tags($instance[ 'phone' ]);
        $email    = strip_tags($instance[ 'email' ]);
        $linkedin = strip_tags($instance[ 'linkedin' ]);
        $twitter  = strip_tags($instance[ 'twitter' ]);
        $facebook  = strip_tags($instance[ 'facebook' ]);

        $title_placeholder    = __('Widget Title', 'theme');
        $logo_placeholder     = __('Trademark logo image', 'theme');
        $text_placeholder     = __('Trademark name/title', 'theme');
        $address_placeholder  = __('Street, City, Country', 'theme');
        $phone_placeholder    = '+1 999 222 000';
        $email_placeholder    = 'info@trademark.com';
        $linkedin_placeholder = '//www.linkedin.com/in/trademark/';
        $twitter_placeholder  = '//www.twitter.com/trademark/';
        $facebook_placeholder  = '//www.facebook.com/trademark/';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" placeholder="<?php echo $title_placeholder; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('logo'); ?>">Logo:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('logo'); ?>" name="<?php echo $this->get_field_name('logo'); ?>" type="text" value="<?php echo esc_attr($logo); ?>" placeholder="<?php echo $logo_placeholder; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('text'); ?>">Text:</label>
            <textarea class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" rows="16" cols="20" placeholder="<?php echo $text_placeholder; ?>"><?php echo $text; ?></textarea>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('address'); ?>">Address:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('address'); ?>" name="<?php echo $this->get_field_name('address'); ?>" type="text" value="<?php echo esc_attr($address); ?>" placeholder="<?php echo $address_placeholder; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('phone'); ?>">Phone:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('phone'); ?>" name="<?php echo $this->get_field_name('phone'); ?>" type="text" value="<?php echo esc_attr($phone); ?>" placeholder="<?php echo $phone_placeholder; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('email'); ?>">Email:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('email'); ?>" name="<?php echo $this->get_field_name('email'); ?>" type="text" value="<?php echo esc_attr($email); ?>" placeholder="<?php echo $email_placeholder; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('linkedin'); ?>">LinkedIn:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('linkedin'); ?>" name="<?php echo $this->get_field_name('linkedin'); ?>" type="text" value="<?php echo esc_attr($linkedin); ?>" placeholder="<?php echo $linkedin_placeholder; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('twitter'); ?>">Twitter:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('twitter'); ?>" name="<?php echo $this->get_field_name('twitter'); ?>" type="text" value="<?php echo esc_attr($twitter); ?>" placeholder="<?php echo $twitter_placeholder; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('facebook'); ?>">Facebook:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('facebook'); ?>" name="<?php echo $this->get_field_name('facebook'); ?>" type="text" value="<?php echo esc_attr($facebook); ?>" placeholder="<?php echo $facebook_placeholder; ?>">
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = $old_instance;

        $instance[ 'title' ] = strip_tags($new_instance[ 'title' ]);
        $instance[ 'logo' ]  = strip_tags($new_instance[ 'logo' ]);

        if (current_user_can('unfiltered_html'))
            $instance[ 'text' ] = $new_instance[ 'text' ];
        else
            $instance[ 'text' ] = stripslashes(wp_filter_post_kses(addslashes($new_instance[ 'text' ]))); // wp_filter_post_kses() expects slashed

        $instance[ 'address' ]  = strip_tags($new_instance[ 'address' ]);
        $instance[ 'phone' ]    = strip_tags($new_instance[ 'phone' ]);
        $instance[ 'email' ]    = strip_tags($new_instance[ 'email' ]);
        $instance[ 'linkedin' ] = strip_tags($new_instance[ 'linkedin' ]);
        $instance[ 'twitter' ]  = strip_tags($new_instance[ 'twitter' ]);
        $instance[ 'facebook' ] = strip_tags($new_instance[ 'facebook' ]);

        return $instance;
    }

}
