<?php

/**
 * INITIALIZE A CUSTOM POST
 */
add_action('init', '_slider_slide_init');

function _slider_slide_init() {
    // The custom post type prefix
    $type_prefix = '_slider_slide';

    global $_slider_slide_data;

    $_slider_slide_data = array(
        'prefix'    => $type_prefix,
        'register'  => array(
            'labels'    => array(
                'name'           => __('Slides', 'theme'),
                'singulare_name' => __('Slide', 'theme'),
                'add_new_item'   => __('Add New "Slide"', 'theme'),
                'edit_item'      => __('Edit "Slide"', 'theme'),
            ),
            'public'    => true,
            'rewrite'   => array( 'slug' => 'slide' ),
            'menu_icon' => 'dashicons-format-gallery',
            'supports'  => array( 'title', 'editor', 'author', 'thumbnail', 'page-attributes' )
        ),
        'taxonomy'  => array(
            array(
                'hierarchical' => true,
                'labels'       => array(
                    'name'              => 'Sliders',
                    'singular_name'     => 'Slider',
                    'search_items'      => 'Search',
                    'all_items'         => 'All Sliders',
                    'parent_item'       => 'Parent Slider',
                    'parent_item_colon' => 'Parent Slider:',
                    'edit_item'         => 'Edit Slider',
                    'update_item'       => 'Update Slider',
                    'add_new_item'      => 'Add New Slider',
                    'new_item_name'     => 'New Slider',
                    'menu_name'         => 'Sliders',
                ),
                'show_ui'      => true,
                'rewrite'      => array( 'slug', 'slider' )
            )
        ),
        'metadata'  => array(
            /*array(
                'label' => __('Height', 'theme'),
                'desc'  => __('Height of this slide', 'theme'),
                'id'    => $type_prefix . '_height',
                'type'  => 'text',
            ),*/
            array(
                'label' => __('Link', 'theme'),
                'desc'  => __('Link this slide to an URL', 'theme'),
                'id'    => $type_prefix . '_link',
                'type'  => 'text',
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

    register_post_type($type_prefix, $_slider_slide_data[ 'register' ]);

    if (isset($_slider_slide_data[ 'taxonomy' ]) && gettype($_slider_slide_data[ 'taxonomy' ]) === 'array') {
        foreach ($_slider_slide_data[ 'taxonomy' ] as $taxonomy) {
            register_taxonomy($type_prefix . '_group', $type_prefix, $taxonomy);
        }
    }

    if (isset($_slider_slide_data[ 'metaboxes' ])) {
        // metaboxes
        add_action('add_meta_boxes', $type_prefix . '_meta_addboxes');

        // metas list
        add_action('save_post', $type_prefix . '_meta_save');
    }
}

// Add team member meta
function _slider_slide_meta_addboxes() {
    global $_slider_slide_data;

    if (isset($_slider_slide_data[ 'metaboxes' ]) && gettype($_slider_slide_data[ 'metaboxes' ]) === 'array') {
        foreach ($_slider_slide_data[ 'metaboxes' ] as $metabox) {
            call_user_func_array('add_meta_box', $metabox);
        }
    }
}

// The team member meta callback
function _slider_slide_meta_show() {
    global $_slider_slide_data, $post;

    $type_prefix = $_slider_slide_data[ 'prefix' ];


    // Use nonce for verification
    echo '<input type="hidden" name="' . $type_prefix . '_meta_nonce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';

    // Begin the field table and loop
    $html = '<table class="form-table">';
    foreach ($_slider_slide_data[ 'metadata' ] as $field) {
        // get value of this field if it exists for this post
        $meta = get_post_meta($post->ID, $field[ 'id' ], true);
        // begin a table row with
        $html .= '<tr>';
        $html .= '<th><label for="' . $field[ 'id' ] . '">' . $field[ 'label' ] . '</label></th>';
        $html .= '<td>';
        switch ($field[ 'type' ]) {
            // text
            case 'text':
                $html .= '<input type="text" name="' . $field[ 'id' ] . '" id="' . $field[ 'id' ] . '" value="' . $meta . '" size="30" />';
                $html .= '<br /><span class="description">' . $field[ 'desc' ] . '</span>';
                break;
        } //end switch
        $html .= '</td></tr>';
    } // end foreach
    $html .= '</table>'; // end table

    echo $html;
}

// Save the team member metas
function _slider_slide_meta_save($post_id) {
    global $_slider_slide_data;

    $type_prefix = $_slider_slide_data[ 'prefix' ];

    // verify nonce
    if (!wp_verify_nonce(filter_input(INPUT_POST, $type_prefix . '_meta_nonce'), basename(__FILE__))) {
        return $post_id;
    }

    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // check permissions
    if ($type_prefix == filter_input(INPUT_POST,'post_type')) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    }
    elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    // loop through fields and save the data
    foreach ($_slider_slide_data[ 'metadata' ] as $field) {
        $old = get_post_meta($post_id, $field[ 'id' ], true);
        $new = filter_input(INPUT_POST,$field[ 'id' ]);
        if ($new && $new != $old) {
            update_post_meta($post_id, $field[ 'id' ], $new);
        }
        elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field[ 'id' ], $old);
        }
    }
    // end foreach
}
