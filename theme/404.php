<?php
/**
 * Not found.
 */

get_header();
glitter_page_header( __( 'This page lost its sparkle', 'glitter' ), '404', __( 'It doesn’t exist or it has moved.', 'glitter' ) );
?>
<section class="section section--tight">
	<div class="container container--narrow">
		<a class="btn btn--big" href="<?php echo esc_url( glitter_home_url() ); ?>"><?php esc_html_e( 'Back home', 'glitter' ); ?></a>
	</div>
</section>
<?php
get_footer();
