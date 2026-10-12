<?php
/**
 * Template Name: About (team)
 *
 * Page content as the story, then the team grid and testimonials.
 */

get_header();

while ( have_posts() ) :
	the_post();
	glitter_page_header( get_the_title(), __( 'About us', 'glitter' ), has_excerpt() ? get_the_excerpt() : '' );
	?>
	<section class="section section--tight">
		<div class="container container--narrow entry-content reveal">
			<?php the_content(); ?>
		</div>
	</section>
	<?php
endwhile;

$glitter_team = glitter_query( 'team_member' );
if ( $glitter_team->have_posts() ) :
	?>
	<section class="section team">
		<div class="container container--wide">
			<div class="section__head reveal">
				<p class="eyebrow"><?php esc_html_e( 'The team', 'glitter' ); ?></p>
				<h2 class="section__title"><?php echo glitter_highlight( __( 'The people behind the *sparkle*', 'glitter' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in glitter_highlight() ?></h2>
			</div>
			<ul class="team-grid">
				<?php
				while ( $glitter_team->have_posts() ) :
					$glitter_team->the_post();
					$glitter_role = glitter_meta( get_the_ID(), 'role' );
					$glitter_ig   = glitter_meta( get_the_ID(), 'instagram' );
					?>
					<li class="team-card reveal">
						<div class="team-card__photo">
							<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'glitter-card', [ 'loading' => 'lazy' ] );
							}
							?>
						</div>
						<h3 class="team-card__name"><?php the_title(); ?></h3>
						<?php if ( $glitter_role ) : ?>
							<p class="team-card__role"><?php echo esc_html( $glitter_role ); ?></p>
						<?php endif; ?>
						<div class="team-card__bio"><?php the_content(); ?></div>
						<?php if ( $glitter_ig ) : ?>
							<a class="team-card__ig" href="<?php echo esc_url( $glitter_ig ); ?>" target="_blank" rel="noopener"><?php echo glitter_icon( 'instagram' ); // phpcs:ignore WordPress.Security.EscapeOutput ?> <span><?php esc_html_e( 'Instagram', 'glitter' ); ?></span></a>
						<?php endif; ?>
					</li>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</ul>
		</div>
	</section>
<?php endif; ?>

<?php get_template_part( 'template-parts/testimonials' ); ?>
<?php get_template_part( 'template-parts/cta' ); ?>

<?php
get_footer();
