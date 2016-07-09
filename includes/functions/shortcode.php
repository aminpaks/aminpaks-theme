<?php

/**
 * PART
 */
function _theme_part_shortcode($atts_raw, $content = null) {

    $atts = shortcode_atts(array(
        'type'  => 'full',
        'class' => '',
    ), $atts_raw);

    if (!empty($atts[ 'class' ])) {
        $atts[ 'class' ] = ' ' . $atts[ 'class' ];
    }

    $html = '<div class="part part-' . esc_attr($atts[ 'type' ]) . esc_attr($atts[ 'class' ]) . '">';
    $html .= do_shortcode($content);
    $html .= '</div>';

    return $html;
}

add_shortcode('part', '_theme_part_shortcode');

/**
 * LAYOUT
 */
function _theme_shortcode_layout($__atts, $content = null) {
    $atts = shortcode_atts(array(
        'layout' => false,
        'class'  => false,
    ), $__atts);

    foreach ($atts as $key => $value) {
        $atts[ $key ] = esc_attr($value);
    }

    if ($atts[ 'layout' ] !== false) {
        $atts[ 'class' ] = trim(sprintf('layout-%s %s', $atts[ 'layout' ], $atts[ 'class' ]));
    }

    if (strpos($atts[ 'class' ], 'revers') === false) {
        $atts[ 'class' ] .= ' normal';
    }
    if ($atts[ 'class' ]) {
        $atts[ 'class' ] = ' ' . trim($atts[ 'class' ]);
    }

    $html = sprintf('<div class="layout%s"><div class="layout-holder">', $atts[ 'class' ]);
    $html .= $content;
    $html .= '</div></div>';

    return do_shortcode($html);
}

add_shortcode('layout', '_theme_shortcode_layout');

/**
 *  LAYOUT COL
 */
function _theme_shortcode_layout_col($__atts, $content = null) {
    $atts = shortcode_atts(array(
        'class' => false,
    ), $__atts);

    foreach ($atts as $key => $value) {
        $atts[ $key ] = esc_attr($value);
    }

    $html = sprintf('<div class="column">%s</div>', $content);

    return do_shortcode($html);
}

add_shortcode('col', '_theme_shortcode_layout_col');

/**
 * IFRAME SHORTCODE
 */
function _theme_shortcode_frame($__atts, $content = null) {
    $atts = shortcode_atts(array(
        'width'   => false,
        'height'  => false,
        'class'   => false,
        'link'    => false,
        'style'   => false,
        'exstyle' => false,
        'scroll'  => true,
        'content' => $content,
    ), $__atts);

    foreach ($atts as $key => $attr) {
        if ($key !== 'content') {
            if (gettype($atts[ $key ]) === 'string') {
                $atts[ $key ] = esc_attr($attr);
            }
        }
        else {
            $atts[ $key ] = preg_replace('/^<br\s*\/?>/i', '', $attr);
            $atts[ $key ] = preg_replace('/<br\s*\/?>$/i', '', $atts[ $key ]);
        }
    }

    if ($atts[ 'height' ] !== false) {
        $atts[ 'height' ] = sprintf('height="%s"', $atts[ 'height' ]);
    }
    else {
        $atts[ 'class' ] = 'auto-height';
    }

    if (empty($atts[ 'link' ]) || $atts[ 'link' ] === false) {
        return '';
    }

    /*
     * YOUTUBE:
     * https://www.youtube.com/embed/2zFKGcmSgHk
     * https://youtu.be/2zFKGcmSgHk
     */
    if (preg_match('/(https?:)?\/\/((www\.)?youtube\.com|youtu\.be)\/embed\/([^\/\?]+)(.*)/', $atts[ 'link' ], $groups)) {
        $atts[ 'link' ] = '//www.youtube.com/embed/' . $groups[ 4 ] . '?showinfo=0&rel=0&controls=2';
    }

    if (!empty($atts[ 'width' ])) {
        $atts[ 'style' ] = sprintf(' width: %s; %s', $atts[ 'width' ], $atts[ 'style' ]);
    }

    if (!empty($atts[ 'class' ])) {
        $atts[ 'class' ] = ' ' . make_html_class_string($atts[ 'class' ]);
    }

    if (!empty($atts[ 'style' ])) {
        $atts[ 'style' ] = sprintf(' style="%s"', trim($atts[ 'style' ], '; '));
    }

    if ($atts[ 'scroll' ] === true) {
        $atts[ 'scroll' ] = ' scrolling="yes"';
    }
    else {
        $atts[ 'scroll' ] = strtolower($atts[ 'scroll' ]);
        if ((int) $atts[ 'scroll' ] === 0 || $atts[ 'scroll' ] === 'false') {
            $atts[ 'scroll' ] = ' scrolling="no"';
        }
        else {
            $atts[ 'scroll' ] = ' scrolling="yes"';
        }
    }

    if ($atts[ 'exstyle' ] !== false) {
        $atts[ 'exstyle' ] = sprintf(' style="%s"', $atts[ 'exstyle' ]);
    }

    if (!empty($atts[ 'content' ])) {
        $atts[ 'content' ] = sprintf('<div class="iframe-caption">%s</div>', $atts[ 'content' ]);
    }

    $html = sprintf('<div class="iframe%s"%s><div class="iframe-holder"><div class="iframe-wrapper">'
    . '<iframe src="%s" %s%s%s frameborder="0" allowfullscreen="allowfullscreen"></iframe></div></div>%s</div>',
    $atts[ 'class' ], $atts[ 'style' ], $atts[ 'link' ], $atts[ 'height' ], $atts[ 'scroll' ], $atts[ 'exstyle' ],
    $atts[ 'content' ]);

    return '[raw]' . $html . '[/raw]';
}

add_shortcode('frame', '_theme_shortcode_frame');

/**
 * HEADLINE
 */
function _theme_shortcode_headline($_atts, $content = null) {
    $atts = shortcode_atts(array(
        'bold'     => false,
        'class'    => 'center',
        'title'    => false,
        'subtitle' => false,
    ), $_atts);

    if (empty($atts[ 'title' ])) {
        return false;
    }

    foreach ($atts as $key => $value) {
        if (gettype($value) === 'string') {
            $atts[ $key ] = esc_attr($value);
        }
        else {
            $atts[ $key ] = $value;
        }
    }

    if ($atts[ 'class' ] !== false) {
        $atts[ 'class' ] = ' ' . $atts[ 'class' ];
    }

    $html = sprintf('<div class="headline%s">', $atts[ 'class' ]);

    if (!empty($atts[ 'subtitle' ]) || $atts[ 'bold' ]) {
        $html .= sprintf('<h1>%1$s</h1>', $atts[ 'title' ]);
    }
    else {
        $html .= sprintf('<h2>%1$s</h2>', $atts[ 'title' ]);
    }

    if (!empty($atts[ 'subtitle' ])) {
        $html .= sprintf('<h2>%s</h2>', $atts[ 'subtitle' ]);
    }

    if (!empty($content)) {
        $html .= sprintf('<div class="description">%s</div>', $content);
    }

    $html .= '</div>';

    return $html;
}

add_shortcode('headline', '_theme_shortcode_headline');

/**
 * FIGURE
 */
function _theme_shortcode_figure($__atts, $content = null) {
    $atts = shortcode_atts(array(
        'class'    => false,
        'image'    => false,
        'link'     => false,
        'title'    => false,
        'content'  => $content,
        'imgstyle' => false,
    ), $__atts);

    foreach ($atts as $key => $value) {
        if ($key !== 'content') {
            if (gettype($value) == 'string') {
                $atts[ $key ] = esc_attr($value);
            }
        }
    }

    if (!empty($atts[ 'image' ])) {
        $image = $atts[ 'image' ];

        $backimage = strpos($atts[ 'class' ], 'back-image') !== false;

        $atts[ 'image' ] = sprintf('<div class="image-holder"><img src="%1$s" alt="%2$s"%3$s/>', $image,
        $atts[ 'title' ], $backimage ? ' class="invisible"' : '');
        if ($backimage) {
            $atts[ 'image' ] .= sprintf('<div class="image-wrapper" style="background-image: url(%1$s);%3$s"></div>',
            $image, $atts[ 'title' ], $atts[ 'imgstyle' ]);
        }
        $atts[ 'image' ] .= '</div>';
    }

    if (!empty($atts[ 'link' ])) {
        $atts[ 'image' ] = sprintf('<a href="%1$s">%2$s</a>', $atts[ 'link' ], $atts[ 'image' ]);
    }

    if (!empty($atts[ 'content' ])) {

        /* making figcaption for content */
        $atts[ 'content' ] = sprintf('<figcaption>%s</figcaption>', $atts[ 'content' ]);
    }

    if (!empty($atts[ 'class' ])) {
        $atts[ 'class' ] = sprintf(' class="%s"', trim($atts[ 'class' ]));
    }

    $html = sprintf('<figure%3$s>%1$s%2$s</figure>', $atts[ 'image' ], $atts[ 'content' ], $atts[ 'class' ]);

    return '[raw]' . $html . '[/raw]';
}

add_shortcode('figure', '_theme_shortcode_figure');

/**
 * SHARING BUTTONS
 */
function _theme_shortcode_share($atts) {
    $default_atts = shortcode_atts(array(
    ), $atts);

    $html = '<div class="sharing-btns">
                                    <div class="sharing-btns-holder">
                                        <div class="sharing-btns-wrapper">
                                            <a href="//www.facebook.com/share.php?u=' . get_the_permalink() . '" class="btn btn-facebook fa fa-facebook">Share on Facebook</a>
                                            <a href="//twitter.com/share?url=' . get_the_permalink() . '" class="btn btn-twitter fa fa-twitter">Share on Twitter</a>
                                        </div>
                                    </div>
                                </div>';


    return $html;
}

add_shortcode('share', '_theme_shortcode_share');

/**
 * BUTTON
 */
function _theme_shortcode_button($__atts) {
    $atts = shortcode_atts(array(
        'title'   => __('Untitle', 'theme'),
        'link'    => false,
        'class'   => false,
        'exclass' => false,
    ), $__atts);

    foreach ($atts as $key => $value) {
        if (gettype($value) == 'string') {
            $atts[ $key ] = esc_attr($value);
        }
    }

    $target = '';

    if (preg_match('/(http:)?(\/\/)(www\.)?([^\/]+)/is', $atts[ 'link' ], $match)) {

        $home_url = get_home_url();

        preg_match('/(http:)?(\/\/)(www\.)?([^\/]+)/is', $home_url, $match2);

        if ($match[ 4 ] !== $match2[ 4 ]) {
            $target = ' target="_blank"';
        }
    }

    if ($atts[ 'exclass' ]) {
        $atts[ 'exclass' ] = sprintf(' class="%s"', $atts[ 'exclass' ]);
    }

    $html = '<div class="button' . ($atts[ 'class' ] ? ' ' . $atts[ 'class' ] : '') . '">'
    . '<div class="button-holder">'
    . '<div class="button-wrapper">'
    . '<a' . ($atts[ 'link' ] !== false ? sprintf(' href="%s"', $atts[ 'link' ]) : '') . $target . ($atts[ 'exclass' ] ? $atts[ 'exclass' ] : '') . '>'
    . html_entity_decode($atts[ 'title' ])
    . '</a>'
    . '</div>'
    . '</div>'
    . '</div>';

    return $html;
}

add_shortcode('button', '_theme_shortcode_button');

/**
 *  MAP
 */
function _theme_shortcode_map($__atts) {
    $atts = shortcode_atts(array(
        'title'  => 'DEFAULT-TITLE',
        'coords' => false,
    ), $__atts);

    foreach ($atts as $key => $value) {
        if (gettype($value) === 'string') {
            $atts[ $key ] = esc_attr($value);
        }
    }

    if ($atts[ 'coords' ] === false) {
        return false;
    }

    $html = sprintf('<div class="google-map"><div class="google-map-canvas" data-title="%s" data-coords="%s"></div></div>',
    $atts[ 'title' ], $atts[ 'coords' ]);

    return $html;
}

add_shortcode('map', '_theme_shortcode_map');

/**
 * CONTROL
 */
function _theme_shortcode_control($__atts) {

    // make a clean variable
    $atts = shortcode_atts(array(
        'name' => false,
        'lang' => 'default',
        'vars' => false,
    ), $__atts);

    $control_name = 'templates/control-' . esc_attr($atts[ 'name' ]);

    if (!file_exists(TEMPLATEPATH . '/' . $control_name . '.php')) {
        return false;
    }

    if ($atts[ 'vars' ] !== false) {

        $json = preg_replace('/({|,)([^\w]*)([^\s]*)(:)/s', '$1$2"$3"$4', $atts[ 'vars' ]);
        $json = str_replace('\'', '"', $json);
        //$json = str_replace("'", '"', $clean_atts[ 'vars' ]);

        $array = json_decode($json, true);

        if (is_array($array)) {

            global $control_vars;

            $control_vars = $array;
        }
    }

    // interpreting the control
    ob_start();

    get_template_part($control_name);
    $return = ob_get_contents();

    ob_end_clean();

    if (empty($return) || strlen($return) <= 0) {
        return false;
    }

    return '[raw]' . $return . '[/raw]';
}

add_shortcode('control', '_theme_shortcode_control');

/**
 * HEADER
 */
function _theme_shortcode_container($__atts, $content = null) {
    $atts = shortcode_atts(array(
        'class'  => false,
        'anchor' => false,
        'role'   => false,
        'image'  => false,
    ), $__atts);

    foreach ($atts as $key => $value) {
        if (gettype($value) === 'string') {
            $atts[ $key ] = esc_attr($value);
        }
        else {
            $atts[ $key ] = $value;
        }
    }
    
    $atts[ 'attr' ] = '';

    if ($atts[ 'class' ]) {
        $atts[ 'class' ] = ' ' . trim($atts[ 'class' ]);
    }

    if (!empty($atts[ 'anchor' ])) {
        $atts[ 'attr' ] .= sprintf(' id="%s"', $atts[ 'anchor' ]);
    }
    
    if (!empty($atts['image'])) {
        $atts[ 'attr' ] .= sprintf(' style="background-image: url(%s)"', $atts[ 'image' ]);
    }

    $html              = '';
    $atts[ 'closure' ] = '';

    if ($atts[ 'role' ] === 'section') {
        $html .= sprintf('<div class="section%s"%s><div class="container">', $atts[ 'class' ], $atts[ 'attr' ]);

        $atts[ 'closure' ] .= '</div></div>';
    }
    else {
        $html .= sprintf('<div class="container%s"%s>', $atts[ 'class' ], $atts[ 'attr' ]);
        $atts[ 'closure' ] .= '</div>';
    }

    $html .= $content;

    $html .= $atts[ 'closure' ];

    return do_shortcode($html);
}

add_shortcode('container', '_theme_shortcode_container');

/**
 * HEADER
 */
function _theme_shortcode_header($__atts, $content = null) {
    $atts = shortcode_atts(array(
        'style' => '',
        'image' => false,
        'class' => false,
    ), $__atts);

    foreach ($atts as $key => $value) {
        if (gettype($value) === 'string') {
            $atts[ $key ] = esc_attr($value);
        }
        else {
            $atts[ $key ] = $value;
        }
    }

    if ($atts[ 'class' ]) {
        $atts[ 'class' ] = ' ' . trim($atts[ 'class' ]);
    }

    if ($atts[ 'image' ]) {
        $atts[ 'style' ] = sprintf(' background-image: url(%s);', $atts[ 'image' ]);
    }

    if (!empty(trim($atts[ 'style' ]))) {
        $atts[ 'style' ] = sprintf(' style="%s"', $atts[ 'style' ]);
    }

    $html = sprintf('<div class="header%s"%s>', $atts[ 'class' ], $atts[ 'style' ]);
    $html .= '<div class="header-holder"><div class="header-wrapper">';
    $html .= $content;
    $html .= '</div><!-- .header-wrapper !--></div><!-- .header-holder !-->';

    if (!empty($atts[ 'image' ])) {
        $html .= sprintf('<img src="%s" class="invisible" />', $atts[ 'image' ]);
    }

    $html .= '</div><!-- .header !-->';

    return do_shortcode('[raw]' . $html . '[/raw]');
}

add_shortcode('header', '_theme_shortcode_header');

/**
 * CONTENT
 */
function _theme_shortcode_content($__atts, $content = null) {
    $atts = shortcode_atts(array(
        'class' => false,
    ), $__atts);

    foreach ($atts as $key => $value) {
        if (gettype($value) === 'string') {
            $atts[ $key ] = esc_attr($value);
        }
        else {
            $atts[ $key ] = $value;
        }
    }

    if ($atts[ 'class' ]) {
        $atts[ 'class' ] = ' ' . trim($atts[ 'class' ]);
    }

    $html = sprintf('<div class="content%s">', $atts[ 'class' ]);
    $html .= $content;
    $html .= '</div>';

    return do_shortcode($html);
}

add_shortcode('content', '_theme_shortcode_content');

/**
 * MENU NAVIGATION
 */
function _theme_shortcode_navgen($__atts) {
    $atts = shortcode_atts(array(
        'name' => false,
    ), $__atts);

    foreach ($atts as $key => $value) {
        if (gettype($value) === 'string') {
            $atts[ $key ] = esc_attr($value);
        }
        else {
            $atts[ $key ] = $value;
        }
    }

    if (empty($atts[ 'name' ])) {
        return false;
    }

    $menus = wp_nav_menu(array(
        'menu'            => $atts[ 'name' ],
        'container'       => '',
        'container_class' => '',
        'items_wrap'      => '<ul>%3$s</ul>',
        'echo'            => false,
    ));


    $html = sprintf('<div class="x-navigation">%s</div>', $menus);

    return $html;
}

add_shortcode('nav', '_theme_shortcode_navgen');
