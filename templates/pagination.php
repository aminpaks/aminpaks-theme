<?php
/*
 *  @license © 2014, Amin Paks, T. (514) 441-2413, W. http://www.aminpaks.com
 */

global $paginate_query, $wp_rewrite;

if (isset($paginate_query) && $paginate_query->max_num_pages > 1) {

    // CURRENT PAGE
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    $array = array();

    $span_format  = '<span%s>%s</span>';
    $anchr_format = '<a href="%s">%s</a>';

    $tmp_link = get_pagenum_link();

    $page_link_format = trailingslashit($tmp_link . $wp_rewrite->pagination_base . '/%d/');

    for ($i = 1; $i <= $paginate_query->max_num_pages; $i++) {

        if ($paged === $i) {
            $array[] = sprintf($span_format, '', $i);
        }
        else {
            $array[] = sprintf($anchr_format, sprintf($page_link_format, $i), $i);
        }
    }

    if ($paged === 1) {
        $prev = sprintf($span_format, ' class="fa fa-angel-left disabled"', '');
    }
    else {
        $prev = sprintf($anchr_format, sprintf($page_link_format, $paged - 1), '');
    }

    if ($paged == $paginate_query->max_num_pages) {
        $next = sprintf($span_format, ' class="disabled"', '');
    }
    else {
        $next = sprintf($anchr_format, sprintf($page_link_format, $paged + 1), '');
    }
}
else {

    $search_value = get_query_var('s', false);

    $add_args = false;

    if (isset($search_value) && !empty($search_value) && gettype($search_value) === 'string') {
        $add_args['s'] = $search_value;
    }

    $array = paginate_links(array(
        'prev_text' => '',
        'next_text' => '',
        'type' => 'array',
        'add_args' => $add_args,
    ));

    if (isset($array)) {

        if (strpos($array[0], 'prev') !== false) {
            $prev = array_shift($array);
        }
        else {
            $prev = '<span class="disabled"></span>';
        }

        if (strpos($array[count($array) - 1], 'next') !== false) {
            $next = array_pop($array);
        }
        else {
            $next = '<span class="disabled"></span>';
        }
    }
}

if (isset($array)) :
    ?>
    <div class="pagination">
        <div class="pagination-holder">
            <div class="pagination-wrapper">
                <div class="pagination-nav prev"><?php echo $prev; ?></div>
                <div class="pagination-pages">
                    <ul class="pagination-pages-holder">
                        <?php foreach ($array as $item) : ?>
                            <li><?php echo $item; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="pagination-nav next"><?php echo $next; ?></div>
            </div>
        </div>
    </div>
    <?php

 endif;