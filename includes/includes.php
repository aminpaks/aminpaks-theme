<?php
/**
 * INCLUDING ALL SCRIPTS
 */
define('THEMEINCLUDE', TEMPLATEPATH . '/includes/');

require_once THEMEINCLUDE . 'admin/theme_options.php';

require_once THEMEINCLUDE . 'helpers/global.php';
require_once THEMEINCLUDE . 'functions/shortcode.php';

require_once THEMEINCLUDE . 'functions/posttypes/header.php';
require_once THEMEINCLUDE . 'functions/posttypes/team.php';
require_once THEMEINCLUDE . 'functions/posttypes/work.php';
//require_once THEMEINCLUDE . 'functions/posttypes/slide.php';

require_once THEMEINCLUDE . 'admin/page_extra.php';