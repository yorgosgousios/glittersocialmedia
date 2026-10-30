<?php
/**
 * Coming soon mode (Customize → Agency & social): logged-out visitors get a
 * 503 holding page; logged-in users, wp-admin, wp-login and the brief form work as usual.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'template_redirect',
	function () {
		if ( ! glitter_option( 'glitter_coming_soon' ) || is_user_logged_in() ) {
			return;
		}
		// 503 + Retry-After tells search engines "temporarily down", not "gone".
		status_header( 503 );
		header( 'Retry-After: 86400' );
		header( 'X-Robots-Tag: noindex, nofollow' );
		nocache_headers();
		require GLITTER_DIR . '/template-coming-soon.php';
		exit;
	},
	0
);
