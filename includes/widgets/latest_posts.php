<?php
/**
 *  Plugin Name: Customized Latest Posts Widget
 *  Description: A widget to display latest posts from categories
 *  Version: 1.0
 *  Author: Amin Pakseresht
 *  Author URI: http://www.aminpaks.com
 */
/**
 * Add function to widgets_init that'll load our widget.
 */
add_action('widgets_init', '_theme_load_latest_posts_widget');

function _theme_load_latest_posts_widget() {
    register_widget('_theme_latest_posts_widget');
}

class _theme_latest_posts_widget extends WP_Widget {

    /**
     * Registrering with WordPress
     */
    function __construct() {
        parent::__construct(
        '_theme_latest_posts_widget', '+ Latest Posts',
        array(
            'description' => 'A customized widget to display latest posts.'
        ));
    }

    /**
     * Front-end display of widget
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {

        $title        = apply_filters('widget_title', $instance[ 'title' ]);
        $category_str = apply_filters('widget_categories_args', $instance[ 'category_ids' ]);
        $posts_count  = (int) $instance[ 'posts_count' ];

        if ($posts_count < 2) {
            $posts_count = 2;
        }

        $category_ids = array();

        $explode = explode(',', $category_str);

        foreach ((array) $explode as $cat_id) {
            $cat_id = trim($cat_id);
            if (empty($cat_id)) {
                continue;
            }

            $category_ids[] = (int) $cat_id;
        }

        $cache_key = sprintf('_theme_latest_posts_category_%s_post_%s_query', implode('_', $category_ids), $posts_count);

        // First, let's see if we have the data in the cache already
        $query = wp_cache_get($cache_key); // the cache key is a unique identifier for this data

        if ($query === false) {

            $query_args = array(
                'posts_per_page'      => $posts_count,
                'post_type'           => 'post',
                'orderby'             => 'date',
                'order'               => 'DESC',
                //'post__not_in'   => $stickies,
                'ignore_sticky_posts' => true,
                //'nopaging'       => true,
            );

            if (count($category_ids) > 0) {
                $query_args[ 'category__in' ] = $category_ids;
            }

            $query = new WP_Query($query_args);

            // Now, let's save the data to the cache
            // In this case, we're telling the cache to expire the data after 300 seconds
            wp_cache_set($cache_key, $query); // the third parameter is $group, which can be useful if you're looking to group related cached values together
        }


        $args[ 'before_widget' ] = str_replace(array( '\'', '"' ), '"', $args[ 'before_widget' ]);
        if (preg_match('/(class="[^"]+)/i', $args[ 'before_widget' ], $matches)) {
            $args[ 'before_widget' ] = str_replace(
            $matches[ 1 ], $matches[ 1 ] . ' latest-posts-widget', $args[ 'before_widget' ]);
        }
        else {
            $args[ 'before_widget' ] = str_replace('>', ' class="latest-posts-widget">', $args[ 'before_widget' ]);
        }

        echo $args[ 'before_widget' ];

        if (!empty($title)) {
            echo $args[ 'before_title' ] . $title . $args[ 'after_title' ];
        }
        ?>

        <div class="widget-content">
            <div class="widget-content-holder">
                <div class="widget-content-wrapper">
                    <ul>

                        <?php
                        while ($query->have_posts()) :
                            $query->the_post();
                            ?>

                            <li>
                                <div class="content-entry">
                                    <?php if (has_the_featured_image()) : ?>
                                    <a class="entry-image" href="<?php echo get_the_permalink(); ?>" title="<?php echo get_the_title(); ?>"><img src="<?php echo get_the_featured_image('medium'); ?>" /></a>
                                    <?php endif; ?>
                                    <h3 class="entry-title"><a href="<?php echo get_the_permalink(); ?>" title="<?php echo get_the_title(); ?>"><?php echo get_the_title(); ?></a></h3>
                                    <h5 class="entry-date"><?php echo get_the_date(); ?></h5>
                                </div>
                            </li>

            <?php
        endwhile;
        wp_reset_query();
        ?>

                    </ul>
                </div><!-- .widget-content-wrapper -->
            </div><!-- .widget-content-holder -->
        </div><!-- .widget-content -->

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
            'title'        => '',
            'category_ids' => '',
            'posts_count'  => 6,
        ));

        $title        = strip_tags($instance[ 'title' ]);
        $category_ids = strip_tags($instance[ 'category_ids' ]);
        $posts_count  = strip_tags($instance[ 'posts_count' ]);

        $title_placeholder        = __('More Posts', 'theme');
        $category_ids_placeholder = '[ID1 [[, ID2] [, ID3]...]]';
        $posts_count_placeholder  = __('Enter a number to limit post count', 'theme');

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" placeholder="<?php echo $title_placeholder; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('category_ids'); ?>">Category Id(s):</label>
            <input class="widefat" id="<?php echo $this->get_field_id('category_ids'); ?>" name="<?php echo $this->get_field_name('category_ids'); ?>" type="text" value="<?php echo esc_attr($category_ids); ?>" placeholder="<?php echo $category_ids_placeholder; ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('posts_count'); ?>">Post Count:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('posts_count'); ?>" name="<?php echo $this->get_field_name('posts_count'); ?>" type="text" value="<?php echo esc_attr($posts_count); ?>" placeholder="<?php echo $posts_count_placeholder; ?>">
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

        $instance[ 'title' ]        = strip_tags($new_instance[ 'title' ]);
        $instance[ 'posts_count' ]  = strip_tags($new_instance[ 'posts_count' ]);
        $instance[ 'category_ids' ] = strip_tags($new_instance[ 'category_ids' ]);

        return $instance;
    }

}
