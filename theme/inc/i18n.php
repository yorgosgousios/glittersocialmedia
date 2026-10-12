<?php
/**
 * Bilingual support (Greek default, English at /en/) via Polylang.
 *
 * Theme UI strings are translated with the normal gettext files in /languages
 * (Polylang switches the locale per language). Content (pages, posts, CPTs) is
 * translated in Polylang. Everything here degrades gracefully without Polylang.
 */

defined( 'ABSPATH' ) || exit;

/** Our custom post types are translatable. */
add_filter(
	'pll_get_post_types',
	function ( array $types ): array {
		foreach ( [ 'service', 'case_study', 'team_member', 'testimonial' ] as $type ) {
			$types[ $type ] = $type;
		}
		return $types;
	}
);

/** Current language slug ('el' / 'en'). */
function glitter_lang(): string {
	return function_exists( 'pll_current_language' ) ? (string) pll_current_language() : substr( get_locale(), 0, 2 );
}

/**
 * Permalink of a page in the current language, found by its slug in the default language.
 * Falls back to the default-language page, then to home.
 *
 * @param string $slug Page slug in the default language (e.g. 'epikoinonia').
 */
function glitter_page_url( string $slug ): string {
	$page = get_page_by_path( $slug );
	if ( ! $page ) {
		return home_url( '/' );
	}
	$id = function_exists( 'pll_get_post' ) ? ( pll_get_post( $page->ID ) ?: $page->ID ) : $page->ID;
	return (string) get_permalink( $id );
}

/** Archive link for a post type in the current language. */
function glitter_archive_url( string $post_type ): string {
	return (string) get_post_type_archive_link( $post_type );
}

/** Home URL in the current language. */
function glitter_home_url(): string {
	return function_exists( 'pll_home_url' ) ? (string) pll_home_url() : home_url( '/' );
}

/** EL / EN switcher (links to the translation of the current page when it exists). */
function glitter_language_switcher(): void {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return;
	}
	$langs = pll_the_languages(
		[
			'raw'           => 1,
			'hide_if_empty' => 0,
		]
	);
	if ( ! $langs ) {
		return;
	}
	echo '<ul class="lang-switch" aria-label="' . esc_attr__( 'Language', 'glitter' ) . '">';
	foreach ( $langs as $lang ) {
		printf(
			'<li><a href="%s" hreflang="%s" lang="%s"%s>%s</a></li>',
			esc_url( $lang['url'] ),
			esc_attr( $lang['locale'] ),
			esc_attr( $lang['slug'] ),
			$lang['current_lang'] ? ' aria-current="true"' : '',
			esc_html( 'el' === $lang['slug'] ? 'ΕΛ' : strtoupper( $lang['slug'] ) )
		);
	}
	echo '</ul>';
}

/**
 * Greeklish slugs: «Τι κάνουν οι managers» → ti-kanoun-oi-managers, instead of
 * percent-encoded Greek in URLs. Only applies when a slug is generated on save.
 */
add_filter(
	'sanitize_title',
	function ( string $title, string $raw_title = '', string $context = 'display' ): string {
		if ( 'save' !== $context || ! preg_match( '/\p{Greek}/u', $raw_title ) ) {
			return $title;
		}
		return sanitize_title_with_dashes( glitter_greeklish( $raw_title ), '', 'save' );
	},
	9,
	3
);

/** Greek → Latin transliteration (ELOT-style, simplified for URLs). */
function glitter_greeklish( string $text ): string {
	$text = mb_strtolower( $text, 'UTF-8' );
	$text = strtr(
		$text,
		[
			'ά' => 'α',
			'έ' => 'ε',
			'ή' => 'η',
			'ί' => 'ι',
			'ό' => 'ο',
			'ύ' => 'υ',
			'ώ' => 'ω',
			'ϊ' => 'ι',
			'ΐ' => 'ι',
			'ϋ' => 'υ',
			'ΰ' => 'υ',
			'ς' => 'σ',
		]
	);
	// Digraphs first.
	$text = strtr(
		$text,
		[
			'ου' => 'ou',
			'αι' => 'ai',
			'ει' => 'ei',
			'οι' => 'oi',
			'υι' => 'yi',
			'αυ' => 'av',
			'ευ' => 'ev',
			'μπ' => 'b',
			'ντ' => 'nt',
			'γκ' => 'gk',
			'γγ' => 'ng',
			'θ'  => 'th',
			'χ'  => 'ch',
			'ψ'  => 'ps',
		]
	);
	return strtr(
		$text,
		[
			'α' => 'a',
			'β' => 'v',
			'γ' => 'g',
			'δ' => 'd',
			'ε' => 'e',
			'ζ' => 'z',
			'η' => 'i',
			'ι' => 'i',
			'κ' => 'k',
			'λ' => 'l',
			'μ' => 'm',
			'ν' => 'n',
			'ξ' => 'x',
			'ο' => 'o',
			'π' => 'p',
			'ρ' => 'r',
			'σ' => 's',
			'τ' => 't',
			'υ' => 'y',
			'φ' => 'f',
			'ω' => 'o',
		]
	);
}
