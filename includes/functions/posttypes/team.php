<?php

/**
 * INITIALIZE A CUSTOM POST
 */
add_action('init', '_team_member_init');

function _team_member_init() {
    // The custom post type prefix
    $type_prefix = '_team_member';

    global $_team_member_data;

    $_team_member_data = array(
        'prefix'    => $type_prefix,
        'register'  => array(
            'labels'    => array(
                'name'               => __('Members', 'theme'),
                'singulare_name'     => __('Member', 'theme'),
                'menu_name'          => __('Team', 'theme'),
                'name_admin_bar'     => __('Member', 'theme'),
                'all_items'          => __('All Members', 'theme'),
                'add_new'            => __('New Member', 'theme'),
                'add_new_item'       => __('Add New Member', 'theme'),
                'edit_item'          => __('Edit Member', 'theme'),
                'new_item'           => __('New Member', 'theme'),
                'view_item'          => __('View Member', 'theme'),
                'search_items'       => __('Search Members', 'theme'),
                'not_found'          => __('No member found.', 'theme'),
                'not_found_in_trash' => __('No member found in trash.', 'theme'),
                'parent_item_colon'  => __('Superior Member:', 'theme'),
            ),
            'rewrite'   => array( 'slug' => 'member', 'with_front' => false ),
            'menu_icon' => 'dashicons-businessman',
            'supports'  => array( 'title', 'editor'/* , 'author' */, 'thumbnail', 'page-attributes' ),
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
                'label'       => __('Website', 'theme'),
                'placeholder' => 'http://www.website.com',
                'id'          => $type_prefix . '_website',
                'type'        => 'text',
            ),
            array(
                'label'       => __('Email', 'theme'),
                'placeholder' => 'fullname@trademark.com',
                'id'          => $type_prefix . '_email',
                'type'        => 'text',
            ),
            array(
                'label'       => __('Twitter', 'theme'),
                'placeholder' => 'http://www.twitter.com/username',
                'id'          => $type_prefix . '_twitter',
                'type'        => 'text',
            ),
            array(
                'label'       => __('LinkedId', 'theme'),
                'placeholder' => 'http://www.linkedin.com/in/username',
                'id'          => $type_prefix . '_linkedin',
                'type'        => 'text',
            ),
            array(
                'label'       => __('Position Title', 'theme'),
                'placeholder' => __('CEO & Chairman', 'theme'),
                'id'          => $type_prefix . '_position_title',
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

    register_post_type($type_prefix, $_team_member_data[ 'register' ]);

    if (isset($_team_member_data[ 'taxonomy' ]) && gettype($_team_member_data[ 'taxonomy' ]) === 'array') {
        foreach ($_team_member_data[ 'taxonomy' ] as $taxonomy) {
            register_taxonomy($taxonomy[ 'taxonomy_slug' ], $type_prefix, $taxonomy);
        }
    }

    if (isset($_team_member_data[ 'metaboxes' ])) {
        // metaboxes
        add_action('add_meta_boxes', $type_prefix . '_meta_addboxes');

        // metas list
        add_action('save_post', $type_prefix . '_meta_save');
    }


    //add_image_size('member-thumb', 500, 500, true);
    // THUMBNAILS TO ADMIN POST VIEW

    add_filter('manage__team_member_posts_columns', '_team_member_posts_columns', 5);
    add_action('manage__team_member_posts_custom_column', '_team_member_posts_custom_column', 5, 2);
    add_action('admin_enqueue_scripts', '_team_member_posts_column_resize');

    add_filter("manage_edit-the-team_columns", '_team_member_role_columns');
}

// Add team member meta
function _team_member_meta_addboxes() {
    global $_team_member_data;

    if (isset($_team_member_data[ 'metaboxes' ]) && gettype($_team_member_data[ 'metaboxes' ]) === 'array') {
        foreach ($_team_member_data[ 'metaboxes' ] as $metabox) {
            call_user_func_array('add_meta_box', $metabox);
        }
    }
}

// The team member meta callback
function _team_member_meta_show() {
    global $_team_member_data, $post;

    $type_prefix = $_team_member_data[ 'prefix' ];


    // Use nonce for verification
    echo '<input type="hidden" name="' . $type_prefix . '_meta_nonce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';

    // Begin the field table and loop
    $html = '<table class="form-table">';
    foreach ($_team_member_data[ 'metadata' ] as $field) {
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
function _team_member_meta_save($post_id) {
    global $_team_member_data;

    $type_prefix = $_team_member_data[ 'prefix' ];

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
    foreach ($_team_member_data[ 'metadata' ] as $field) {
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

// resize columns in post listing screen
function _team_member_posts_column_resize() {
    ?>
    <style type="text/css">
        .edit-php .wp-list-table .column-member-thumb {
            width: 80px;
        }
        .edit-php .wp-list-table .member-thumb-wrapper {
            width: 80px;
            height: 80px;
            overflow: hidden;
            background-size: cover;
            display: inline-block;
            vertical-align: middle;
        }
    </style>
    <?php

}

function _team_member_posts_columns($defaults) {
    $idx         = 0;
    $new_columns = array();

    foreach ($defaults as $key => $col) {

        if ($idx === 1) {
            $new_columns[ 'member-thumb' ] = 'Photo';
        }

        $new_columns[ $key ] = $col;

        $idx++;
    }

    return $new_columns;
}

function _team_member_role_columns($role_columns) {

    unset($role_columns[ 'description' ]);

    return $role_columns;
}

function _team_member_posts_custom_column($column_name, $id) {
    if ($column_name === 'member-thumb') {
        echo sprintf('<div class="member-thumb-wrapper" style="background-image: url(%s);"></div>',
        get_the_featured_image('thumbnail', $id));
    }
}
