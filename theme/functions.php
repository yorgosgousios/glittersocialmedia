<?php
/**
 * Glitter theme bootstrap.
 */

defined( 'ABSPATH' ) || exit;

define( 'GLITTER_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'GLITTER_DIR', get_template_directory() );
define( 'GLITTER_URI', get_template_directory_uri() );

require GLITTER_DIR . '/inc/setup.php';
require GLITTER_DIR . '/inc/i18n.php';
require GLITTER_DIR . '/inc/assets.php';
require GLITTER_DIR . '/inc/post-types.php';
require GLITTER_DIR . '/inc/meta-boxes.php';
require GLITTER_DIR . '/inc/customizer.php';
require GLITTER_DIR . '/inc/contact-form.php';
require GLITTER_DIR . '/inc/template-tags.php';
