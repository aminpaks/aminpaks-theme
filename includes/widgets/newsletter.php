<?php
/**
 *  Plugin Name: Customized Newsletter Widget
 *  Description: A widget to display Newsletter
 *  Version: 1.0
 *  Author: Amin Pakseresht
 *  Author URI: http://www.aminpaks.com
 */
/**
 * Add function to widgets_init that'll load our widget.
 */
add_action('widgets_init', '_theme_load_newsletter_widget');

function _theme_load_newsletter_widget() {
    register_widget('_theme_newsletter_widget');
}

class _theme_newsletter_widget extends WP_Widget {

    /**
     * Registrering with WordPress
     */
    function __construct() {
        parent::__construct(
        '_theme_newsletter_widget', '+ Newsletter',
        array(
            'description' => 'A customized newsletter widget.'
        ));
    }

    /**
     * Front-end display of widget
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {

        $url            = isset($instance[ 'url' ]) ? $instance[ 'url' ] : '';
        $fields         = isset($instance[ 'fields' ]) ? $instance[ 'fields' ] : '';
        $socials        = isset($instance[ 'socials' ]) ? $instance[ 'socials' ] : '';
        $title          = apply_filters('widget_title', $instance[ 'title' ]);
        $text           = apply_filters('widget_text', $instance[ 'text' ]);
        $agreement_text = apply_filters('widget_text', $instance[ 'agreement_text' ]);

        if (empty($text)) {
            $text = false;
        }

        $fields_defaults = array(
            'email'  => array(
                'type'        => 'email',
                'name'        => 'email',
                'class'       => 'input-email',
                'placeholder' => 'Your email here',
                'container'   => array(
                    'tag_name' => 'div',
                    'class'    => 'input input-text icon-envelope user-email',
                )
            ),
            'submit' => array(
                'type'      => 'submit',
                'value'     => 'Subscribe Me',
                'class'     => 'input-submit',
                'container' => array(
                    'tag_name'  => 'div',
                    'class'     => 'button-holder icon-arrow-right',
                    'container' => array(
                        'tag_name' => 'div',
                        'class'    => 'button button-submit',
                    )
                )
            )
        );
        
        $socials_default = array(
            'linkedin' => array(
                'text'      => 'LinkedIn',
                'container' => array(
                    'tag_name' => 'a',
                    'class'    => 'icon icon-linkedin',
                    'href'     => '',
                    'target'   => '_blank',
                ),
            ),
            'twitter'  => array(
                'text'      => 'Twitter',
                'container' => array(
                    'tag_name' => 'a',
                    'class'    => 'icon icon-twitter',
                    'href'     => '',
                    'target'   => '_blank',
                ),
            ),
            'facebook' => array(
                'text'      => 'Facebook',
                'container' => array(
                    'tag_name' => 'a',
                    'class'    => 'icon icon-facebook',
                    'href'     => '',
                    'target'   => '_blank',
                ),
            ),
        );

        if (!empty($fields)) {

            $fields = preg_replace('/({|,)([^\w]*)([^\s]*)(:)/s', '$1$2"$3"$4', $fields);
            $fields = str_replace('\'', '"', $fields);

            $json = json_decode($fields, true);

            if (!isset($json)) {
                $json = array();
            }

            $fields_obj = array_replace_recursive($fields_defaults, $json);
        }
        
        if (!empty($socials)) {
            
            $socials = preg_replace('/({|,)([^\w]*)([^\s]*)(:)/s', '$1$2"$3"$4', $socials);
            $socials = str_replace('\'', '"', $socials);

            $socials_json = json_decode($socials, true);

            if (!isset($socials_json)) {
                $socials_json = array();
            }
            
            if (!function_exists('____socials_sorting')) {

                function ____socials_sorting($a, $b) {

                    if (isset($a[ 'order' ]) && isset($b[ 'order' ])) {

                        return strcmp($a[ 'order' ], $b[ 'order' ]);
                    }
                    else {
                        return 0;
                    }
                }

            }

            $socials_array = array_replace_recursive($socials_default, $socials_json);
            usort($socials_array, '____socials_sorting');
        }

        $args[ 'before_widget' ] = str_replace(array( '\'', '"' ), '"', $args[ 'before_widget' ]);
        if (preg_match('/(class="[^"]+)/i', $args[ 'before_widget' ], $matches)) {
            $args[ 'before_widget' ] = str_replace(
            $matches[ 1 ], $matches[ 1 ] . ' newsletter-widget', $args[ 'before_widget' ]);
        }
        else {
            $args[ 'before_widget' ] = str_replace('>', ' class="newsletter-widget">', $args[ 'before_widget' ]);
        }

        echo $args[ 'before_widget' ];
        if (!empty($title)) {
            echo $args[ 'before_title' ] . $title . $args[ 'after_title' ];
        }
        ?>
        <div class="widget-content">
            <form action="<?php echo (isset($url) && !empty($url) ? $url : '#newsletter-url'); ?>" method="post">
                <?php if (isset($socials_array) && is_array($socials_array) && count($socials_array)) : ?>
                <div class="widget-content-socials">
                    <?php
                    foreach ($socials_array as $value) :
                        
                        if (!isset($value['container']['href']) || empty($value['container']['href'])) {
                            continue;
                        }
                        
                        if (isset($value[ 'container' ])) {
                            $container = wrap_with_container($value[ 'container' ]);

                            unset($value[ 'container' ]);
                        }
                        
                        $social_html = $value['text'];

                        if (isset($container)) {
                            $social_html = sprintf($container, $social_html);
                        }

                        echo $social_html;
                        
                    endforeach; ?>
                </div>
                <?php endif; ?>
                <?php if ($text !== false) : ?>
                    <div class="widget-content-text"><?php echo $text; ?></div>
                <?php endif; ?>
                <div class="widget-content-inputs">
                    <?php
                    foreach ($fields_obj as $value) :

                        if (isset($value[ 'container' ])) {
                            $container = wrap_with_container($value[ 'container' ]);

                            unset($value[ 'container' ]);
                        }

                        $input_attrubutes = '';

                        foreach ($value as $attr_name => $attr_value) {
                            $input_attrubutes .= ' ' . esc_attr($attr_name) . '="' . esc_attr($attr_value) . '"';
                        }

                        $html = "<input$input_attrubutes />";

                        if (isset($container)) {
                            $html = sprintf($container, $html);
                        }

                        echo $html;

                    endforeach;
                    ?>
                    <?php if (!empty($agreement_text)) : ?>

                        <div class="input input-checkbox user-agreement">
                            <div class="input-holder">
                                <label><?php echo $agreement_text; ?>
                                    <input id="check-box-form" class="input-option" name="agreement" type="hidden">
                                </label>
                            </div>
                        </div>

                    <?php endif; ?>

                </div>
            </form>
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
        $instance = wp_parse_args((array) $instance,
        array(
            'title'          => '',
            'text'           => '',
            'url'            => '',
            'agreement_text' => '',
            'fields'         => '',
        ));

        $title          = strip_tags($instance[ 'title' ]);
        $text           = esc_textarea($instance[ 'text' ]);
        $url            = strip_tags($instance[ 'url' ]);
        $agreement_text = strip_tags($instance[ 'agreement_text' ]);
        $fields         = isset($instance[ 'fields' ]) ? $instance[ 'fields' ] : '';
        $socials        = isset($instance[ 'socials' ]) ? $instance[ 'socials' ] : '';

        $title_placeholder          = __('Widget Title', 'theme');
        $text_placeholder           = __('Trademark name/title', 'theme');
        $url_placeholder            = '//www.campaign-monitor.com/domain/form-address/';
        $agreement_text_placeholder = __('Agreement phrase', 'theme');
        $fields_placeholder = "{\n\temail: {\n\t\ttype: 'email',\n\t\tname: 'email',\n\t\tplaceholder: 'Enter your email',\n\t},\n\tsubmit: {\n\t\tvalue: 'Subscribe'\n\t}\n}";
        $socials_placeholder = "{\n\tlinkedin: {\n\t\ttext: 'LinkedIn',\n\t\tcontainer: {\n\t\t\thref: '//linkedin.com/name'\n\t\t}\n\t}\n}";
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('url'); ?>">URL:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" type="text" value="<?php echo esc_attr($url); ?>" placeholder="<?php echo $url_placeholder; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" placeholder="<?php echo $title_placeholder; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('text'); ?>">Text:</label>
            <textarea class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" rows="16" cols="20" placeholder="<?php echo $text_placeholder; ?>"><?php echo $text; ?></textarea>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('agreement_text'); ?>">Agreement:</label>
            <textarea class="widefat" id="<?php echo $this->get_field_id('agreement_text'); ?>" name="<?php echo $this->get_field_name('agreement_text'); ?>" rows="5" cols="20" placeholder="<?php echo $agreement_text_placeholder; ?>"><?php echo $agreement_text; ?></textarea>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('fields'); ?>">Fields <small>Advanced Settings</small>:</label>
            <textarea class="widefat" id="<?php echo $this->get_field_id('fields'); ?>" name="<?php echo $this->get_field_name('fields'); ?>" rows="16" cols="20" placeholder="<?php echo $fields_placeholder; ?>"><?php echo $fields; ?></textarea>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('socials'); ?>">Social Networks <small>Advanced Settings</small>:</label>
            <textarea class="widefat" id="<?php echo $this->get_field_id('socials'); ?>" name="<?php echo $this->get_field_name('socials'); ?>" rows="16" cols="20" placeholder="<?php echo $socials_placeholder; ?>"><?php echo $socials; ?></textarea>
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

        $instance[ 'url' ]     = strip_tags($new_instance[ 'url' ]);
        $instance[ 'title' ]   = strip_tags($new_instance[ 'title' ]);
        $instance[ 'fields' ]  = strip_tags($new_instance[ 'fields' ]);
        $instance[ 'socials' ] = strip_tags($new_instance[ 'socials' ]);

        if (current_user_can('unfiltered_html'))
            $instance[ 'text' ] = $new_instance[ 'text' ];
        else
            $instance[ 'text' ] = stripslashes(wp_filter_post_kses(addslashes($new_instance[ 'text' ]))); // wp_filter_post_kses() expects slashed

        $instance[ 'agreement_text' ] = strip_tags($new_instance[ 'agreement_text' ]);

        return $instance;
    }

}

if (!function_exists('wrap_with_container')) {

    function wrap_with_container($container) {

        if (!is_array($container)) {
            return null;
        }

        $attribute_str = '';
        $tag           = strip_tags($container[ 'tag_name' ]);

        unset($container[ 'tag_name' ]);

        if (isset($container[ 'container' ])) {
            $wrap_str_fmt = wrap_with_container($container[ 'container' ]);

            unset($container[ 'container' ]);
        }

        foreach ($container as $attr_name => $attr_value) {
            $attribute_str .= ' ' . esc_attr($attr_name) . '="' . esc_attr($attr_value) . '"';
        }

        $html = "<$tag $attribute_str>%s</$tag>";

        if (isset($wrap_str_fmt)) {
            $html = sprintf($wrap_str_fmt, $html);
        }

        return $html;
    }

}