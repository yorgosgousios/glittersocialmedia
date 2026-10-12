<?php
/**
 * Closing call to action: dark full-width band.
 */

?>
<section class="cta">
	<div class="container container--wide cta__inner reveal">
		<p class="eyebrow eyebrow--light"><?php esc_html_e( 'Let’s talk', 'glitter' ); ?></p>
		<h2 class="cta__title"><?php echo glitter_highlight( __( 'Ready to get your *glitter* on?', 'glitter' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in glitter_highlight() ?></h2>
		<div class="cta__row">
			<p><?php esc_html_e( 'Tell us about your brand and we’ll come back with ideas, not a sales pitch.', 'glitter' ); ?></p>
			<a class="btn btn--pink btn--big" href="<?php echo esc_url( glitter_page_url( 'epikoinonia' ) ); ?>"><?php esc_html_e( 'Start a project', 'glitter' ); ?> <?php echo glitter_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		</div>
		<?php echo glitter_sticker( 'disco', 'cta__ball' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</div>
</section>
