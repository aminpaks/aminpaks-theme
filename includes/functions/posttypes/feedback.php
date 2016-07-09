<?php

/**
 * INITIALIZE A CUSTOM POST
 */
add_action('init', '_feedback_init');

function _feedback_init() {
    // The custom post type prefix
    $type_prefix = '_feedback';

    global $_feedback_data;

    $_feedback_data = array(
        'prefix'    => $type_prefix,
        'register'  => array(
            'labels'    => array(
                'name'               => __('Feedbacks', 'theme'),
                'singulare_name'     => __('Feedback', 'theme'),
                'menu_name'          => __('Feedback', 'theme'),
                'name_admin_bar'     => __('Feedback', 'theme'),
                'all_items'          => __('All Feedbacks', 'theme'),
                'add_new'            => __('New Feedback', 'theme'),
                'add_new_item'       => __('Add New Feedback', 'theme'),
                'edit_item'          => __('Edit Feedback', 'theme'),
                'new_item'           => __('New Feedback', 'theme'),
                'view_item'          => __('View Feedback', 'theme'),
                'search_items'       => __('Search Feedbacks', 'theme'),
                'not_found'          => __('No feedback found.', 'theme'),
                'not_found_in_trash' => __('No feedback found in trash.', 'theme'),
                'parent_item_colon'  => __('Superior Feedback:', 'theme'),
            ),
            'rewrite'   => array( 'slug' => 'member', 'with_front' => false ),
            'menu_icon' => 'dashicons-format-chat',
            'supports'  => array( 'title', 'editor'/* , 'author', 'thumbnail', 'page-attributes' */ ),
            'public'              => false,
            'publicly_queriable'  => false,
            'show_ui'             => true,
            'show_in_nav_menus'   => true,
            'exclude_from_search' => true,
        ),
        /*'taxonomy'  => array(
            array(
                'hierarchical'  => true,
                'labels'        => array(
                    'name'              => 'Team Groups',
                    'singular_name'     => 'Group',
                    'search_items'      => 'Search groups',
                    'all_items'         => 'All Groups',
                    'parent_item'       => 'Parent Group',
                    'parent_item_colon' => 'Parent Group:',
                    'edit_item'         => 'Edit Group',
                    'update_item'       => 'Update Group',
                    'add_new_item'      => 'Add New Group',
                    'new_item_name'     => 'New Group',
                    'menu_name'         => 'Groups',
                ),
                'show_ui'       => true,
                'rewrite'       => array( 'slug' => 'the-team', 'with_front' => false ),
                'taxonomy_slug' => 'the-team',
            )
        ),*/
        'metadata'  => array(
            array(
                'label'       => __('Client\'s Name', 'theme'),
                'placeholder' => __('Adam Johnson', 'theme'),
                'id'          => $type_prefix . '_name',
                'type'        => 'text',
            ),
            array(
                'label'       => __('Client\'s Title', 'theme'),
                'placeholder' => __('CEO', 'theme'),
                'id'          => $type_prefix . '_title',
                'type'        => 'text',
            ),
            array(
                'label'       => __('Client\'s Company', 'theme'),
                'placeholder' => __('ROI Land Investments, LTD', 'theme'),
                'id'          => $type_prefix . '_company',
                'type'        => 'text',
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

    register_post_type($type_prefix, $_feedback_data[ 'register' ]);

    if (isset($_feedback_data[ 'taxonomy' ]) && gettype($_feedback_data[ 'taxonomy' ]) === 'array') {
        foreach ($_feedback_data[ 'taxonomy' ] as $taxonomy) {
            register_taxonomy($taxonomy[ 'taxonomy_slug' ], $type_prefix, $taxonomy);
        }
    }

    if (isset($_feedback_data[ 'metaboxes' ])) {
        // metaboxes
        add_action('add_meta_boxes', $type_prefix . '_meta_addboxes');

        // metas list
        add_action('save_post', $type_prefix . '_meta_save');
    }

    add_filter('manage_edit-the-team_columns', '_feedback_role_columns');
}

// Add team member meta
function _feedback_meta_addboxes() {
    global $_feedback_data;

    if (isset($_feedback_data[ 'metaboxes' ]) && gettype($_feedback_data[ 'metaboxes' ]) === 'array') {
        foreach ($_feedback_data[ 'metaboxes' ] as $metabox) {
            call_user_func_array('add_meta_box', $metabox);
        }
    }
}

// The team member meta callback
function _feedback_meta_show() {
    global $_feedback_data, $post;

    $type_prefix = $_feedback_data[ 'prefix' ];


    // Use nonce for verification
    echo '<input type="hidden" name="' . $type_prefix . '_meta_nonce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';

    // Begin the field table and loop
    $html = '<table class="form-table">';
    foreach ($_feedback_data[ 'metadata' ] as $field) {
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
        } //end switch
        $html .= '</td></tr>';
    } // end foreach
    $html .= '</table>'; // end table

    echo $html;
}

// Save the team member metas
function _feedback_meta_save($post_id) {
    global $_feedback_data;

    $type_prefix = $_feedback_data[ 'prefix' ];

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
    foreach ($_feedback_data[ 'metadata' ] as $field) {
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

function _feedback_role_columns($role_columns) {

    unset($role_columns[ 'description' ]);

    return $role_columns;
}
