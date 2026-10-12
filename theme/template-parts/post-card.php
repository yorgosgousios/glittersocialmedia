<?php
/**
 * Journal post card.
 */

?>
<li class="post-card reveal">
	<a class="post-card__link" href="<?php the_permalink(); ?>">
		<span class="post-card__media">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail(
					'glitter-card',
					[
						'loading' => 'lazy',
						'sizes'   => '(min-width: 1100px) 33vw, (min-width: 700px) 50vw, 100vw',
					]
				);
			} else {
				echo glitter_sticker( 'sparkle', 'post-card__placeholder' ); // phpcs:ignore WordPress.Security.EscapeOutput
			}
			?>
		</span>
		<span class="post-card__meta"><?php echo esc_html( get_the_date() ); ?></span>
		<span class="post-card__title"><?php the_title(); ?></span>
		<span class="post-card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></span>
	</a>
</li>
