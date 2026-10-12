<?php
/**
 * Front-end styles and scripts.
 */

defined( 'ABSPATH' ) || exit;

/** File mtime in local dev for cache busting, theme version elsewhere. */
function glitter_asset_version( string $path ): string {
	if ( wp_get_environment_type() === 'local' && file_exists( GLITTER_DIR . $path ) ) {
		return (string) filemtime( GLITTER_DIR . $path );
	}
	return GLITTER_VERSION;
}

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'glitter', GLITTER_URI . '/assets/css/main.css', [], glitter_asset_version( '/assets/css/main.css' ) );

		wp_enqueue_script(
			'lenis',
			GLITTER_URI . '/assets/js/vendor/lenis.min.js',
			[],
			'1.1.20',
			[
				'strategy'  => 'defer',
				'in_footer' => true,
			]
		);
		wp_enqueue_script(
			'glitter',
			GLITTER_URI . '/assets/js/main.js',
			[ 'lenis' ],
			glitter_asset_version( '/assets/js/main.js' ),
			[
				'strategy'  => 'defer',
				'in_footer' => true,
			]
		);

		if ( ! is_singular() && ! is_front_page() ) {
			wp_dequeue_style( 'wp-block-library' );
			wp_dequeue_style( 'global-styles' );
		}
		wp_dequeue_style( 'classic-theme-styles' );
	}
);

add_action(
	'wp_head',
	function () {
		$fonts = [ 'fraunces-latin', 'manrope-latin' ];
		if ( 'el' === glitter_lang() ) {
			$fonts = [ 'fraunces-latin', 'piazzolla-greek', 'manrope-greek' ];
		}
		foreach ( $fonts as $font ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( GLITTER_URI . "/assets/fonts/{$font}.woff2" )
			);
		}
	},
	1
);
