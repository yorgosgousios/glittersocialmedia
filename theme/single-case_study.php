<?php
/**
 * Single case study: hero image, facts, results, story.
 */

get_header();

while ( have_posts() ) :
	the_post();
	$glitter_id      = get_the_ID();
	$glitter_results = glitter_results( $glitter_id );
	$glitter_facts   = array_filter(
		[
			__( 'Client', 'glitter' )    => glitter_meta( $glitter_id, 'client' ),
			__( 'Platforms', 'glitter' ) => glitter_meta( $glitter_id, 'platforms' ),
			__( 'Year', 'glitter' )      => glitter_meta( $glitter_id, 'year' ),
		]
	);
	$glitter_link    = glitter_meta( $glitter_id, 'link' );
	glitter_page_header( get_the_title(), __( 'Case study', 'glitter' ), has_excerpt() ? get_the_excerpt() : '' );
	?>
	<article class="case">
		<?php if ( has_post_thumbnail() ) : ?>
			<div class="container container--wide">
				<figure class="case__hero reveal">
					<?php
					the_post_thumbnail(
						'glitter-wide',
						[
							'loading' => 'eager',
							'sizes'   => '100vw',
						]
					);
					?>
				</figure>
			</div>
		<?php endif; ?>

		<div class="container container--wide case__grid">
			<aside class="case__facts">
				<?php if ( $glitter_facts ) : ?>
					<dl>
						<?php foreach ( $glitter_facts as $glitter_label => $glitter_value ) : ?>
							<div><dt><?php echo esc_html( $glitter_label ); ?></dt><dd><?php echo esc_html( $glitter_value ); ?></dd></div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
				<?php if ( $glitter_link ) : ?>
					<a class="link-arrow" href="<?php echo esc_url( $glitter_link ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'See it live', 'glitter' ); ?></a>
				<?php endif; ?>
			</aside>

			<div class="case__body">
				<?php if ( $glitter_results ) : ?>
					<ul class="results">
						<?php foreach ( $glitter_results as $glitter_i => [ $glitter_value, $glitter_label ] ) : ?>
							<li class="results__item reveal" style="--delay: <?php echo esc_attr( $glitter_i * 90 ); ?>ms">
								<strong><?php echo esc_html( $glitter_value ); ?></strong>
								<span><?php echo esc_html( $glitter_label ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<div class="entry-content"><?php the_content(); ?></div>
			</div>
		</div>

		<nav class="post-nav container container--wide" aria-label="<?php esc_attr_e( 'More projects', 'glitter' ); ?>">
			<?php previous_post_link( '%link', glitter_icon( 'prev' ) . ' %title' ); ?>
			<?php next_post_link( '%link', '%title ' . glitter_icon( 'next' ) ); ?>
		</nav>
	</article>
	<?php
endwhile;

get_template_part( 'template-parts/cta' );

get_footer();
