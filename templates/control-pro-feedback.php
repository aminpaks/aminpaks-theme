<?php
global $control_vars;

$query_default = array(
    'post_type'     => '_feedback',
    'order'         => 'DESC',
    'orderby'       => 'date',
    'posts_per_page' => 3,
);

if (isset($control_vars) && isset($control_vars['query'])) {

    $query_parameters = array_merge($query_default, $control_vars['query']);
    unset($control_vars['query']);
} else {
    $query_parameters = $query_default;
}
unset($query_default);

$query_key = sprintf('_control_feedback_%s', md5(serialize($query_parameters)));

$query = wp_cache_get($query_key);

if ($query === false) {

    $query = new WP_Query($query_parameters);

    wp_cache_set($query_key, $query);
}
?>

<div class="control feedback feedback-control">
    <div class="feedback-holder">

        <?php
        while ($query->have_posts()) :
            $query->the_post();
            $name = get_post_meta(get_the_ID(), '_feedback_name', true);
            $title = get_post_meta(get_the_ID(), '_feedback_title', true);
            $company = get_post_meta(get_the_ID(), '_feedback_company', true);
        ?>

        <div class="feedback-entry">
            <div class="feedback-text"><?php echo wp_strip_all_tags(get_the_content()); ?></div>
            <h3 class="feedback-name"><?php echo $name; ?></h3>
            <h5 class="feedback-title"><?php echo $title . (empty($company) ? '' : ' <span>/</span> ' . $company); ?></h5>
        </div>

        <?php
        endwhile;
        unset($query);
        unset($query_key);
        ?>

    </div>
</div>