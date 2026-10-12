<form role="search" method="get" class="search-form" action="<?php echo esc_url( glitter_home_url() ); ?>">
	<label>
		<span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'glitter' ); ?></span>
		<input type="search" class="search-form__input" placeholder="<?php esc_attr_e( 'Search…', 'glitter' ); ?>" value="<?php echo get_search_query(); ?>" name="s">
	</label>
	<button type="submit" class="icon-btn" aria-label="<?php esc_attr_e( 'Search', 'glitter' ); ?>"><?php echo glitter_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button>
</form>
