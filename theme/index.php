<?php
/**
 * Journal (posts page), archives and search results.
 */

get_header();

if ( is_search() ) {
	/* translators: %s: search query */
	glitter_page_header( sprintf( __( 'Results for “%s”', 'glitter' ), get_search_query() ), __( 'Search', 'glitter' ) );
} elseif ( is_home() ) {
	glitter_page_header(
		single_post_title( '', false ) ?: __( 'Journal', 'glitter' ),
		__( 'Journal', 'glitter' ),
		__( 'Tips, trends and behind-the-scenes from the Glitter team.', 'glitter' )
	);
} else {
	glitter_page_header( wp_strip_all_tags( get_the_archive_title() ), __( 'Journal', 'glitter' ) );
}
?>

<section class="section section--tight">
	<div class="container container--wide">
		<?php if ( have_posts() ) : ?>
			<ul class="post-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/post-card' );
				endwhile;
				?>
			</ul>
			<?php
			the_posts_pagination(
				[
					'prev_text' => glitter_icon( 'prev' ) . '<span class="screen-reader-text">' . __( 'Previous', 'glitter' ) . '</span>',
					'next_text' => '<span class="screen-reader-text">' . __( 'Next', 'glitter' ) . '</span>' . glitter_icon( 'next' ),
				]
			);
			?>
		<?php else : ?>
			<p class="empty"><?php esc_html_e( 'Nothing here yet. Check back soon!', 'glitter' ); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
