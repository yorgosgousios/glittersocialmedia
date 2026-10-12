<?php
/**
 * Case study card: large 4:5 image, client, title and the headline result.
 */

$glitter_client  = glitter_meta( get_the_ID(), 'client' );
$glitter_results = glitter_results( get_the_ID() );
?>
<li class="work-card reveal">
	<a class="work-card__link" href="<?php the_permalink(); ?>">
		<span class="work-card__media">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail(
					'glitter-card',
					[
						'loading' => 'lazy',
						'sizes'   => '(min-width: 1100px) 33vw, (min-width: 700px) 50vw, 100vw',
					]
				);
			}
			?>
			<span class="work-card__view" aria-hidden="true"><?php echo glitter_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		</span>
		<span class="work-card__meta">
			<?php if ( $glitter_client ) : ?>
				<span><?php echo esc_html( $glitter_client ); ?></span>
			<?php endif; ?>
			<?php if ( $glitter_results ) : ?>
				<span class="work-card__result"><?php echo esc_html( $glitter_results[0][0] . ' ' . $glitter_results[0][1] ); ?></span>
			<?php endif; ?>
		</span>
		<span class="work-card__title"><?php the_title(); ?></span>
	</a>
</li>
