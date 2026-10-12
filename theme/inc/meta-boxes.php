<?php
/**
 * Simple "Details" meta boxes for the custom post types.
 * Values are stored as `_glitter_<key>` post meta.
 */

defined( 'ABSPATH' ) || exit;

/** Field definitions per post type: key => [label, type, options/help]. */
function glitter_meta_fields(): array {
	return [
		'service'     => [
			'deliverables' => [ __( 'What’s included (one per line)', 'glitter' ), 'textarea', '' ],
		],
		'case_study'  => [
			'client'    => [ __( 'Client', 'glitter' ), 'text', '' ],
			'platforms' => [ __( 'Platforms (e.g. Instagram, TikTok)', 'glitter' ), 'text', '' ],
			'year'      => [ __( 'Year', 'glitter' ), 'text', '' ],
			'results'   => [ __( 'Results, one per line as "value | label" (e.g. +240% | reach)', 'glitter' ), 'textarea', '' ],
			'link'      => [ __( 'Link (Instagram profile, campaign…)', 'glitter' ), 'url', '' ],
		],
		'team_member' => [
			'role'      => [ __( 'Role', 'glitter' ), 'text', '' ],
			'instagram' => [ __( 'Instagram URL', 'glitter' ), 'url', '' ],
		],
		'testimonial' => [
			'company' => [ __( 'Role / company', 'glitter' ), 'text', '' ],
		],
	];
}

/** Post meta value for a field. */
function glitter_meta( int $post_id, string $key ): string {
	return (string) get_post_meta( $post_id, "_glitter_{$key}", true );
}

add_action(
	'add_meta_boxes',
	function ( string $post_type ) {
		if ( isset( glitter_meta_fields()[ $post_type ] ) ) {
			add_meta_box( 'glitter_details', __( 'Details', 'glitter' ), 'glitter_render_meta_box', $post_type, 'normal', 'high' );
		}
	}
);

/** Renders the Details meta box. */
function glitter_render_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'glitter_meta', '_glitter_meta_nonce' );
	foreach ( glitter_meta_fields()[ $post->post_type ] as $key => [ $label, $type, $options ] ) {
		$id    = "glitter_{$key}";
		$value = glitter_meta( $post->ID, $key );
		echo '<p><label for="' . esc_attr( $id ) . '"><strong>' . esc_html( $label ) . '</strong></label><br>';
		if ( 'textarea' === $type ) {
			echo '<textarea class="widefat" rows="4" id="' . esc_attr( $id ) . '" name="glitter_meta[' . esc_attr( $key ) . ']">' . esc_textarea( $value ) . '</textarea>';
		} elseif ( 'select' === $type ) {
			echo '<select id="' . esc_attr( $id ) . '" name="glitter_meta[' . esc_attr( $key ) . ']">';
			foreach ( $options as $opt => $opt_label ) {
				echo '<option value="' . esc_attr( $opt ) . '"' . selected( $value, $opt, false ) . '>' . esc_html( $opt_label ) . '</option>';
			}
			echo '</select>';
		} else {
			echo '<input class="widefat" type="' . esc_attr( 'url' === $type ? 'url' : 'text' ) . '" id="' . esc_attr( $id ) . '" name="glitter_meta[' . esc_attr( $key ) . ']" value="' . esc_attr( $value ) . '">';
		}
		echo '</p>';
	}
}

add_action(
	'save_post',
	function ( int $post_id, WP_Post $post ) {
		$fields = glitter_meta_fields()[ $post->post_type ] ?? null;
		if ( ! $fields || ! isset( $_POST['_glitter_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['_glitter_meta_nonce'] ), 'glitter_meta' ) ) {
			return;
		}
		if ( wp_is_post_autosave( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$values = (array) wp_unslash( $_POST['glitter_meta'] ?? [] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per field type below.
		foreach ( $fields as $key => [ , $type ] ) {
			$raw   = (string) ( $values[ $key ] ?? '' );
			$clean = match ( $type ) {
				'textarea' => sanitize_textarea_field( $raw ),
				'url'      => esc_url_raw( $raw ),
				default    => sanitize_text_field( $raw ),
			};
			update_post_meta( $post_id, "_glitter_{$key}", $clean );
		}
	},
	10,
	2
);

/**
 * Case-study results as [ [value, label], … ].
 *
 * @param int $post_id Case study ID.
 */
function glitter_results( int $post_id ): array {
	$rows = [];
	foreach ( preg_split( '/\R/', glitter_meta( $post_id, 'results' ) ) as $line ) {
		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		if ( '' !== $parts[0] ) {
			$rows[] = [ $parts[0], $parts[1] ?? '' ];
		}
	}
	return $rows;
}

/**
 * Lines of a textarea field.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Field key.
 */
function glitter_lines( int $post_id, string $key ): array {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\R/', glitter_meta( $post_id, $key ) ) ) ) );
}
