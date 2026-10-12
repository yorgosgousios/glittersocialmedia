<?php
/**
 * Work: all case studies.
 */

get_header();
glitter_page_header(
	__( 'Work', 'glitter' ),
	__( 'Selected projects', 'glitter' ),
	__( 'A few of the brands we’ve helped sparkle online.', 'glitter' )
);
?>

<section class="section section--tight">
	<div class="container container--wide">
		<?php if ( have_posts() ) : ?>
			<ul class="work-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/work-card' );
				endwhile;
				?>
			</ul>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p class="empty"><?php esc_html_e( 'Case studies coming soon.', 'glitter' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php get_template_part( 'template-parts/testimonials' ); ?>
<?php get_template_part( 'template-parts/cta' ); ?>

<?php
get_footer();
