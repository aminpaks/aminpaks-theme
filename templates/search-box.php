<div class="search-box">
    <div class="search-box-holder">
        <form method="get" action="/">
            <div class="search-box-input"><input class="search-box-input-element" name="s" type="text" placeholder="<?php _e('Search', 'theme'); ?>" value="<?php echo get_search_query(); ?>"/>
                <button class="search-box-submit icon icon-search" type="submit"></button></div>
            <input type="hidden" name="post_type" value="post" />
        </form>
    </div>
</div>