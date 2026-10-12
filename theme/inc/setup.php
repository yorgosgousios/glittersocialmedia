<?php
/**
 * Theme supports, menus, image sizes and head cleanup.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'glitter', GLITTER_DIR . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ] );
		add_theme_support(
			'custom-logo',
			[
				'height'      => 120,
				'width'       => 360,
				'flex-height' => true,
				'flex-width'  => true,
			]
		);

		add_editor_style( 'assets/css/editor.css' );

		register_nav_menus(
			[
				'primary' => __( 'Primary', 'glitter' ),
				'footer'  => __( 'Footer', 'glitter' ),
			]
		);

		add_image_size( 'glitter-card', 900, 1125, true );   // 4:5, like an IG post
		add_image_size( 'glitter-square', 720, 720, true );
		add_image_size( 'glitter-wide', 1600, 1000, true );

		add_post_type_support( 'page', 'excerpt' );
	}
);

// Keep <head> lean.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );

add_filter( 'excerpt_length', fn() => 22 );
add_filter( 'excerpt_more', fn() => '…' );

/**
 * Menu highlighting: WordPress marks the blog page as "current parent" on every
 * single post of any type. Keep that for journal posts only, and highlight the
 * Services / Work archive item on their single pages instead.
 */
add_filter(
	'nav_menu_css_class',
	function ( array $classes, $item ): array {
		$type = is_singular() ? get_post_type() : '';
		if ( $type && 'post' !== $type && 'page' === $item->object ) { // only the blog page (in any language) gets current_page_parent
			$classes = array_diff( $classes, [ 'current_page_parent' ] );
		}
		if ( 'post_type_archive' === $item->type && $type === $item->object ) {
			$classes[] = 'current-menu-ancestor';
		}
		return $classes;
	},
	10,
	2
);
