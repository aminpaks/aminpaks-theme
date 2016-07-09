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
add_action('widgets_init', '_theme_load_more_posts_widget');

function _theme_load_more_posts_widget() {
    register_widget('_theme_more_posts_widget');
}

class _theme_more_posts_widget extends WP_Widget {

    /**
     * Registrering with WordPress
     */
    function __construct() {
        parent::__construct(
        '_theme_more_posts_widget', '+ More Posts',
        array(
            'description' => 'A customized widget to display some random posts.'
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

        //$stickies = get_option('sticky_posts');

        $query_args = array(
            'posts_per_page'      => $posts_count,
            'post_type'           => 'post',
            'orderby'             => 'rand',
            //'post__not_in'   => $stickies,
            'ignore_sticky_posts' => true,
        //'nopaging'       => true,
        );

        if (count($category_ids) > 0) {
            $query_args[ 'category__in' ] = $category_ids;
        }

        if (is_single() && ($id = get_the_ID())) {
            $query_args[ 'post__not_in' ] = array( $id );
        }

        $query = new WP_Query($query_args);

        // NO CACHE FOR RANDOM POSTS

        /*
          $cache_key = sprintf('_theme_category_%s_post_%s_query', implode('_', $category_ids), $posts_count);

          // First, let's see if we have the data in the cache already
          $query = wp_cache_get($cache_key); // the cache key is a unique identifier for this data

          if ($query === false) {

          // Looks like the cache didn't have our data
          // Let's generate the query
          $query_args = array(
          'post_type' => 'post',
          'orderby' => 'rand',
          'posts_per_page' => $posts_count,
          'category__in' => $category_ids,
          );

          if (!empty(get_the_ID())) {
          $query_args['post__not_in'] = array(get_the_ID());
          }

          $query = new WP_Query($query_args);

          // Now, let's save the data to the cache
          // In this case, we're telling the cache to expire the data after 300 seconds
          wp_cache_set($cache_key, $query); // the third parameter is $group, which can be useful if you're looking to group related cached values together
          } */

        $args[ 'before_widget' ] = str_replace(array( '\'', '"' ), '"', $args[ 'before_widget' ]);
        if (preg_match('/(class="[^"]+)/i', $args[ 'before_widget' ], $matches)) {
            $args[ 'before_widget' ] = str_replace(
            $matches[ 1 ], $matches[ 1 ] . ' posts-widget with-pagination', $args[ 'before_widget' ]);
        }
        else {
            $args[ 'before_widget' ] = str_replace('>', ' class="posts-widget with-pagination">',
            $args[ 'before_widget' ]);
        }

        echo $args[ 'before_widget' ];

        if (!empty($title)) {
            echo $args[ 'before_title' ] . $title . $args[ 'after_title' ];
        }
        ?>

        <div class="posts-holder">
            <div class="posts-wrapper">
                <ul>

                    <?php while ($query->have_posts()) : $query->the_post(); ?>

                        <li>
                            <figure class="with-back-image">
                                <a class="entry-image entry-thumbnail" href="<?php echo get_the_permalink(); ?>" title="<?php echo get_the_title(); ?>"><div style="background-image: url(<?php echo get_the_featured_image('medium'); ?>);"></div></a>
                                <figcaption>
                                    <div class="entry-date"><?php echo get_the_date(); ?></div>
                                    <h3 class="entry-title"><a href="<?php echo get_the_permalink(); ?>" title="<?php echo get_the_title(); ?>"><?php echo get_the_title(); ?></a></h3>
                                    <div class="entry-summary"><?php
                                        echo get_the_excerpt_ex(200, ' ... <a href="%s">continue reading</a>');
                                        ?></div>
                                </figcaption>
                            </figure>
                        </li>

                        <?php
                    endwhile;
                    wp_reset_query();
                    ?>

                </ul>
            </div><!-- .post-wrapper -->
        </div><!-- .posts-holder -->

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
