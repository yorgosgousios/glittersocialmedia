<?php
/**
 * Bilingual placeholder content (EL + EN, linked as translations). Run via `make seed`.
 * Every placeholder post has meta _glitter_placeholder=1 so it's easy to remove before launch.
 */

if ( ! function_exists( 'pll_set_post_language' ) ) {
	WP_CLI::error( 'Polylang is not active — run make install first.' );
}

require_once ABSPATH . 'wp-admin/includes/image.php';

if ( get_posts( [ 'post_type' => [ 'post', 'service', 'case_study', 'team_member', 'testimonial' ], 'meta_key' => '_glitter_placeholder', 'posts_per_page' => 1, 'fields' => 'ids', 'lang' => '' ] ) ) {
	WP_CLI::success( 'Placeholders already seeded.' );
	return;
}

$uploads = wp_upload_dir();

/** Pastel gradient placeholder with a few sparkles (GD). Returns attachment id. */
function glitter_seed_image( int $n, int $w, int $h, int $parent ): int {
	$palettes = [
		[ [ 255, 209, 230 ], [ 215, 184, 245 ] ],
		[ [ 183, 222, 250 ], [ 236, 218, 252 ] ],
		[ [ 229, 24, 106 ], [ 255, 140, 190 ] ],
		[ [ 215, 184, 245 ], [ 255, 236, 245 ] ],
	];
	[ $a, $b ] = $palettes[ $n % count( $palettes ) ];
	$img = imagecreatetruecolor( $w, $h );
	for ( $y = 0; $y < $h; $y++ ) {
		$t = $y / $h;
		$c = imagecolorallocate( $img, (int) ( $a[0] + ( $b[0] - $a[0] ) * $t ), (int) ( $a[1] + ( $b[1] - $a[1] ) * $t ), (int) ( $a[2] + ( $b[2] - $a[2] ) * $t ) );
		imageline( $img, 0, $y, $w, $y, $c );
	}
	mt_srand( $n * 31 );
	$white = imagecolorallocatealpha( $img, 255, 255, 255, 30 );
	for ( $i = 0; $i < 9; $i++ ) {
		$cx = mt_rand( 0, $w );
		$cy = mt_rand( 0, $h );
		$r  = mt_rand( 10, 60 );
		imagefilledpolygon( $img, [ $cx, $cy - $r, $cx + $r / 5, $cy - $r / 5, $cx + $r, $cy, $cx + $r / 5, $cy + $r / 5, $cx, $cy + $r, $cx - $r / 5, $cy + $r / 5, $cx - $r, $cy, $cx - $r / 5, $cy - $r / 5 ], $white );
	}
	$file = trailingslashit( wp_upload_dir()['path'] ) . "glitter-placeholder-{$n}.jpg";
	imagejpeg( $img, $file, 85 );
	imagedestroy( $img );

	$id = wp_insert_attachment( [ 'post_mime_type' => 'image/jpeg', 'post_title' => "Placeholder {$n}", 'post_status' => 'inherit' ], $file, $parent );
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
	return (int) $id;
}

/**
 * Creates the EL + EN versions of an item and links them.
 *
 * @param string $type  Post type.
 * @param array  $el    [title, content, excerpt].
 * @param array  $en    [title, content, excerpt].
 * @param array  $meta  Shared meta (key => value) or per language ['el' => [...], 'en' => [...]].
 * @param int    $order menu_order.
 * @param int    $image Image size [w, h] or 0 for none.
 */
function glitter_seed_pair( string $type, array $el, array $en, array $meta = [], int $order = 0, array $image = [] ): void {
	static $n = 0;
	$ids   = [];
	$thumb = 0;
	foreach ( [ 'el' => $el, 'en' => $en ] as $lang => [ $title, $content, $excerpt ] ) {
		$id = wp_insert_post(
			[
				'post_type'    => $type,
				'post_status'  => 'publish',
				'post_title'   => $title,
				'post_content' => $content,
				'post_excerpt' => $excerpt,
				'menu_order'   => $order,
			]
		);
		pll_set_post_language( $id, $lang );
		$lang_meta = $meta[ $lang ] ?? $meta;
		foreach ( $lang_meta as $key => $value ) {
			if ( ! is_array( $value ) ) {
				update_post_meta( $id, "_glitter_{$key}", $value );
			}
		}
		update_post_meta( $id, '_glitter_placeholder', 1 );
		if ( $image ) {
			$thumb = $thumb ?: glitter_seed_image( ++$n, $image[0], $image[1], $id );
			set_post_thumbnail( $id, $thumb );
		}
		$ids[ $lang ] = $id;
	}
	pll_save_post_translations( $ids );
}

$p = fn( string $t ) => "<!-- wp:paragraph -->\n<p>{$t}</p>\n<!-- /wp:paragraph -->";

// Services -----------------------------------------------------------------------
$services = [
	[ 'sparkle', 'Social Media Management', 'Social Media Management', 'Στρατηγική, πρόγραμμα αναρτήσεων και καθημερινή διαχείριση των λογαριασμών σου.', 'Strategy, posting calendar and day-to-day management of your accounts.', "Στρατηγική & content plan\nΔιαχείριση Instagram, TikTok, Facebook\nCommunity management\nΜηνιαίο report", "Strategy & content plan\nInstagram, TikTok, Facebook management\nCommunity management\nMonthly report" ],
	[ 'eyes', 'Content Creation', 'Content Creation', 'Φωτογραφίσεις, reels και graphics που σταματούν το scroll.', 'Photo shoots, reels and graphics that stop the scroll.', "Φωτογραφίσεις προϊόντων & lifestyle\nReels & TikToks\nGraphics & carousels", "Product & lifestyle shoots\nReels & TikToks\nGraphics & carousels" ],
	[ 'bolt', 'Στρατηγική επικοινωνίας', 'Communication Strategy', 'Tone of voice, κοινό και πλάνο για να ξέρεις τι λες, πού και γιατί.', 'Tone of voice, audience and a plan so you know what to say, where and why.', "Audit λογαριασμών\nPersonas & tone of voice\nΠλάνο 3 μηνών", "Account audit\nPersonas & tone of voice\n3-month plan" ],
	[ 'disco', 'Διαφημίσεις', 'Paid Ads', 'Meta & TikTok ads με στόχους, budget και μετρήσιμα αποτελέσματα.', 'Meta & TikTok ads with clear goals, budgets and measurable results.', "Στήσιμο καμπανιών\nA/B testing δημιουργικών\nReporting", "Campaign setup\nCreative A/B testing\nReporting" ],
	[ 'heart', 'Influencer συνεργασίες', 'Influencer Marketing', 'Βρίσκουμε τους σωστούς creators και στήνουμε συνεργασίες που φαίνονται αληθινές.', 'We find the right creators and build collaborations that feel genuine.', "Επιλογή creators\nBriefing & διαπραγμάτευση\nΜέτρηση αποτελεσμάτων", "Creator selection\nBriefing & negotiation\nResults tracking" ],
	[ 'star', 'Branding', 'Branding', 'Logo, χρώματα και visual identity που ταιριάζουν στο feed σου.', 'Logo, colours and a visual identity made for your feed.', "Logo & παλέτα\nTemplates για social\nBrand guidelines", "Logo & palette\nSocial templates\nBrand guidelines" ],
];
foreach ( $services as $i => [ , $el_title, $en_title, $el_ex, $en_ex, $el_items, $en_items ] ) {
	glitter_seed_pair(
		'service',
		[ $el_title, $p( $el_ex . ' Placeholder κείμενο για την υπηρεσία.' ), $el_ex ],
		[ $en_title, $p( $en_ex . ' Placeholder text for this service.' ), $en_ex ],
		[
			'el' => [ 'deliverables' => $el_items ],
			'en' => [ 'deliverables' => $en_items ],
		],
		$i
	);
}

// Case studies ---------------------------------------------------------------------
$work = [
	[ 'Λανσάρισμα για beauty brand', 'Launch for a beauty brand', 'Beauty brand', "+240% | reach\n12K | νέοι followers\n3.1% | engagement", "+240% | reach\n12K | new followers\n3.1% | engagement" ],
	[ 'Καλοκαιρινή καμπάνια café', 'Summer café campaign', 'Café', "1.2M | views στα reels\n+68% | κρατήσεις", "1.2M | reel views\n+68% | bookings" ],
	[ 'Rebrand για fashion boutique', 'Rebrand for a fashion boutique', 'Fashion boutique', "x3 | πωλήσεις από IG\n45 | creators", "x3 | IG sales\n45 | creators" ],
];
foreach ( $work as $i => [ $el_title, $en_title, $client, $el_res, $en_res ] ) {
	glitter_seed_pair(
		'case_study',
		[ $el_title, $p( 'Placeholder case study: η πρόκληση, η ιδέα και το αποτέλεσμα.' ), 'Placeholder περιγραφή του project.' ],
		[ $en_title, $p( 'Placeholder case study: the challenge, the idea and the result.' ), 'Placeholder project summary.' ],
		[
			'el' => [ 'client' => $client, 'platforms' => 'Instagram, TikTok', 'year' => '2026', 'results' => $el_res ],
			'en' => [ 'client' => $client, 'platforms' => 'Instagram, TikTok', 'year' => '2026', 'results' => $en_res ],
		],
		$i,
		[ 1080, 1350 ]
	);
}

// Team -----------------------------------------------------------------------------
glitter_seed_pair(
	'team_member',
	[ 'Ηλιάνα', $p( 'Founder. Είχε μια ιδέα και να που είμαστε. Placeholder bio.' ), '' ],
	[ 'Iliana', $p( 'Founder. Had an idea, so here we are. Placeholder bio.' ), '' ],
	[
		'el' => [ 'role' => 'Founder & Social Media Strategist', 'instagram' => 'https://www.instagram.com/ilianakyr/' ],
		'en' => [ 'role' => 'Founder & Social Media Strategist', 'instagram' => 'https://www.instagram.com/ilianakyr/' ],
	],
	0,
	[ 900, 1125 ]
);
glitter_seed_pair(
	'team_member',
	[ 'Μέλος ομάδας', $p( 'Placeholder bio για το επόμενο μέλος της ομάδας.' ), '' ],
	[ 'Team member', $p( 'Placeholder bio for the next team member.' ), '' ],
	[ 'role' => 'Content Creator' ],
	1,
	[ 900, 1125 ]
);

// Testimonials ---------------------------------------------------------------------
$quotes = [
	[ 'Μαρία Κ.', 'Maria K.', 'Owner, beauty brand', 'Από τότε που ξεκινήσαμε με την Glitter, το Instagram μας επιτέλους μοιάζει με εμάς.', 'Since we started with Glitter, our Instagram finally looks like us.' ],
	[ 'Νίκος Π.', 'Nikos P.', 'Café owner', 'Τα reels τους έφεραν κόσμο στο μαγαζί. Κυριολεκτικά.', 'Their reels brought people into the shop. Literally.' ],
	[ 'Ελένη Δ.', 'Eleni D.', 'Fashion boutique', 'Δημιουργικοί, γρήγοροι και πάντα με χιούμορ.', 'Creative, fast and always funny.' ],
	[ 'Γιώργος Μ.', 'George M.', 'Startup founder', 'Μας έδωσαν στρατηγική, όχι απλά posts.', 'They gave us a strategy, not just posts.' ],
];
foreach ( $quotes as $i => [ $el_name, $en_name, $company, $el_q, $en_q ] ) {
	glitter_seed_pair( 'testimonial', [ $el_name, $el_q, '' ], [ $en_name, $en_q, '' ], [ 'company' => $company ], $i );
}

// Journal posts ----------------------------------------------------------------------
$posts = [
	[ 'Τι κάνουν πραγματικά οι social media managers;', 'What do social media managers actually do?' ],
	[ 'Social media managers vs content creators', 'Social media managers vs content creators' ],
	[ '5 trends για το feed σου αυτή τη σεζόν', '5 trends for your feed this season' ],
];
foreach ( $posts as $i => [ $el_title, $en_title ] ) {
	glitter_seed_pair(
		'post',
		[ $el_title, $p( 'Placeholder άρθρο. Εδώ θα μπει το πραγματικό κείμενο.' ), 'Placeholder περίληψη άρθρου.' ],
		[ $en_title, $p( 'Placeholder post. The real text goes here.' ), 'Placeholder post summary.' ],
		[],
		$i,
		[ 1080, 1350 ]
	);
}

WP_CLI::success( 'Bilingual placeholders seeded.' );
