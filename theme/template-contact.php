<?php
/**
 * Template Name: Contact (project brief)
 *
 * Page content as intro, then the brief form (see inc/contact-form.php).
 */

get_header();

$glitter_status = sanitize_key( wp_unslash( $_GET['brief'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only status flag.
$glitter_errors = glitter_brief_errors();
$glitter_old    = glitter_brief_old_input();
$glitter_val    = fn( string $k ) => esc_attr( $glitter_old[ $k ] ?? '' );

while ( have_posts() ) :
	the_post();
	glitter_page_header( get_the_title(), __( 'Let’s talk', 'glitter' ), '' );
	?>
	<section class="section section--tight">
		<div class="container container--wide contact">
			<aside class="contact__aside">
				<div class="entry-content"><?php the_content(); ?></div>
				<div class="contact__card">
					<p class="eyebrow"><?php esc_html_e( 'Prefer email?', 'glitter' ); ?></p>
					<?php $glitter_email = glitter_contact_email(); ?>
					<a class="contact__email" href="mailto:<?php echo esc_attr( antispambot( $glitter_email ) ); ?>"><?php echo esc_html( antispambot( $glitter_email ) ); ?></a>
					<?php $glitter_phone = glitter_option( 'glitter_phone' ); ?>
					<?php if ( $glitter_phone ) : ?>
						<a class="contact__phone" href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $glitter_phone ) ); ?>"><?php echo esc_html( $glitter_phone ); ?></a>
					<?php endif; ?>
					<p><?php esc_html_e( 'Or slide into our DMs:', 'glitter' ); ?></p>
					<?php glitter_social_links(); ?>
				</div>
			</aside>

			<div class="contact__form" id="brief">
				<?php if ( 'sent' === $glitter_status ) : ?>
					<div class="notice notice--success" role="status">
						<h2><?php esc_html_e( 'Glitter incoming!', 'glitter' ); ?></h2>
						<p><?php esc_html_e( 'Thanks for your brief. We’ll get back to you within two working days.', 'glitter' ); ?></p>
					</div>
				<?php else : ?>
					<?php if ( isset( $glitter_errors[ $glitter_status ] ) ) : ?>
						<div class="notice notice--error" role="alert"><p><?php echo esc_html( $glitter_errors[ $glitter_status ] ); ?></p></div>
					<?php endif; ?>

					<form class="form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="<?php echo esc_attr( GLITTER_FORM_ACTION ); ?>">
						<input type="hidden" name="lang" value="<?php echo esc_attr( glitter_lang() ); ?>">
						<input type="hidden" name="_started" value="<?php echo esc_attr( time() ); ?>">
						<?php wp_nonce_field( GLITTER_FORM_ACTION, '_glitter_nonce', false ); ?>
						<div class="form__hp" aria-hidden="true">
							<label>Website <input type="text" name="website_url" tabindex="-1" autocomplete="off"></label>
						</div>

						<div class="form__row">
							<label class="field">
								<span><?php esc_html_e( 'Your name', 'glitter' ); ?> *</span>
								<input type="text" name="name" required autocomplete="name" value="<?php echo $glitter_val( 'name' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in closure ?>">
							</label>
							<label class="field">
								<span><?php esc_html_e( 'Email', 'glitter' ); ?> *</span>
								<input type="email" name="email" required autocomplete="email" value="<?php echo $glitter_val( 'email' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in closure ?>">
							</label>
						</div>

						<div class="form__row">
							<label class="field">
								<span><?php esc_html_e( 'Brand / business', 'glitter' ); ?> *</span>
								<input type="text" name="brand" required autocomplete="organization" value="<?php echo $glitter_val( 'brand' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in closure ?>">
							</label>
							<label class="field">
								<span><?php esc_html_e( 'Website or @handle', 'glitter' ); ?></span>
								<input type="text" name="handle" placeholder="@" value="<?php echo $glitter_val( 'handle' ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in closure ?>">
							</label>
						</div>

						<fieldset class="field">
							<legend><?php esc_html_e( 'What do you need?', 'glitter' ); ?></legend>
							<div class="chips">
								<?php foreach ( glitter_brief_services() as $glitter_service ) : ?>
									<label class="chip">
										<input type="checkbox" name="services[]" value="<?php echo esc_attr( $glitter_service ); ?>" <?php checked( in_array( $glitter_service, $glitter_old['services'] ?? [], true ) ); ?>>
										<span><?php echo esc_html( $glitter_service ); ?></span>
									</label>
								<?php endforeach; ?>
							</div>
						</fieldset>

						<fieldset class="field">
							<legend><?php esc_html_e( 'Budget', 'glitter' ); ?> *</legend>
							<div class="chips">
								<?php foreach ( glitter_budgets() as $glitter_key => $glitter_label ) : ?>
									<label class="chip">
										<input type="radio" name="budget" value="<?php echo esc_attr( $glitter_key ); ?>" required <?php checked( $glitter_old['budget'] ?? '', $glitter_key ); ?>>
										<span><?php echo esc_html( $glitter_label ); ?></span>
									</label>
								<?php endforeach; ?>
							</div>
						</fieldset>

						<label class="field">
							<span><?php esc_html_e( 'Tell us about your brand and goals', 'glitter' ); ?> *</span>
							<textarea name="message" rows="6" required><?php echo esc_textarea( $glitter_old['message'] ?? '' ); ?></textarea>
						</label>

						<label class="check">
							<input type="checkbox" name="consent" value="1" required <?php checked( ! empty( $glitter_old['consent'] ) ); ?>>
							<span>
								<?php
								$glitter_privacy = get_privacy_policy_url();
								echo wp_kses(
									$glitter_privacy
										/* translators: %s: privacy policy URL */
										? sprintf( __( 'I agree that my details are used to reply to this message, as described in the <a href="%s">privacy policy</a>.', 'glitter' ), esc_url( $glitter_privacy ) )
										: __( 'I agree that my details are used to reply to this message.', 'glitter' ),
									[ 'a' => [ 'href' => [] ] ]
								);
								?>
								*
							</span>
						</label>

						<button class="btn btn--big" type="submit"><?php esc_html_e( 'Send the brief', 'glitter' ); ?> <?php echo glitter_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
					</form>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
endwhile;

get_footer();
