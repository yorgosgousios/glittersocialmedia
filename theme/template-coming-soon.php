<?php
/**
 * Coming soon holding page, served by inc/coming-soon.php (not a page template).
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#ecdafc">
	<meta name="robots" content="noindex, nofollow">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'coming-soon' ); ?>>
<main id="main" class="site-main">
	<section class="hero">
		<div class="hero__inner container container--wide">
			<div class="hero__text">
				<p class="eyebrow hero__eyebrow"><?php glitter_logo(); ?></p>
				<h1 class="hero__title">
					<?php echo glitter_highlight( __( 'Something *sparkly* is coming soon', 'glitter' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in glitter_highlight() ?>
				</h1>
				<div class="hero__bottom">
					<p class="hero__lead"><?php esc_html_e( 'Our new website is almost ready. Until then, say hi:', 'glitter' ); ?>
						<?php $glitter_email = glitter_contact_email(); ?>
						<a href="mailto:<?php echo esc_attr( antispambot( $glitter_email ) ); ?>"><?php echo esc_html( antispambot( $glitter_email ) ); ?></a>
					</p>
					<?php glitter_social_links(); ?>
					<p><a class="link-arrow" href="<?php echo esc_url( wp_login_url( home_url( add_query_arg( [] ) ) ) ); ?>"><?php esc_html_e( 'Log in', 'glitter' ); ?></a></p>
				</div>
			</div>
			<div class="hero__art" aria-hidden="true">
				<span class="hero__glow"></span>
				<span class="hero__string"></span>
				<?php echo glitter_sticker( 'disco', 'hero__ball' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo glitter_sticker( 'sparkle', 'hero__glint hero__glint--1' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php echo glitter_sticker( 'sparkle', 'hero__glint hero__glint--2' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>
		</div>
	</section>
</main>
<?php wp_footer(); ?>
</body>
</html>
