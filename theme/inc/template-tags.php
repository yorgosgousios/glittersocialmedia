<?php
/**
 * Rendering helpers: icons, stickers, logo, social links, headers.
 */

defined( 'ABSPATH' ) || exit;

/** Inline UI icon from a fixed set. */
function glitter_icon( string $name ): string {
	$icons = [
		'search'      => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
		'close'       => '<path d="M6 6l12 12M18 6 6 18"/>',
		'menu'        => '<path d="M3 7h18M3 12h18M3 17h18"/>',
		'arrow-right' => '<path d="M5 12h14M12 5l7 7-7 7"/>',
		'arrow-up'    => '<path d="M12 19V5M5 12l7-7 7 7"/>',
		'prev'        => '<path d="M15 18l-6-6 6-6"/>',
		'next'        => '<path d="M9 18l6-6-6-6"/>',
		'check'       => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
		'instagram'   => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".6" fill="currentColor"/>',
		'tiktok'      => '<path d="M14 3v11.5a3.5 3.5 0 1 1-3.5-3.5M14 3c.5 2.5 2.5 4.5 5 5"/>',
		'linkedin'    => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 10v7M8 7v.01M12 17v-4a2 2 0 0 1 4 0v4M12 10v7"/>',
		'facebook'    => '<path d="M14 8h3V4h-3a4 4 0 0 0-4 4v3H7v4h3v6h4v-6h3l1-4h-4V8z"/>',
	];
	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}
	return '<svg class="icon icon-' . esc_attr( $name ) . '" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $icons[ $name ] . '</svg>';
}

/**
 * Decorative brand mark: 'sparkle' (✦) or 'disco' (disco ball). Always aria-hidden.
 *
 * @param string $name        Mark name.
 * @param string $extra_class Extra classes.
 */
function glitter_sticker( string $name, string $extra_class = '' ): string {
	$open = '<svg class="mark mark--' . esc_attr( $name ) . ( $extra_class ? ' ' . esc_attr( $extra_class ) : '' ) . '" viewBox="0 0 100 100" aria-hidden="true" focusable="false">';
	switch ( $name ) {
		case 'sparkle':
			$body = '<path fill="currentColor" d="M50 2C54 34 66 46 98 50 66 54 54 66 50 98 46 66 34 54 2 50 34 46 46 34 50 2Z"/>';
			break;
		case 'disco':
			$body = glitter_disco_ball_svg();
			break;
		default:
			return '';
	}
	return $open . $body . '</svg>';
}

/** Disco ball: a tiled band that scrolls inside a round clip, so the ball "spins" (CSS). */
function glitter_disco_ball_svg(): string {
	static $tiles = null, $n = 0;
	$uid          = 'disco-' . ( ++$n ); // unique ids: a clipPath inside a hidden copy would break the others
	if ( null !== $tiles ) {
		return glitter_disco_ball_markup( $uid, $tiles );
	}
	$colors = [ '#ffffff', '#f3eefa', '#e4daf2', '#d3c4ea', '#c2afe0', '#efe7f8', '#f6d3e4', '#b9a5da' ];
	$tiles  = '';
	$size   = 9;
	// Deterministic "random" colours so the ball looks the same on every request.
	for ( $row = 0; $row < 12; $row++ ) {
		for ( $col = 0; $col < 24; $col++ ) {
			$cc     = $col % 12; // repeats every 12 columns so the CSS scroll loops seamlessly
			$c      = $colors[ ( $row * 7 + $cc * 13 + ( $row * $cc ) % 5 ) % count( $colors ) ];
			$tiles .= sprintf( '<rect x="%d" y="%d" width="%d" height="%d" fill="%s"/>', $col * $size, $row * $size - 4, $size - 1.4, $size - 1.4, $c );
		}
	}
	return glitter_disco_ball_markup( $uid, $tiles );
}

/**
 * Disco ball SVG body.
 *
 * @param string $uid   Unique id prefix.
 * @param string $tiles Tile <rect>s.
 */
function glitter_disco_ball_markup( string $uid, string $tiles ): string {
	return '<defs>'
		. '<clipPath id="' . $uid . '-clip"><circle cx="50" cy="50" r="46"/></clipPath>'
		. '<radialGradient id="' . $uid . '-shade" cx="38%" cy="32%" r="72%"><stop offset="0" stop-color="#fff" stop-opacity=".55"/><stop offset=".35" stop-color="#fff" stop-opacity="0"/><stop offset=".8" stop-color="#3a1850" stop-opacity=".28"/><stop offset="1" stop-color="#2b0f3a" stop-opacity=".55"/></radialGradient>'
		. '<radialGradient id="' . $uid . '-rim" cx="50%" cy="50%" r="50%"><stop offset=".86" stop-color="#e5186a" stop-opacity="0"/><stop offset="1" stop-color="#e5186a" stop-opacity=".35"/></radialGradient>'
		. '</defs>'
		. '<g clip-path="url(#' . $uid . '-clip)"><rect width="100" height="100" fill="#e4daf2"/><g class="disco-tiles">' . $tiles . '</g></g>'
		. '<circle cx="50" cy="50" r="46" fill="url(#' . $uid . '-shade)"/>'
		. '<circle cx="50" cy="50" r="46" fill="url(#' . $uid . '-rim)"/>'
		. '<path class="glint" fill="#fff" d="M34 22c.9 4.6 2.4 6.1 7 7-4.6.9-6.1 2.4-7 7-.9-4.6-2.4-6.1-7-7 4.6-.9 6.1-2.4 7-7z"/>';
}

/** Contents of a theme SVG (assets/img/<name>.svg), or '' if missing. */
function glitter_svg( string $name ): string {
	$path = GLITTER_DIR . "/assets/img/{$name}.svg";
	return is_readable( $path ) ? (string) file_get_contents( $path ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file
}

/**
 * Brand mark, in order of preference: Customizer logo, assets/img/logo.svg,
 * placeholder wordmark (site name in the display font + disco ball).
 */
function glitter_logo_mark(): string {
	if ( has_custom_logo() ) {
		return wp_get_attachment_image(
			(int) get_theme_mod( 'custom_logo' ),
			'full',
			false,
			[
				'class' => 'logo-img',
				'alt'   => get_bloginfo( 'name' ),
			]
		);
	}
	$svg = glitter_svg( 'logo' );
	if ( $svg ) {
		return '<span class="logo-svg" role="img" aria-label="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . $svg . '</span>';
	}
	return '<span class="logo-placeholder">' . glitter_sticker( 'disco', 'logo-placeholder__ball' ) . '<span>glitter</span></span>';
}

/** Linked brand mark for header/footer. */
function glitter_logo(): void {
	printf(
		'<a class="brand" href="%s" rel="home" aria-label="%s">%s</a>',
		esc_url( glitter_home_url() ),
		esc_attr( get_bloginfo( 'name' ) ),
		glitter_logo_mark() // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts / theme files
	);
}

/** Social links from the Customizer. */
function glitter_social_links( string $extra_class = '' ): void {
	$links = array_filter(
		[
			'instagram' => [ glitter_option( 'glitter_instagram' ), 'Instagram' ],
			'tiktok'    => [ glitter_option( 'glitter_tiktok' ), 'TikTok' ],
			'linkedin'  => [ glitter_option( 'glitter_linkedin' ), 'LinkedIn' ],
			'facebook'  => [ glitter_option( 'glitter_facebook' ), 'Facebook' ],
		],
		fn( $l ) => ! empty( $l[0] )
	);
	if ( ! $links ) {
		return;
	}
	echo '<ul class="social ' . esc_attr( $extra_class ) . '">';
	foreach ( $links as $icon => [ $url, $label ] ) {
		printf(
			'<li><a href="%s" target="_blank" rel="noopener" aria-label="%s">%s</a></li>',
			esc_url( $url ),
			esc_attr( $label ),
			glitter_icon( $icon ) // phpcs:ignore WordPress.Security.EscapeOutput -- fixed SVG markup
		);
	}
	echo '</ul>';
}

/**
 * Inner-page header: eyebrow, title, optional intro.
 *
 * @param string $title  Page title.
 * @param string $kicker Small eyebrow above the title.
 * @param string $intro  Optional intro line.
 */
function glitter_page_header( string $title, string $kicker = '', string $intro = '' ): void {
	?>
	<header class="page-header">
		<div class="container container--wide">
			<?php if ( $kicker ) : ?>
				<p class="eyebrow"><?php echo esc_html( $kicker ); ?></p>
			<?php endif; ?>
			<h1 class="page-title"><?php echo glitter_highlight( $title ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in glitter_highlight() ?></h1>
			<?php if ( $intro ) : ?>
				<p class="page-intro"><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>
		</div>
	</header>
	<?php
}

/**
 * Heading with a highlighted word: "Strategies that *sparkle*" → <em>sparkle</em> (pink italic).
 *
 * @param string $text Text with *asterisks* around the highlight.
 */
function glitter_highlight( string $text ): string {
	return (string) preg_replace( '/\*(.+?)\*/u', '<em>$1</em>', esc_html( $text ) );
}
