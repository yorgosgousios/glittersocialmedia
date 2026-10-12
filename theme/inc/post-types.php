<?php
/**
 * Content types: services, work (case studies), team members, testimonials.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		register_post_type(
			'service',
			[
				'labels'        => [
					'name'          => __( 'Services', 'glitter' ),
					'singular_name' => __( 'Service', 'glitter' ),
					'add_new_item'  => __( 'Add new service', 'glitter' ),
					'edit_item'     => __( 'Edit service', 'glitter' ),
				],
				'public'        => true,
				'has_archive'   => 'services',
				'rewrite'       => [
					'slug'       => 'services',
					'with_front' => false,
				],
				'menu_icon'     => 'dashicons-star-filled',
				'menu_position' => 5,
				'supports'      => [ 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ],
				'show_in_rest'  => true,
			]
		);

		register_post_type(
			'case_study',
			[
				'labels'        => [
					'name'          => __( 'Work', 'glitter' ),
					'singular_name' => __( 'Project', 'glitter' ),
					'add_new_item'  => __( 'Add new project', 'glitter' ),
					'edit_item'     => __( 'Edit project', 'glitter' ),
					'all_items'     => __( 'All projects', 'glitter' ),
				],
				'public'        => true,
				'has_archive'   => 'work',
				'rewrite'       => [
					'slug'       => 'work',
					'with_front' => false,
				],
				'menu_icon'     => 'dashicons-portfolio',
				'menu_position' => 6,
				'supports'      => [ 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ],
				'show_in_rest'  => true,
			]
		);

		// Team and testimonials only appear inside other pages: no single URLs.
		$internal = [
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'exclude_from_search' => true,
		];

		register_post_type(
			'team_member',
			$internal + [
				'labels'        => [
					'name'           => __( 'Team', 'glitter' ),
					'singular_name'  => __( 'Team member', 'glitter' ),
					'add_new_item'   => __( 'Add team member', 'glitter' ),
					'featured_image' => __( 'Photo', 'glitter' ),
				],
				'menu_icon'     => 'dashicons-groups',
				'menu_position' => 7,
				'supports'      => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
			]
		);

		register_post_type(
			'testimonial',
			$internal + [
				'labels'        => [
					'name'          => __( 'Testimonials', 'glitter' ),
					'singular_name' => __( 'Testimonial', 'glitter' ),
					'add_new_item'  => __( 'Add testimonial', 'glitter' ),
				],
				'menu_icon'     => 'dashicons-format-quote',
				'menu_position' => 8,
				'supports'      => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
			]
		);
	}
);

// Manual order (menu_order) first, newest next.
add_action(
	'pre_get_posts',
	function ( WP_Query $q ) {
		if ( is_admin() || ! $q->is_main_query() ) {
			return;
		}
		if ( $q->is_post_type_archive( [ 'service', 'case_study' ] ) ) {
			$q->set( 'posts_per_page', 24 );
			$q->set(
				'orderby',
				[
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				]
			);
		}
	}
);

/**
 * Posts of a type in display order.
 *
 * @param string $type  Post type.
 * @param int    $limit Max posts (-1 for all).
 */
function glitter_query( string $type, int $limit = -1 ): WP_Query {
	return new WP_Query(
		[
			'post_type'      => $type,
			'posts_per_page' => $limit,
			'orderby'        => [
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			],
			'no_found_rows'  => true,
		]
	);
}
