<?php

/**
 * INITIALIZE A CUSTOM POST
 */
add_action('init', '_work_init');

function _work_init() {
    // The custom post type prefix
    $type_prefix = '_work';

    global $_work_data;

    $_work_data = array(
        'prefix'    => $type_prefix,
        'register'  => array(
            'labels'     => array(
                'name'               => __('Projects', 'theme'),
                'singulare_name'     => __('Project', 'theme'),
                'menu_name'          => __('Projects', 'theme'),
                'name_admin_bar'     => __('Project', 'theme'),
                'all_items'          => __('All Projects', 'theme'),
                'add_new'            => __('New Project', 'theme'),
                'add_new_item'       => __('Add New Project', 'theme'),
                'edit_item'          => __('Edit Project', 'theme'),
                'new_item'           => __('New Project', 'theme'),
                'view_item'          => __('View Project', 'theme'),
                'search_items'       => __('Search Projects', 'theme'),
                'not_found'          => __('No project found.', 'theme'),
                'not_found_in_trash' => __('No project found in trash.', 'theme'),
                'parent_item_colon'  => __('Superior Project:', 'theme'),
            ),
            'rewrite'    => array( 'slug' => 'work', 'with_front' => false ),
            'menu_icon'  => 'dashicons-tickets-alt',
            'supports'   => array( 'title', 'editor'/* , 'author' */, 'thumbnail'/* , 'page-attributes' */ ),
            'public'     => true,
            //'publicly_queriable'  => true,
            //'show_ui'             => true,
            //'show_in_nav_menus'   => true,
            //'exclude_from_search' => false,
            'taxonomies' => array( 'work-filter' ),
        ),
        'taxonomy'  => array(
            array(
                'hierarchical'  => false,
                'labels'        => array(
                    'name'              => 'Filters',
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
                'public'        => true,
                'show_ui'       => true,
                'show_tagcloud' => false,
                'rewrite'       => array( 'slug' => 'work-filter', 'with_front' => false ),
                'taxonomy_slug' => 'work-filter',
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
        )
    );

    register_post_type($type_prefix, $_work_data[ 'register' ]);

    if (isset($_work_data[ 'taxonomy' ]) && gettype($_work_data[ 'taxonomy' ]) === 'array') {
        foreach ($_work_data[ 'taxonomy' ] as $taxonomy) {
            register_taxonomy($taxonomy[ 'taxonomy_slug' ], $type_prefix, $taxonomy);
        }
    }

    if (isset($_work_data[ 'metaboxes' ])) {
        // metaboxes
        add_action('add_meta_boxes', $type_prefix . '_meta_addboxes');

        // metas list
        add_action('save_post', $type_prefix . '_meta_save');
    }

    add_filter("manage_edit-the-team_columns", '_work_role_columns');
}

// Add team member meta
function _work_meta_addboxes() {
    global $_work_data;

    if (isset($_work_data[ 'metaboxes' ]) && gettype($_work_data[ 'metaboxes' ]) === 'array') {
        foreach ($_work_data[ 'metaboxes' ] as $metabox) {
            call_user_func_array('add_meta_box', $metabox);
        }
    }
}

// The team member meta callback
function _work_meta_show() {
    global $_work_data, $post;

    $type_prefix = $_work_data[ 'prefix' ];


    // Use nonce for verification
    echo '<input type="hidden" name="' . $type_prefix . '_meta_nonce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';

    // Begin the field table and loop
    $html = '<table class="form-table">';
    foreach ($_work_data[ 'metadata' ] as $field) {
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
function _work_meta_save($post_id) {
    global $_work_data;

    $type_prefix = $_work_data[ 'prefix' ];

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
    foreach ($_work_data[ 'metadata' ] as $field) {
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

function _work_role_columns($role_columns) {

    unset($role_columns[ 'description' ]);

    return $role_columns;
}
