<?php
/**
 * Deletes all placeholder content (meta _glitter_placeholder=1) and its images, in every language.
 * Run via `make unseed` — e.g. before launch, or before re-seeding.
 */

$ids = get_posts(
	[
		'post_type'      => [ 'post', 'service', 'case_study', 'team_member', 'testimonial' ],
		'post_status'    => 'any',
		'meta_key'       => '_glitter_placeholder',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'lang'           => '',
	]
);
foreach ( $ids as $id ) {
	foreach ( get_attached_media( 'image', $id ) as $attachment ) {
		wp_delete_attachment( $attachment->ID, true );
	}
	wp_delete_post( $id, true );
}
WP_CLI::success( count( $ids ) . ' placeholder posts deleted.' );
