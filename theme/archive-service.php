<?php
/**
 * Services overview.
 */

get_header();
glitter_page_header(
	__( 'Services', 'glitter' ),
	__( 'What we do', 'glitter' ),
	__( 'Strategy, content and community for brands that want to stand out in the feed.', 'glitter' )
);
?>

<section class="section section--tight">
	<div class="container container--wide">
		<?php if ( have_posts() ) : ?>
			<ul class="service-list">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/service-row', null, [ 'index' => $wp_query->current_post ] );
				endwhile;
				?>
			</ul>
		<?php else : ?>
			<p class="empty"><?php esc_html_e( 'Services coming soon.', 'glitter' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php get_template_part( 'template-parts/cta' ); ?>

<?php
get_footer();
