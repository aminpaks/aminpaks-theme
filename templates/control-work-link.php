<?php
$postprev = get_adjacent_post_ex('prev', '_work');
$postnext = get_adjacent_post_ex('next', '_work');

$prevlink = $nextlink = '';

if (!empty($postprev)) {
    $prevlink = do_shortcode('[button title="' . get_the_title($postprev->ID) . '" link="' . get_the_permalink($postprev->ID) . '" class="prev-work" exclass="icon icon-prev"]');
}
if (!empty($postnext)) {
    $nextlink = do_shortcode('[button title="' . get_the_title($postnext->ID) . '" link="' . get_the_permalink($postnext->ID) . '" class="next-work" exclass="icon icon-next"]');
}
?>
<div class="work-entry-links"><?php echo $prevlink . $nextlink; ?></div><!-- .work-entry-links -->