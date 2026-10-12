<?php
/**
 * Service as a numbered row: number, title, excerpt, arrow. Expects $args['index'].
 */

$glitter_index = (int) ( $args['index'] ?? 0 ) + 1;
?>
<li class="service-row reveal">
	<a class="service-row__link" href="<?php the_permalink(); ?>">
		<span class="service-row__num"><?php echo esc_html( sprintf( '%02d', $glitter_index ) ); ?></span>
		<span class="service-row__title"><?php the_title(); ?></span>
		<?php if ( has_excerpt() ) : ?>
			<span class="service-row__text"><?php echo esc_html( get_the_excerpt() ); ?></span>
		<?php endif; ?>
		<span class="service-row__arrow"><?php echo glitter_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
	</a>
</li>
