<?php
/**
 * Thin ticker of what the agency does. Rendered twice for a seamless CSS loop.
 */

$glitter_items = [
	__( 'Strategy', 'glitter' ),
	__( 'Content creation', 'glitter' ),
	__( 'Reels & TikToks', 'glitter' ),
	__( 'Community', 'glitter' ),
	__( 'Paid ads', 'glitter' ),
	__( 'Influencers', 'glitter' ),
	__( 'Branding', 'glitter' ),
];
?>
<div class="marquee" role="img" aria-label="<?php echo esc_attr( implode( ', ', $glitter_items ) ); ?>">
	<div class="marquee__track">
		<?php for ( $glitter_copy = 0; $glitter_copy < 2; $glitter_copy++ ) : ?>
			<ul class="marquee__list" aria-hidden="true">
				<?php foreach ( $glitter_items as $glitter_item ) : ?>
					<li><?php echo esc_html( $glitter_item ); ?><?php echo glitter_sticker( 'sparkle' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endfor; ?>
	</div>
</div>
