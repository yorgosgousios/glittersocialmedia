<?php
/**
 * Testimonials: one large quote at a time with prev/next (main.js). All quotes stay
 * in the markup, so without JS they simply stack.
 */

$glitter_quotes = glitter_query( 'testimonial', 12 );
if ( ! $glitter_quotes->have_posts() ) {
	return;
}
?>
<section class="section testimonials">
	<div class="container container--wide">
		<p class="eyebrow reveal"><?php esc_html_e( 'Kind words', 'glitter' ); ?></p>
		<div class="quotes" data-quotes>
			<ul class="quotes__list">
				<?php
				while ( $glitter_quotes->have_posts() ) :
					$glitter_quotes->the_post();
					$glitter_company = glitter_meta( get_the_ID(), 'company' );
					?>
					<li class="quote">
						<blockquote class="quote__text"><?php echo wp_kses_post( wpautop( get_the_content() ) ); ?></blockquote>
						<p class="quote__author">
							<strong><?php the_title(); ?></strong>
							<?php if ( $glitter_company ) : ?>
								<span><?php echo esc_html( $glitter_company ); ?></span>
							<?php endif; ?>
						</p>
					</li>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</ul>
			<div class="quotes__nav" hidden>
				<button class="icon-btn" type="button" data-quotes-prev aria-label="<?php esc_attr_e( 'Previous', 'glitter' ); ?>"><?php echo glitter_icon( 'prev' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
				<span class="quotes__count" aria-live="polite"></span>
				<button class="icon-btn" type="button" data-quotes-next aria-label="<?php esc_attr_e( 'Next', 'glitter' ); ?>"><?php echo glitter_icon( 'next' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
			</div>
		</div>
	</div>
</section>
