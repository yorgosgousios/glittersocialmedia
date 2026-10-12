<?php
/**
 * One-time site setup: Polylang languages (Greek default at /, English at /en/),
 * bilingual pages, menus per language and permalink/date settings. Idempotent.
 *
 * Used by the "Appearance → Glitter setup" admin page (production, no WP-CLI)
 * and by scripts/setup.php (local `make install`).
 */

defined( 'ABSPATH' ) || exit;

/**
 * Runs the setup.
 *
 * @param callable $log Receives progress messages (string).
 * @throws RuntimeException When Polylang is missing or a language can't be created.
 */
function glitter_site_setup( callable $log ): void {
	if ( ! function_exists( 'PLL' ) || ! function_exists( 'pll_set_post_language' ) ) {
		throw new RuntimeException( 'Polylang is not active.' );
	}

	update_option( 'timezone_string', 'Europe/Athens' );
	update_option( 'date_format', 'j F Y' );
	if ( '/%postname%/' !== get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		$log( 'Permalinks set to /%postname%/' );
	}

	$model = PLL()->model;

	// 1. Languages -----------------------------------------------------------------
	$wanted   = [
		'el' => [
			'name'       => 'Ελληνικά',
			'slug'       => 'el',
			'locale'     => 'el',
			'flag'       => 'gr',
			'term_group' => 0,
		],
		'en' => [
			'name'       => 'English',
			'slug'       => 'en',
			'locale'     => 'en_US',
			'flag'       => 'us',
			'term_group' => 1,
		],
	];
	$existing = wp_list_pluck( $model->get_languages_list(), 'slug' );
	foreach ( $wanted as $slug => $args ) {
		if ( ! in_array( $slug, $existing, true ) ) {
			$result = $model->languages->add( $args + [ 'rtl' => false ] );
			if ( is_wp_error( $result ) ) {
				throw new RuntimeException( $result->get_error_message() ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- escaped where displayed
			}
			$log( "Added language {$slug}" );
		}
	}
	$model->clean_languages_cache();

	$options = PLL()->options;
	$options->set( 'default_lang', 'el' );
	$options->set( 'hide_default', true );  // Greek at /, English at /en/
	$options->set( 'force_lang', 1 );       // language from the URL directory
	$options->set( 'rewrite', true );       // /en/ not /language/en/
	$options->set( 'browser', false );      // don't auto-redirect by browser language
	$options->set( 'redirect_lang', true );  // translated front page lives at /en/, not /en/home/

	// 2. Pages (Greek + English translations) -------------------------------------
	// Finds or creates a page in a language.
	$setup_page = function ( string $slug, string $title, string $lang, string $content = '', string $excerpt = '', string $template = '' ) use ( $log ): int {
		$page = get_page_by_path( $slug );
		if ( $page && 'publish' !== $page->post_status ) {
			// e.g. WordPress's own draft "privacy-policy" page: publish it with our content.
			wp_update_post(
				[
					'ID'           => $page->ID,
					'post_status'  => 'publish',
					'post_title'   => $title,
					'post_content' => $content,
					'post_excerpt' => $excerpt,
				]
			);
			$log( "Published existing page /{$slug}/" );
		}
		if ( $page ) {
			$id = $page->ID;
		} else {
			$id = wp_insert_post(
				[
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $title,
					'post_name'    => $slug,
					'post_content' => $content,
					'post_excerpt' => $excerpt,
				]
			);
			$log( "Created page /{$slug}/" );
		}
		pll_set_post_language( $id, $lang );
		if ( $template ) {
			update_post_meta( $id, '_wp_page_template', $template );
		}
		return (int) $id;
	};

	$p = fn( string $text ) => "<!-- wp:paragraph -->\n<p>{$text}</p>\n<!-- /wp:paragraph -->";

	$pages = [
		'home'    => [
			'el' => [ 'archiki', 'Αρχική', $p( 'Είμαστε η Glitter, ένα social media agency που κάνει τα brands να λάμπουν online. Στρατηγική, περιεχόμενο και community, με πολλή φαντασία και ακόμα περισσότερο glitter.' ) . "\n\n" . $p( 'Η @ilianakyr είχε μια ιδέα και να που είμαστε. Placeholder κείμενο — αλλάζει από τη σελίδα Αρχική.' ), 'Social media & communication strategies που λάμπουν.' ],
			'en' => [ 'home', 'Home', $p( 'We’re Glitter, a social media agency that makes brands shine online. Strategy, content and community — with lots of imagination and even more glitter.' ) . "\n\n" . $p( '@ilianakyr had an idea, so here we are. Placeholder text — edit it on the Home page.' ), 'Social media & communication strategies that sparkle.' ],
		],
		'about'   => [
			'el' => [ 'sxetika', 'Ποιοι είμαστε', $p( 'Placeholder: η ιστορία της Glitter, οι αξίες μας και ο τρόπος που δουλεύουμε.' ), 'Μια μικρή ομάδα με μεγάλες ιδέες.', 'template-about.php' ],
			'en' => [ 'about', 'About us', $p( 'Placeholder: the Glitter story, our values and how we work.' ), 'A small team with big ideas.', 'template-about.php' ],
		],
		'contact' => [
			'el' => [ 'epikoinonia', 'Ας μιλήσουμε', $p( 'Πες μας για το brand σου και τους στόχους σου. Απαντάμε σε κάθε μήνυμα μέσα σε δύο εργάσιμες.' ), '', 'template-contact.php' ],
			'en' => [ 'contact', 'Let’s talk', $p( 'Tell us about your brand and your goals. We reply to every message within two working days.' ), '', 'template-contact.php' ],
		],
		'journal' => [
			'el' => [ 'nea', 'Journal', '' ],
			'en' => [ 'journal', 'Journal', '' ],
		],
		'privacy' => [
			'el' => [ 'politiki-aporritou', 'Πολιτική απορρήτου', $p( 'Placeholder πολιτική απορρήτου. Θα γραφτεί πριν το launch.' ) ],
			'en' => [ 'privacy-policy', 'Privacy policy', $p( 'Placeholder privacy policy. To be written before launch.' ) ],
		],
	];

	$ids = [];
	foreach ( $pages as $key => $langs ) {
		foreach ( $langs as $lang => $def ) {
			$ids[ $key ][ $lang ] = $setup_page( $def[0], $def[1], $lang, $def[2] ?? '', $def[3] ?? '', $def[4] ?? '' );
		}
		pll_save_post_translations( $ids[ $key ] );
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $ids['home']['el'] );
	update_option( 'page_for_posts', $ids['journal']['el'] );
	update_option( 'wp_page_for_privacy_policy', $ids['privacy']['el'] );
	$model->clean_languages_cache(); // Polylang caches each language's front/posts page

	// 3. Menus per language --------------------------------------------------------
	// Finds or creates a menu with the given items.
	$setup_menu = function ( string $name, array $items ) use ( $log ): int {
		$menu = wp_get_nav_menu_object( $name );
		if ( $menu ) {
			return (int) $menu->term_id;
		}
		$menu_id = wp_create_nav_menu( $name );
		foreach ( $items as $i => $item ) {
			$args = [
				'menu-item-status'   => 'publish',
				'menu-item-position' => $i + 1,
				'menu-item-title'    => $item['title'],
			];
			if ( isset( $item['page'] ) ) {
				$args += [
					'menu-item-type'      => 'post_type',
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $item['page'],
				];
			} else {
				$args += [
					'menu-item-type'   => 'post_type_archive',
					'menu-item-object' => $item['archive'],
				];
			}
			wp_update_nav_menu_item( $menu_id, 0, $args );
		}
		$log( "Created menu {$name}" );
		return (int) $menu_id;
	};

	$menus = [
		'el' => [
			'primary' => $setup_menu(
				'Κύριο μενού (EL)',
				[
					[
						'title'   => 'Υπηρεσίες',
						'archive' => 'service',
					],
					[
						'title'   => 'Δουλειές',
						'archive' => 'case_study',
					],
					[
						'title' => 'Ποιοι είμαστε',
						'page'  => $ids['about']['el'],
					],
					[
						'title' => 'Journal',
						'page'  => $ids['journal']['el'],
					],
					[
						'title' => 'Επικοινωνία',
						'page'  => $ids['contact']['el'],
					],
				]
			),
			'footer'  => $setup_menu(
				'Footer (EL)',
				[
					[
						'title' => 'Πολιτική απορρήτου',
						'page'  => $ids['privacy']['el'],
					],
				]
			),
		],
		'en' => [
			'primary' => $setup_menu(
				'Main menu (EN)',
				[
					[
						'title'   => 'Services',
						'archive' => 'service',
					],
					[
						'title'   => 'Work',
						'archive' => 'case_study',
					],
					[
						'title' => 'About',
						'page'  => $ids['about']['en'],
					],
					[
						'title' => 'Journal',
						'page'  => $ids['journal']['en'],
					],
					[
						'title' => 'Contact',
						'page'  => $ids['contact']['en'],
					],
				]
			),
			'footer'  => $setup_menu(
				'Footer (EN)',
				[
					[
						'title' => 'Privacy policy',
						'page'  => $ids['privacy']['en'],
					],
				]
			),
		],
	];

	$theme     = get_stylesheet();
	$nav_menus = $options->get( 'nav_menus' );
	foreach ( [ 'primary', 'footer' ] as $location ) {
		foreach ( [ 'el', 'en' ] as $lang ) {
			$nav_menus[ $theme ][ $location ][ $lang ] = $menus[ $lang ][ $location ];
		}
	}
	$options->set( 'nav_menus', $nav_menus );
	set_theme_mod(
		'nav_menu_locations',
		[
			'primary' => $menus['el']['primary'],
			'footer'  => $menus['el']['footer'],
		]
	);

	if ( method_exists( $options, 'save' ) ) {
		$options->save();
	}

	// Default install content: move to trash (restorable), only if never edited.
	foreach ( [
		'sample-page' => 'page',
		'hello-world' => 'post',
	] as $slug => $type ) {
		$default = get_page_by_path( $slug, OBJECT, $type );
		if ( $default && 'trash' !== $default->post_status && $default->post_modified === $default->post_date ) {
			wp_trash_post( $default->ID );
			$log( "Moved default {$type} /{$slug}/ to trash" );
		}
	}

	// Hard flush writes .htaccess; its helper isn't loaded outside wp-admin screens (e.g. admin-post.php).
	if ( ! function_exists( 'save_mod_rewrite_rules' ) ) {
		require_once ABSPATH . 'wp-admin/includes/misc.php';
	}
	flush_rewrite_rules( true );
	$log( 'Languages, pages and menus are set up.' );
}
