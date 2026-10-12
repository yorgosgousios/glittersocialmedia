<?php
/**
 * Appearance → Glitter setup: runs glitter_site_setup() from the admin, for hosts
 * without WP-CLI. Safe to run more than once (existing pages/menus are reused).
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'admin_menu',
	function () {
		add_theme_page( __( 'Glitter setup', 'glitter' ), __( 'Glitter setup', 'glitter' ), 'manage_options', 'glitter-setup', 'glitter_render_setup_page' );
	}
);

add_action(
	'admin_post_glitter_site_setup',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to do that.', 'glitter' ) );
		}
		check_admin_referer( 'glitter_site_setup' );

		$messages = [];
		$ok       = true;
		try {
			glitter_site_setup(
				function ( string $message ) use ( &$messages ) {
					$messages[] = $message;
				}
			);
		} catch ( RuntimeException $e ) {
			$ok         = false;
			$messages[] = $e->getMessage();
		}
		set_transient( 'glitter_setup_result', [ $ok, $messages ], 5 * MINUTE_IN_SECONDS );
		wp_safe_redirect( admin_url( 'themes.php?page=glitter-setup' ) );
		exit;
	}
);

/** Renders the setup page: checklist + run button + last result. */
function glitter_render_setup_page(): void {
	$result = get_transient( 'glitter_setup_result' );
	delete_transient( 'glitter_setup_result' );

	$checks = [
		__( 'Polylang active', 'glitter' )               => function_exists( 'PLL' ),
		__( 'Languages: Ελληνικά + English', 'glitter' ) => function_exists( 'pll_languages_list' ) && ! array_diff( [ 'el', 'en' ], pll_languages_list() ),
		__( 'Static front page', 'glitter' )             => 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ),
		__( 'Pretty permalinks', 'glitter' )             => '/%postname%/' === get_option( 'permalink_structure' ),
	];
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Glitter setup', 'glitter' ); ?></h1>
		<p><?php esc_html_e( 'Creates the Greek and English languages, the bilingual pages (Home, About, Contact, Journal, Privacy), one menu per language and the permalink settings. Existing pages and menus are kept, so it is safe to run again.', 'glitter' ); ?></p>

		<?php if ( is_array( $result ) ) : ?>
			<div class="notice <?php echo $result[0] ? 'notice-success' : 'notice-error'; ?>">
				<?php foreach ( $result[1] as $message ) : ?>
					<p><?php echo esc_html( $message ); ?></p>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<table class="widefat striped" style="max-width:520px">
			<tbody>
				<?php foreach ( $checks as $label => $done ) : ?>
					<tr>
						<td><?php echo esc_html( $label ); ?></td>
						<td><?php echo $done ? '✅' : '—'; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php if ( ! function_exists( 'PLL' ) ) : ?>
			<p><strong><?php esc_html_e( 'Install and activate the Polylang plugin first.', 'glitter' ); ?></strong></p>
		<?php else : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:1.5rem">
				<input type="hidden" name="action" value="glitter_site_setup">
				<?php wp_nonce_field( 'glitter_site_setup' ); ?>
				<?php submit_button( __( 'Run setup', 'glitter' ), 'primary', 'submit', false ); ?>
			</form>
		<?php endif; ?>
	</div>
	<?php
}
