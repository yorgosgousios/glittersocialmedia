<?php
/**
 * Project brief form: rendered by page-contact.php, posted to admin-post.php,
 * emailed to glitter_contact_email(). Nothing is stored in the database.
 */

defined( 'ABSPATH' ) || exit;

const GLITTER_FORM_ACTION      = 'glitter_brief';
const GLITTER_RATE_LIMIT       = 5; // briefs per IP per hour
const GLITTER_MIN_FILL_SECONDS = 3;

/** Budget options: key => label. */
function glitter_budgets(): array {
	return [
		'lt500'   => __( 'Up to €500 / month', 'glitter' ),
		'500'     => __( '€500 – €1,000 / month', 'glitter' ),
		'1000'    => __( '€1,000 – €2,500 / month', 'glitter' ),
		'2500'    => __( '€2,500+ / month', 'glitter' ),
		'project' => __( 'One-off project', 'glitter' ),
		'unsure'  => __( 'Not sure yet', 'glitter' ),
	];
}

/** Service choices for the form: titles of published services in the current language. */
function glitter_brief_services(): array {
	$titles   = wp_list_pluck( glitter_query( 'service' )->posts, 'post_title' );
	$titles[] = __( 'Not sure yet — help me choose', 'glitter' );
	return $titles;
}

/** Error messages by code (the `brief` query arg after a failed submit). */
function glitter_brief_errors(): array {
	return [
		'required' => __( 'Please fill in all required fields.', 'glitter' ),
		'email'    => __( 'Please enter a valid email address.', 'glitter' ),
		'limit'    => __( 'Too many messages from your connection. Please try again later or email us directly.', 'glitter' ),
		'send'     => __( 'Your message could not be sent. Please try again or email us directly.', 'glitter' ),
		'expired'  => __( 'The form expired. Please try again.', 'glitter' ),
	];
}

add_action( 'admin_post_nopriv_' . GLITTER_FORM_ACTION, 'glitter_handle_brief' );
add_action( 'admin_post_' . GLITTER_FORM_ACTION, 'glitter_handle_brief' );

/** Validates the brief, emails it and redirects back with a status. */
function glitter_handle_brief(): void {
	$back = wp_get_referer() ?: glitter_home_url();
	$back = remove_query_arg( [ 'brief', 'bf' ], $back );

	$fail = function ( string $code, array $input = [] ) use ( $back ) {
		$token = wp_generate_password( 12, false );
		set_transient( 'glitter_bf_' . $token, $input, 10 * MINUTE_IN_SECONDS );
		wp_safe_redirect(
			add_query_arg(
				[
					'brief' => $code,
					'bf'    => $token,
				],
				$back
			) . '#brief'
		);
		exit;
	};

	if ( ! isset( $_POST['_glitter_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['_glitter_nonce'] ), GLITTER_FORM_ACTION ) ) {
		$fail( 'expired' );
	}

	// Bots: honeypot filled or submitted implausibly fast. Pretend success.
	$started = absint( wp_unslash( $_POST['_started'] ?? 0 ) );
	if ( ! empty( $_POST['website_url'] ) || time() - $started < GLITTER_MIN_FILL_SECONDS ) {
		wp_safe_redirect( add_query_arg( 'brief', 'sent', $back ) . '#brief' );
		exit;
	}

	$budgets  = glitter_budgets();
	$services = array_intersect(
		array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['services'] ?? [] ) ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized by array_map.
		glitter_brief_services()
	);
	$input    = [
		'name'     => sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) ),
		'email'    => sanitize_email( wp_unslash( $_POST['email'] ?? '' ) ),
		'brand'    => sanitize_text_field( wp_unslash( $_POST['brand'] ?? '' ) ),
		'handle'   => sanitize_text_field( wp_unslash( $_POST['handle'] ?? '' ) ),
		'services' => array_values( $services ),
		'budget'   => sanitize_key( wp_unslash( $_POST['budget'] ?? '' ) ),
		'message'  => sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) ),
		'consent'  => ! empty( $_POST['consent'] ),
	];

	if ( ! is_email( $input['email'] ) && trim( sanitize_text_field( wp_unslash( $_POST['email'] ?? '' ) ) ) !== '' ) {
		$fail( 'email', $input );
	}
	if ( ! $input['name'] || ! $input['email'] || ! $input['brand'] || ! $input['message'] || ! $input['consent'] || ! isset( $budgets[ $input['budget'] ] ) ) {
		$fail( 'required', $input );
	}

	$ip_key = 'glitter_rl_' . md5( sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) ) );
	$count  = (int) get_transient( $ip_key );
	if ( $count >= GLITTER_RATE_LIMIT ) {
		$fail( 'limit', $input );
	}

	$body = implode(
		"\n",
		[
			sprintf( 'Name: %s', $input['name'] ),
			sprintf( 'Email: %s', $input['email'] ),
			sprintf( 'Brand: %s', $input['brand'] ),
			sprintf( 'Website / handle: %s', $input['handle'] ?: '—' ),
			sprintf( 'Services: %s', $input['services'] ? implode( ', ', $input['services'] ) : '—' ),
			sprintf( 'Budget: %s', $budgets[ $input['budget'] ] ),
			sprintf( 'Language: %s', 'en' === sanitize_key( wp_unslash( $_POST['lang'] ?? '' ) ) ? 'EN' : 'EL' ),
			'',
			'Message:',
			$input['message'],
		]
	);

	$sent = wp_mail(
		glitter_contact_email(),
		sprintf( '[%s] New project brief: %s', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $input['brand'] ),
		$body,
		[ sprintf( 'Reply-To: %s <%s>', $input['name'], $input['email'] ) ]
	);

	if ( ! $sent ) {
		$fail( 'send', $input );
	}

	set_transient( $ip_key, $count + 1, HOUR_IN_SECONDS );
	wp_safe_redirect( add_query_arg( 'brief', 'sent', $back ) . '#brief' );
	exit;
}

/** Previously submitted values after a failed submit (empty array otherwise). */
function glitter_brief_old_input(): array {
	$token = sanitize_key( wp_unslash( $_GET['bf'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only lookup of a short-lived token.
	if ( ! $token ) {
		return [];
	}
	$input = get_transient( 'glitter_bf_' . $token );
	delete_transient( 'glitter_bf_' . $token );
	return is_array( $input ) ? $input : [];
}
