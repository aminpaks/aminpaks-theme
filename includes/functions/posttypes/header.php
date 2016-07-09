<?php

/**
 * INITIALIZE A CUSTOM POST
 */
add_action('init', '_page_header_init');

function _page_header_init() {
    // The custom post type prefix
    $type_prefix = '_page_header';

    global $_page_header_data;

    $_page_header_data = array(
        'prefix'    => $type_prefix,
        'register'  => array(
            'labels'     => array(
                'name'               => __('PHeaders', 'theme'),
                'singulare_name'     => __('PHeader', 'theme'),
                'menu_name'          => __('PHeaders', 'theme'),
                'name_admin_bar'     => __('PHeader', 'theme'),
                'all_items'          => __('All PHeaders', 'theme'),
                'add_new'            => __('New PHeader', 'theme'),
                'add_new_item'       => __('Add New PHeader', 'theme'),
                'edit_item'          => __('Edit PHeader', 'theme'),
                'new_item'           => __('New PHeader', 'theme'),
                'view_item'          => __('View PHeader', 'theme'),
                'search_items'       => __('Search PHeaders', 'theme'),
                'not_found'          => __('No pheader found.', 'theme'),
                'not_found_in_trash' => __('No pheader found in trash.', 'theme'),
                'parent_item_colon'  => __('Superior PHeader:', 'theme'),
            ),
            'menu_position' => 24,
            'rewrite'    => array( 'slug' => 'page-header', 'with_front' => false ),
            'menu_icon'  => 'dashicons-id',
            'supports'   => array( 'title', 'editor'/* , 'author' */, 'thumbnail'/* , 'page-attributes' */ ),
            'public'     => false,
            //'publicly_queriable'  => true,
            'show_ui'             => true,
            'show_in_nav_menus'   => true,
            //'exclude_from_search' => false,
            //'taxonomies' => array( 'page-header' ),
        ),
        /*
        'taxonomy'  => array(
            array(
                'hierarchical'  => false,
                'labels'        => array(
                    'name'              => 'Headers',
                    'singular_name'     => 'Filter',
                    'search_items'      => 'Search filters',
                    'all_items'         => 'All Filters',
                    'parent_item'       => 'Parent Filter',
                    'parent_item_colon' => 'Parent Filter:',
                    'edit_item'         => 'Edit Filter',
                    'update_item'       => 'Update Filter',
                    'add_new_item'      => 'Add New Filter',
                    'new_item_name'     => 'New Filter',
                    'menu_name'         => 'Filters',
                ),
                'public'        => false,
                'show_ui'       => true,
                'show_tagcloud' => false,
                'rewrite'       => array( 'slug' => 'page-header', 'with_front' => false ),
                'taxonomy_slug' => 'page-header',
            )
        ),
        'metadata'  => array(
            array(
                'label' => __('Splash', 'theme'),
                //'placeholder' => __('http://www.youtube.com/watch?v=xxxxx', 'theme'),
                'id'    => $type_prefix . '_splash_image',
                'type'  => 'file',
                'desc'  => __('Upload / Select the picture', 'theme'),
            ),
        ),
        'metaboxes' => array(
            array(
                $type_prefix . '_meta', // $id
                __('Meta', 'theme'), // $title
                $type_prefix . '_meta_show', // $callback
                $type_prefix, // $page
                'normal', // $context
                'high' // $priority
            ),
        )*/
    );

    register_post_type($type_prefix, $_page_header_data[ 'register' ]);

    if (isset($_page_header_data[ 'taxonomy' ]) && gettype($_page_header_data[ 'taxonomy' ]) === 'array') {
        foreach ($_page_header_data[ 'taxonomy' ] as $taxonomy) {
            register_taxonomy($taxonomy[ 'taxonomy_slug' ], $type_prefix, $taxonomy);
        }
    }

    if (isset($_page_header_data[ 'metaboxes' ])) {
        // metaboxes
        add_action('add_meta_boxes', $type_prefix . '_meta_addboxes');

        // metas list
        add_action('save_post', $type_prefix . '_meta_save');
    }

    add_filter('manage_edit-the-team_columns', '_page_header_role_columns');
}

// Add team member meta
function _page_header_meta_addboxes() {
    global $_page_header_data;

    if (isset($_page_header_data[ 'metaboxes' ]) && gettype($_page_header_data[ 'metaboxes' ]) === 'array') {
        foreach ($_page_header_data[ 'metaboxes' ] as $metabox) {
            call_user_func_array('add_meta_box', $metabox);
        }
    }
}

// The team member meta callback
function _page_header_meta_show() {
    global $_page_header_data, $post;

    $type_prefix = $_page_header_data[ 'prefix' ];


    // Use nonce for verification
    echo '<input type="hidden" name="' . $type_prefix . '_meta_nonce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';

    // Begin the field table and loop
    $html = '<table class="form-table">';
    foreach ($_page_header_data[ 'metadata' ] as $field) {
        // get value of this field if it exists for this post
        $meta = get_post_meta($post->ID, $field[ 'id' ], true);
        // begin a table row with
        $html .= '<tr>';
        $html .= '<th><label for="' . $field[ 'id' ] . '">' . $field[ 'label' ] . '</label></th>';
        $html .= '<td>';
        switch ($field[ 'type' ]) {
            // text
            case 'text':
                $html .= '<input type="text" name="' . $field[ 'id' ] . '" id="' . $field[ 'id' ] . '" value="' . $meta . '" class="widefat" placeholder="' . $field[ 'placeholder' ] . '" />';
                break;
            case 'file':

                $html .= '<div class="media-uploader" data-id="' . $field[ 'id' ] . '">';

                $html .= '<input class="media-uploader-input-value" type="hidden" name="' . $field[ 'id' ] . '" id="' . $field[ 'id' ] . '" value="' . $meta . '"/>';
                $html .= '<a href="#' . $field[ 'id' ] . '" class="media-uploader-input media-uploader-button">' . (empty($meta) ? $field[ 'desc' ] : basename($meta)) . '</a>';

                $html .= '</div>';

                break;
        } //end switch
        $html .= '</td></tr>';
    } // end foreach
    $html .= '</table>'; // end table

    echo $html;
}

// Save the team member metas
function _page_header_meta_save($post_id) {
    global $_page_header_data;

    $type_prefix = $_page_header_data[ 'prefix' ];

    // verify nonce
    if (!wp_verify_nonce(filter_input(INPUT_POST, $type_prefix . '_meta_nonce'), basename(__FILE__))) {
        return $post_id;
    }

    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // check permissions
    if ($type_prefix == filter_input(INPUT_POST, 'post_type')) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    }
    elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    // loop through fields and save the data
    foreach ($_page_header_data[ 'metadata' ] as $field) {
        $old = get_post_meta($post_id, $field[ 'id' ], true);
        $new = filter_input(INPUT_POST, $field[ 'id' ]);
        if ($new && $new != $old) {
            update_post_meta($post_id, $field[ 'id' ], $new);
        }
        elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field[ 'id' ], $old);
        }
    }
    // end foreach
}

function _page_header_role_columns($role_columns) {

    unset($role_columns[ 'description' ]);

    return $role_columns;
}
