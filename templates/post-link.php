<?php
$postprev = get_adjacent_post(true, '', true);
$postnext = get_adjacent_post(true, '', false);

if (!empty($postprev)) {
    $post_prev_id = $postprev->ID;
    unset($postprev);
}

if (!empty($postnext)) {
    $post_next_id = $postnext->ID;
    unset($postnext);
}

$prevlink = $nextlink = '';

$format = '';
$format .= '<div class="post-link post-%3$s">';
$format .= '<a href="%2$s"><em class="icon icon-%3$s"></em><span>%1$s</span></a>';
$format .= '</div>';

if (isset($post_prev_id)) {
    $prevlink = sprintf($format, get_the_title($post_prev_id), get_the_permalink($post_prev_id), 'prev');
}
if (isset($post_next_id)) {
    $nextlink = sprintf($format, get_the_title($post_next_id), get_the_permalink($post_next_id), 'next');
}
?>
<div class="content-entry-links">
    <?php echo $prevlink . ' ' . $nextlink; ?>
</div><!-- .content-entry-links -->