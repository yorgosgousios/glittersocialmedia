<?php
/**
 * Customizer: agency contact details and social links.
 */

defined( 'ABSPATH' ) || exit;

/** Fields under Appearance → Customize → Agency & social: id => [label, type, default]. */
function glitter_contact_fields(): array {
	return [
		'glitter_email'     => [ __( 'Contact email (also receives project briefs)', 'glitter' ), 'email', 'hey@glittersocialmedia.gr' ],
		'glitter_phone'     => [ __( 'Phone', 'glitter' ), 'text', '' ],
		'glitter_address'   => [ __( 'Address / city', 'glitter' ), 'textarea', '' ],
		'glitter_instagram' => [ __( 'Instagram URL', 'glitter' ), 'url', 'https://www.instagram.com/glittersocialmedia/' ],
		'glitter_tiktok'    => [ __( 'TikTok URL', 'glitter' ), 'url', '' ],
		'glitter_linkedin'  => [ __( 'LinkedIn URL', 'glitter' ), 'url', '' ],
		'glitter_facebook'  => [ __( 'Facebook URL', 'glitter' ), 'url', '' ],
		'glitter_clients'   => [ __( 'Clients strip on Home (one name per line)', 'glitter' ), 'textarea', "Client one\nClient two\nClient three\nClient four\nClient five\nClient six" ],
	];
}

/** A customizer value with its default. */
function glitter_option( string $id ): string {
	$fields = glitter_contact_fields();
	return (string) get_theme_mod( $id, $fields[ $id ][2] ?? '' );
}

add_action(
	'customize_register',
	function ( WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_section(
			'glitter_agency',
			[
				'title'    => __( 'Agency & social', 'glitter' ),
				'priority' => 30,
			]
		);

		$sanitizers = [
			'email'    => 'sanitize_email',
			'url'      => 'esc_url_raw',
			'text'     => 'sanitize_text_field',
			'textarea' => 'sanitize_textarea_field',
		];

		foreach ( glitter_contact_fields() as $id => [ $label, $type, $default ] ) {
			$wp_customize->add_setting(
				$id,
				[
					'default'           => $default,
					'sanitize_callback' => $sanitizers[ $type ],
				]
			);
			$wp_customize->add_control(
				$id,
				[
					'label'   => $label,
					'section' => 'glitter_agency',
					'type'    => $type,
				]
			);
		}
	}
);

/** Where project briefs go: customizer email, falling back to the admin email. */
function glitter_contact_email(): string {
	return glitter_option( 'glitter_email' ) ?: (string) get_option( 'admin_email' );
}
