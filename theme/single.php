<?php
/**
 * Journal post.
 */

get_header();

while ( have_posts() ) :
	the_post();
	glitter_page_header( get_the_title(), get_the_date(), has_excerpt() ? get_the_excerpt() : '' );
	?>
	<article class="section section--tight">
		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="container post-hero">
				<?php the_post_thumbnail( 'glitter-wide', [ 'loading' => 'eager' ] ); ?>
			</figure>
		<?php endif; ?>
		<div class="container container--narrow entry-content">
			<?php the_content(); ?>
		</div>
		<nav class="post-nav container container--narrow" aria-label="<?php esc_attr_e( 'More posts', 'glitter' ); ?>">
			<?php previous_post_link( '%link', glitter_icon( 'prev' ) . ' %title' ); ?>
			<?php next_post_link( '%link', '%title ' . glitter_icon( 'next' ) ); ?>
		</nav>
	</article>
	<?php
endwhile;

get_template_part( 'template-parts/cta' );

get_footer();
