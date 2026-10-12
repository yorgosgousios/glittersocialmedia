<?php
/**
 * Default page.
 */

get_header();

while ( have_posts() ) :
	the_post();
	glitter_page_header( get_the_title(), '', has_excerpt() ? get_the_excerpt() : '' );
	?>
	<section class="section section--tight">
		<div class="container container--narrow entry-content">
			<?php the_content(); ?>
		</div>
	</section>
	<?php
endwhile;

get_footer();
