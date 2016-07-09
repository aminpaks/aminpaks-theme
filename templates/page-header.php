<?php

$background_image = '';
$background_image_element = '';
if (has_the_featured_image()) {
    $background_image = sprintf(' style="background-image: url(%s)"', get_the_featured_image(''));
    $background_image_element = sprintf('<img src="%s" class="invisible" />', get_the_featured_image(''));
}

$page_header = get_post_meta(get_the_ID(), '_theme_post_extra_page_header', true);

if ($page_header) {
    $header_post = get_post($page_header);
    
    if (has_the_featured_image($header_post->ID)) {
        $background_image = sprintf(' style="background-image: url(%s)"', get_the_featured_image('', $header_post->ID));
        $background_image_element = sprintf('<img src="%s" class="invisible" />', get_the_featured_image('', $header_post->ID));
    }
}

?>
<div class="header"<?php echo $background_image; ?>>
    <div class="header-holder">
        <div class="header-wrapper">
            <?php if (isset($header_post)) { the_content_ex($header_post->ID); } ?>
        </div><!-- .header-wrapper -->
    </div><!-- .header-holder --><?php echo $background_image_element; ?>
</div><!-- .header -->
