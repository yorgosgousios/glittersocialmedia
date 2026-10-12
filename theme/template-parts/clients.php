<?php
/**
 * Client strip: names from Customize → Agency & social → Clients (one per line),
 * shown as quiet wordmarks until real logos are available.
 */

$glitter_clients = array_values( array_filter( array_map( 'trim', preg_split( '/\R/', glitter_option( 'glitter_clients' ) ) ) ) );
if ( count( $glitter_clients ) < 3 ) {
	return;
}
?>
<section class="clients">
	<div class="container container--wide">
		<p class="clients__label"><?php esc_html_e( 'Brands we’ve worked with', 'glitter' ); ?></p>
		<ul class="clients__list">
			<?php foreach ( $glitter_clients as $glitter_client ) : ?>
				<li><?php echo esc_html( $glitter_client ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
