<?php
/*
 *  @license © 2014, Amin Paks, T. (514) 441-2413, W. http://www.aminpaks.com
 */

/**
 * Calls the class on the post edit screen.
 */
function _theme_post_extra_init() {
    new _theme_post_extra();
}

if (is_admin()) {
    add_action('load-post.php', '_theme_post_extra_init');
    add_action('load-post-new.php', '_theme_post_extra_init');
}

/**
 * The Class.
 */
class _theme_post_extra {

    private $prefix = '_theme_post_extra_';
    private $fields = null;

    /**
     * Hook into the appropriate actions when the class is constructed.
     */
    public function __construct() {
        add_action('add_meta_boxes', array( $this, 'add_meta_box' ));
        add_action('save_post', array( $this, 'save' ));

        $this->fields = array(
            'page_header'     => array(
                //_slider_slide
                'label'       => __('Page Header', 'theme'),
                'type'        => 'select',
                'desc'        => false,
                'placeholder' => false,
                'post_type'   => array( 'page' ),
                'callback'    => array( $this, '_theme_post_extra_header_callback' ),
            ),
            'title_overwrite' => array(
                'label'       => __('Extend Title', 'theme'),
                'type'        => 'text',
                'desc'        => __('Overwrite the title for headline within content', 'theme'),
                'placeholder' => 'A new title to be overwrite with the original',
                'post_type'   => array( 'page' )
            ),
            'title_hidden'    => array(
                'label'       => __('Hide Title', 'theme'),
                'type'        => 'checkbox',
                'desc'        => __('Hide the title of page', 'theme'),
                'placeholder' => false,
                'post_type'   => array( 'page' )
            ),
            'class'           => array(
                'label'       => __('CSS-Class', 'theme'),
                'type'        => 'text',
                'desc'        => __('Add extra classes', 'theme'),
                'placeholder' => '[an-extra-class [another-extra-class]]...',
                'post_type'   => array( 'post', 'page' )
            ),
            'page_title'      => array(
                'label'       => __('Page Title', 'theme'),
                'type'        => 'text',
                'desc'        => __('Overwrite default page title <small>(SEO Purposes)</small>', 'theme'),
                'placeholder' => 'A new title to be overwrite with page title not headline',
                'post_type'   => array( 'post', 'page' )
            ),
            'description'     => array(
                'label'       => __('Description', 'theme'),
                'type'        => 'textarea',
                'desc'        => __('Content\'s Description <small>(Write you phrases and sepreate them by ",")</small>',
                'theme'),
                'placeholder' => false,
                'post_type'   => array( 'post', 'page' )
            ),
        /* 'sidebar'              => array(
          'label'       => __('Sidebar', 'theme'),
          'type'        => 'select',
          'desc'        => __('Select a sidebar', 'theme'),
          'placeholder' => false,
          'post_type'   => array( 'post' ),
          'callback'    => array( $this, '_theme_post_extra_sidebar_callback' ),
          ),
          'media_source'       => array(
          'label'       => __('Media Source', 'theme'),
          'type'        => 'text',
          'desc'        => __('Add a video link', 'theme'),
          'placeholder' => __('http://www.youtube.com/?v=xxxxxxxxx'),
          'post_type'   => array( 'post' )
          ),
          'article_source'       => array(
          'label'       => __('Article Source', 'theme'),
          'type'        => 'text',
          'desc'        => __('Add the title of source', 'theme'),
          'placeholder' => __('Title of source artilce'),
          'post_type'   => array( 'post' )
          ),
          'article_source_link' => array(
          'label'       => __('Article Source Link', 'theme'),
          'type'        => 'text',
          'desc'        => __('Add the link of article source', 'theme'),
          'placeholder' => __('http://www.news-article.com/article-id/'),
          'post_type'   => array( 'post' )
          ),
          'slider'               => array(
          //_slider_slide
          'label'       => __('Slider', 'theme'),
          'type'        => 'select',
          'desc'        => __('Select a slider', 'theme'),
          'placeholder' => false,
          'post_type'   => array( 'post', 'page' ),
          'callback'    => array( $this, '_theme_post_extra_slider_callback' ),
          ), */
        );
    }

    protected function get_meta_name($name) {
        return $this->prefix . $name;
    }

    /**
     * Adds the meta box container.
     */
    public function add_meta_box($post_type) {
        $post_types = array( 'post', 'page' );     //limit meta box to certain post types
        if (in_array($post_type, $post_types)) {
            add_meta_box('_theme_post_extra', __('Extra', 'theme'), array( $this, 'render_meta_box_content' ),
            $post_type, 'advanced', 'high');
        }
    }

    /**
     * Save the meta when the post is saved.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function save($post_id) {

        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */

        // Check if our nonce is set.
        if (filter_input(INPUT_POST, '_theme_post_extra_nonce') === false) {
            return $post_id;
        }

        $nonce = filter_input(INPUT_POST, '_theme_post_extra_nonce');

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($nonce, '_theme_post_extra')) {
            return $post_id;
        }


        // If this is an autosave, our form has not been submitted,
        //     so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // Check the user's permissions.
        if (filter_input(INPUT_POST, 'post_type') === 'page') {

            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        }
        else {

            if (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        }

        /* OK, its safe for us to save the data now. */

        foreach ($this->fields as $field_id => $data) {

            // Update the meta field.
            $old = get_post_meta($post_id, $this->get_meta_name($field_id), true);
            $new = filter_input(INPUT_POST, $this->get_meta_name($field_id));

            if ($data[ 'type' ] === 'file') {
                $new = url_without_domain($new);
            }

            if ($new && $new != $old) {

                // Sanitize the user input.
                switch ($data[ 'type' ]) {
                    case 'textarea':

                        if (!current_user_can('unfiltered_html')) {
                            $new = stripslashes(wp_filter_post_kses(addslashes($new)));
                        }

                        break;

                    case 'text':
                        $new = sanitize_text_field($new);
                        break;
                }

                update_post_meta($post_id, $this->get_meta_name($field_id), $new);
            }
            elseif ('' == $new && $old) {
                delete_post_meta($post_id, $this->get_meta_name($field_id));
            }
        }
    }

    /**
     * Render Meta Box content.
     *
     * @param WP_Post $post The post object.
     */
    public function render_meta_box_content($post) {

        // Add an nonce field so we can check for it later.
        wp_nonce_field('_theme_post_extra', '_theme_post_extra_nonce');

        // Begin the field table and loop
        $html = '<table class="form-table">';

        foreach ($this->fields as $field_id => $data) {

            if (!in_array($this->get_current_post_type(), $data[ 'post_type' ])) {
                continue;
            }

            // get value of this field if it exists for this post
            $meta = get_post_meta($post->ID, $this->get_meta_name($field_id), true);
            // begin a table row with

            $html .= '<tr>';
            $html .= '<th><label for="' . $this->get_meta_name($field_id) . '">' . $data[ 'label' ] . '</label></th>';
            $html .= '<td>';
            switch ($data[ 'type' ]) {
                // text
                case 'checkbox':
                    $html .= '<input name="' . $this->get_meta_name($field_id) . '" type="checkbox" id="' . $this->get_meta_name($field_id) . '"' . ($meta ? ' checked="checked"' : '') . '">';
                    $html .= '&nbsp;&nbsp;<span class="description">' . $data[ 'desc' ] . '</span>';

                    break;
                case 'text':
                    $html .= '<input type="text" name="' . $this->get_meta_name($field_id) . '" id="'
                    . '' . $this->get_meta_name($field_id) . '" value="' . $meta . '" placeholder="' . $data[ 'placeholder' ] . '" class="widefat"/>';
                    $html .= '<br /><span class="description">' . $data[ 'desc' ] . '</span>';

                    break;
                case 'textarea':
                    $html .= '<textarea name="' . $this->get_meta_name($field_id) . '" id="'
                    . '' . $this->get_meta_name($field_id) . '" placeholder="' . $data[ 'placeholder' ] . '" class="widefat" rows="4">' . $meta . '</textarea>';
                    $html .= '<br /><span class="description">' . $data[ 'desc' ] . '</span>';

                    break;
                case 'select':
                    $partial = '';

                    $data[ 'id' ]    = $this->get_meta_name($field_id);
                    $data[ 'value' ] = $meta;

                    if (is_callable($data[ 'callback' ])) {
                        $partial = call_user_func($data[ 'callback' ], $data);
                    }

                    $html .= $partial;

                    break;
                case 'file':

                    $html .= '<div class="media-uploader" data-id="' . $this->get_meta_name($field_id) . '">';

                    $html .= '<input class="media-uploader-input-value" type="hidden" name="' . $this->get_meta_name($field_id) . '" id="' . $this->get_meta_name($field_id) . '" value="' . $meta . '"/>';
                    //$html .= '<span class="">' . (empty($meta) ? $field[ 'desc' ] : $meta) . '</span>&nbsp;&nbsp;';
                    //$html .= '<a href="#' . $field[ 'id' ] . '" class="media-uploader-input media-uploader-button">[ ' . __('Edit') . ' ]</a>';
                    $html .= '<a href="#' . $this->get_meta_name($field_id) . '" class="media-uploader-input media-uploader-button">' . (empty($meta) ? $data[ 'desc' ] : basename($meta)) . '</a>';

                    $html .= '</div>';

                    //$html .= $uploader->renderReturn();

                    break;
            } //end switch
            $html .= '</td></tr>';
        } // end foreach
        $html .= '</table>'; // end table

        echo $html;
    }

    public function _theme_post_extra_sidebar_callback($args) {

        $opt = _theme_options::get_instance();

        $user_sidebars = $opt->get_option('sidebar', 'template');

        $sidebar_list = explode(',', $user_sidebars);

        $html = '<select id="' . $args[ 'id' ] . '" name="' . $args[ 'id' ] . '"><option value="">' . __('Default',
        'theme') . '</option>';

        foreach ($sidebar_list as $sidebar) {

            $sidebar = trim($sidebar);

            if (empty($sidebar)) {
                continue;
            }

            $id = 'user_sidebar_' . strtolower($sidebar);

            $html .= "<option value=\"{$id}\"";

            if ($args[ 'value' ] === $id) {
                $html .= ' selected="selected"';
            }
            $html .= ">{$sidebar}</option>";
        }

        $html .= '</select>';

        $html .= '<br /><span class="description">' . $args[ 'desc' ] . '</span>';

        return $html;
    }

    public function _theme_post_extra_header_callback($args) {

        $page_headers = get_posts(array(
            'post_type' => '_page_header',
        ));

        $selected = false;

        $html = '<select id="' . $args[ 'id' ] . '" name="' . $args[ 'id' ] . '"><option value="">' . __('No PageHeader',
        'theme') . '</option>';

        foreach ($page_headers as $item) {

            $html .= "<option value=\"{$item->ID}\"";

            if ((int) $args[ 'value' ] === $item->ID) {
                $html .= ' selected="selected"';
                $selected = true;
            }
            $html .= ">{$item->post_title}</option>";
        }

        $html .= '</select>';

        $html .= ' <a id="' . $args[ 'id' ] . '_edit" href="javascript:void(0)" class="button' . ($selected !== false ? '' : ' disabled') . '" target="_blank">Edit</a>';

        $html .= '<br /><span class="description">' . $args[ 'desc' ] . '</span>';
        ?>
        <script type="text/javascript">
            (function (window, $, undefined) {
                $(function () {
                    var select = $('#' + '<?php echo $args[ 'id' ]; ?>');
                    var button = $('#' + '<?php echo $args[ 'id' ]; ?>' + '_edit');

                    select.on('change', function () {
                        var option = $(this).find('option:selected');

                        if (option.val()) {
                            button.removeClass('disabled').attr('href', '/wp-admin/post.php?post=' + option.val() + '&action=edit');
                        } else {
                            button.addClass('disabled').attr('href', 'javascript:void(0)');
                        }
                    });
                });
            })(window, jQuery);
        </script>
        <?php
        return $html;
    }

    private function get_current_post_type() {
        global $post, $typenow, $current_screen;

        //we have a post so we can just get the post type from that
        if ($post && $post->post_type)
            return $post->post_type;

        //check the global $typenow - set in admin.php
        elseif ($typenow)
            return $typenow;

        //check the global $current_screen object - set in sceen.php
        elseif ($current_screen && $current_screen->post_type)
            return $current_screen->post_type;

        //lastly check the post_type querystring
        elseif (isset($_REQUEST[ 'post_type' ]))
            return sanitize_key($_REQUEST[ 'post_type' ]);

        //we do not know the post type!
        return null;
    }

}
